<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Game;
use App\Models\Interaction;
use Illuminate\Support\Facades\Hash;

class InteractionSeeder extends Seeder
{
    public function run(): void
    {
        // Extraemos todos los IDs de los juegos en tu catálogo
        $juegos = Game::pluck('id')->toArray();

        // Verificamos que al menos haya juegos en la BD
        if (count($juegos) < 3) {
            $this->command->error('No hay suficientes juegos. Ejecuta primero la descarga de la API.');
            return;
        }

        $this->command->info('Generando 50 usuarios y poblando la matriz de interacciones...');

        for ($i = 1; $i <= 50; $i++) {
            
            // 1. Generar usuario ficticio
            $user = User::create([
                'name' => 'Usuario Simulado ' . $i,
                'email' => 'user' . $i . '@test.com',
                'password' => Hash::make('password123'),
                'is_admin' => false,
            ]);

            // Revolvemos el arreglo de juegos para que cada usuario tenga gustos distintos
            shuffle($juegos);

            // ========================================================
            // 2. REGLA ESTRICTA: Romper el Inicio en Frío (Cold Start)
            // ========================================================
            for ($j = 0; $j < 3; $j++) {
                Interaction::create([
                    'user_id' => $user->id,
                    'game_id' => $juegos[$j],
                    'rating' => 5 // Aseguramos el vector inicial
                ]);
            }

            // ========================================================
            // 3. REGLA DE DISPERSIÓN: Darle realismo al sistema
            // ========================================================
            // Hacemos que cada usuario califique entre 5 y 15 juegos extra 
            // con puntajes aleatorios (del 1 al 4)
            $juegosExtra = rand(5, 15);
            for ($k = 3; $k < (3 + $juegosExtra); $k++) {
                Interaction::create([
                    'user_id' => $user->id,
                    'game_id' => $juegos[$k],
                    'rating' => rand(1, 4) 
                ]);
            }
        }

        $this->command->info('¡Matriz poblada exitosamente! Tu dashboard ahora tiene datos reales que analizar.');
    }
}
