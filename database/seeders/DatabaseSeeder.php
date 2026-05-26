<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Game;
use App\Models\Genre;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Leer el archivo JSON que acabamos de descargar
        $rutaArchivo = storage_path('app/juegos.json');
        
        if (!File::exists($rutaArchivo)) {
            $this->command->error('El archivo juegos.json no existe. Ejecuta primero: php artisan fetch:games');
            return;
        }

        $json = File::get($rutaArchivo);
        $gamesData = json_decode($json, true)['results'];

        // 2. Iterar y guardar los juegos en la Base de Datos
        foreach ($gamesData as $apiGame) {
            
            // Extraer el año de lanzamiento de forma segura
            $releaseYear = !empty($apiGame['released']) ? substr($apiGame['released'], 0, 4) : 2026;
            
            // Verificar si está en PC para activar tu bandera de Boosting
            $platforms = array_column($apiGame['platforms'] ?? [], 'platform');
            $platformNames = array_column($platforms, 'name');
            $isHighPerformance = in_array('PC', $platformNames);

            $game = Game::create([
                'title' => $apiGame['name'],
                'cover_url' => $apiGame['background_image'] ?? null,
                'release_year' => $releaseYear,
                'is_high_performance' => $isHighPerformance
            ]);

            // 3. Crear los géneros y relacionarlos
            foreach ($apiGame['genres'] ?? [] as $apiGenre) {
                $genre = Genre::firstOrCreate(['name' => $apiGenre['name']]);
                $game->genres()->attach($genre->id); 
            }
        }
    }
}
