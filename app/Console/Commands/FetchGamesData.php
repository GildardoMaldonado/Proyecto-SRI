<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

class FetchGamesData extends Command
{
    protected $signature = 'fetch:games';
    protected $description = 'Descarga datos de videojuegos reales y genera un JSON local';

    public function handle()
    {
        $this->info('Conectando a la API de FreeToGame...');

        // Llamada a una API de videojuegos reales, sin necesidad de autenticación
        $response = Http::get('https://www.freetogame.com/api/games');

        if ($response->successful()) {
            // La API devuelve cientos de juegos. Tomamos los primeros 100 para un buen catálogo.
            $apiData = array_slice($response->json(), 0, 100);
            $juegosFormateados = [];

            foreach ($apiData as $item) {
                
                // Normalizamos el nombre de la plataforma. 
                // Si es un juego de PC, lo marcamos explícitamente como "PC" para que 
                // tu regla de Boosting de alto rendimiento funcione a la perfección.
                $plataforma = str_contains($item['platform'], 'PC') ? 'PC' : $item['platform'];

                // Construimos la estructura exacta que espera tu base de datos
                $juegosFormateados[] = [
                    'name' => $item['title'],
                    'background_image' => $item['thumbnail'],
                    'released' => $item['release_date'],
                    'platforms' => [
                        ['platform' => ['name' => $plataforma]]
                    ],
                    // La API nos da un solo género principal, lo metemos en el arreglo
                    'genres' => [
                        ['name' => $item['genre']]
                    ]
                ];
            }

            // Convertimos a JSON y lo guardamos
            $jsonFinal = json_encode(['results' => $juegosFormateados], JSON_PRETTY_PRINT);
            File::put(storage_path('app/juegos.json'), $jsonFinal);

            $this->info('¡Éxito! 100 videojuegos reales descargados en storage/app/juegos.json');
        } else {
            $this->error('Hubo un problema al conectar con la API de juegos.');
        }
    }
}
