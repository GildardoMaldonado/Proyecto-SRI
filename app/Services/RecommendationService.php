<?php

namespace App\Services;

use App\Models\User;
use App\Models\Game;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class RecommendationService
{
    /**
     * Función principal que el Dashboard llamará para obtener las recomendaciones
     */
    public function getHybridRecommendations(User $user)
    {
        $cacheKey = 'user_recommendations_' . $user->id;

        return Cache::remember($cacheKey, now()->addHours(24), function () use ($user) {
            
            $contentRecommendations = $this->getContentBasedRecommendations($user);
            $collaborativeRecommendations = $this->getCollaborativeRecommendations($user);
            
            // Extraemos los nombres de los géneros favoritos del usuario
            $favoriteGenreNames = $user->interactions()
                ->where('is_favorite', true)
                ->with('game.genres')
                ->get()
                ->pluck('game.genres')
                ->flatten()
                ->pluck('name')
                ->unique()
                ->toArray();
            
            // Pasamos esa lista a nuestra función final
            return $this->applyBoostingAndRanking($contentRecommendations, $collaborativeRecommendations, $favoriteGenreNames);
            
        });
    }

    private function getContentBasedRecommendations(User $user)
    {
        // 1. Obtener los IDs de los juegos que el usuario seleccionó en el "Inicio en frío"
        $likedGameIds = $user->interactions()
            ->where('is_favorite', true)
            ->pluck('game_id');

        // Si por alguna razón no tiene juegos, regresamos una colección vacía
        if ($likedGameIds->isEmpty()) {
            return collect();
        }

        // 2. Extraer todos los IDs de los géneros que pertenecen a esos juegos favoritos
        // Usamos la tabla pivote directamente para mayor velocidad
        $favoriteGenreIds = DB::table('game_genre')
            ->whereIn('game_id', $likedGameIds)
            ->pluck('genre_id')
            ->unique();

        // 3. Buscar juegos que NO haya jugado, pero que tengan esos géneros.
        // Además, usamos 'withCount' para contar cuántos géneros coinciden y usarlo como puntuación.
        $recommendedGames = Game::whereNotIn('id', $likedGameIds)
            ->withCount(['genres' => function ($query) use ($favoriteGenreIds) {
                $query->whereIn('genres.id', $favoriteGenreIds);
            }])
            ->having('genres_count', '>', 0) // Que al menos comparta 1 género
            ->orderByDesc('genres_count')    // Ordenar de mayor coincidencia a menor
            ->take(15)                       // Tomamos los mejores 15 candidatos
            ->get();

        return $recommendedGames;
    }

    private function getCollaborativeRecommendations(User $user)
    {
        // 1. Obtener las calificaciones del usuario actual (Nuestro Vector A)
        // Transformamos esto en un arreglo clave-valor: [game_id => rating]
        $currentUserInteractions = $user->interactions()->pluck('rating', 'game_id');

        if ($currentUserInteractions->isEmpty()) {
            return collect(); // Si no hay datos, cortamos la ejecución
        }

        // 2. Optimización: Buscar SOLO usuarios que hayan calificado juegos en común
        $otherUsers = User::whereHas('interactions', function ($query) use ($currentUserInteractions) {
            $query->whereIn('game_id', $currentUserInteractions->keys());
        })
            ->where('id', '!=', $user->id)
            ->with('interactions') // Eager loading para no saturar la base de datos
            ->get();

        $similarities = [];

        // 3. Calcular la Similitud del Coseno
        foreach ($otherUsers as $otherUser) {
            $otherUserInteractions = $otherUser->interactions->pluck('rating', 'game_id');

            $dotProduct = 0;
            $normA = 0;
            $normB = 0;

            // Calculamos el Producto Punto y la Norma del usuario actual
            foreach ($currentUserInteractions as $gameId => $ratingA) {
                // Asumimos un rating de 5 para los favoritos del "Inicio en Frío" si el rating es nulo
                $valA = $ratingA ?? 5;
                $normA += pow($valA, 2);

                if ($otherUserInteractions->has($gameId)) {
                    $valB = $otherUserInteractions[$gameId] ?? 5;
                    $dotProduct += ($valA * $valB);
                }
            }

            // Calculamos la Norma del otro usuario
            foreach ($otherUserInteractions as $ratingB) {
                $valB = $ratingB ?? 5;
                $normB += pow($valB, 2);
            }

            // Evitamos divisiones por cero
            if ($normA == 0 || $normB == 0) continue;

            // Aplicamos la fórmula matemática
            $similarity = $dotProduct / (sqrt($normA) * sqrt($normB));

            // Solo nos interesan usuarios con gustos positivamente similares (umbral > 0.3)
            if ($similarity > 0.3) {
                $similarities[$otherUser->id] = $similarity;
            }
        }

        // 4. Seleccionar a nuestras "Almas Gemelas"
        // Ordenamos el arreglo de mayor a menor similitud y tomamos el Top 5
        arsort($similarities);
        $topSimilarUserIds = array_keys(array_slice($similarities, 0, 5, true));

        if (empty($topSimilarUserIds)) {
            return collect();
        }

        // 5. Extraer los juegos recomendados
        // Buscamos juegos que a nuestros "vecinos" les encantaron, pero que nosotros NO hemos jugado
        $recommendedGames = Game::whereHas('interactions', function ($query) use ($topSimilarUserIds) {
            $query->whereIn('user_id', $topSimilarUserIds)
                ->where(function ($q) {
                    $q->where('rating', '>=', 4)
                        ->orWhere('is_favorite', true); // Tolerancia para favoritos sin rating
                });
        })
            ->whereNotIn('id', $currentUserInteractions->keys())
            ->take(15)
            ->get();

        return $recommendedGames;
    }

    // Nota que agregamos $favoriteGenres como tercer parámetro
    private function applyBoostingAndRanking($content, $collab, $favoriteGenres = [])
    {
        $mergedGames = $content->merge($collab)->unique('id');

        $rankedGames = $mergedGames->map(function ($game) use ($favoriteGenres) {
            $score = 0;
            $reasons = []; // Ahora esto será un arreglo de viñetas

            // 1. Pilar de Contenido (Mencionando el género exacto)
            if (isset($game->genres_count) && $game->genres_count > 0) {
                $score += ($game->genres_count * 2);
                
                // Buscamos qué géneros exactos del juego hacen match con los favoritos del usuario
                $gameGenreNames = $game->genres->pluck('name')->toArray();
                $matchedGenres = array_intersect($gameGenreNames, $favoriteGenres);
                
                if (!empty($matchedGenres)) {
                    $reasons[] = "Coincide con tu gusto por: " . implode(', ', $matchedGenres);
                }
            }

            // 2. Pilar Colaborativo
            if ($game->interactions()->count() > 0) {
                $score += 3; 
                $reasons[] = "Jugadores similares a ti le dieron una alta calificación";
            }


            // 4. Boosting por Novedad (Mantenemos los puntos, pero ELIMINAMOS el texto)
            $currentYear = date('Y');
            $age = $currentYear - $game->release_year;
            if ($age <= 2) {
                $score += 2;
            } elseif ($age > 10) {
                $score -= 1; 
            }

            $game->final_score = $score;
            $game->explanation = $reasons; // Guardamos el arreglo completo
            
            return $game;
        });

        return $rankedGames->sortByDesc('final_score')->values()->take(10);
    }
}
