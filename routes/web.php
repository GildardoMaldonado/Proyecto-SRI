<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\ColdStart;
use App\Services\RecommendationService; // <-- Importamos tu nuevo servicio
use App\Livewire\GameCatalog;
use App\Livewire\GameHistory;
use App\Livewire\AdminDashboard;

Route::get('/admin', AdminDashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('admin');

Route::view('/', 'welcome');

Route::get('/onboarding', ColdStart::class)
    ->middleware(['auth'])
    ->name('onboarding');

// Inyectamos el RecommendationService directamente en la función
Route::get('/dashboard', function (RecommendationService $service) {

    $user = auth()->user();

    // 1. El guardia: Verificamos si tiene menos de 3 juegos
    if ($user->interactions()->count() < 3) {
        return redirect()->route('onboarding');
    }

    // 2. La magia: Calculamos las recomendaciones híbridas
    $recommendedGames = $service->getHybridRecommendations($user);

    // 3. Enviamos los datos a la vista
    return view('dashboard', [
        'recommendedGames' => $recommendedGames
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

// Ruta para el catálogo general
Route::get('/explorar', GameCatalog::class)
    ->middleware(['auth', 'verified'])
    ->name('explorar');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/historial', GameHistory::class)
    ->middleware(['auth', 'verified'])
    ->name('historial');

require __DIR__ . '/auth.php';
require __DIR__ . '/auth.php';
