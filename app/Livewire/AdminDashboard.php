<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Game;
use App\Models\Interaction;
use Illuminate\Support\Facades\Auth;

class AdminDashboard extends Component
{
    public function render()
    {
        // 1. Capa de Seguridad: Si no es admin, lo expulsamos con un error 403
        abort_if(!Auth::user()->is_admin, 403, 'Acceso Denegado. Solo administradores.');

        // 2. Cálculos de KPIs Básicos
        $totalUsers = User::count();
        $totalGames = Game::count();
        $totalInteractions = Interaction::count();

        // 3. Cálculo de Dispersión de Datos (Sparsity)
        $possibleInteractions = $totalUsers * $totalGames;
        $sparsity = 100; // Por defecto es 100% disperso (vacío)
        
        if ($possibleInteractions > 0) {
            $sparsity = round(100 - (($totalInteractions / $possibleInteractions) * 100), 2);
        }

        // 4. Datos para la Gráfica: Top 5 juegos más interactuados
        $topGames = Game::withCount('interactions')
            ->having('interactions_count', '>', 0)
            ->orderByDesc('interactions_count')
            ->take(5)
            ->get();

        return view('livewire.admin-dashboard', [
            'totalUsers' => $totalUsers,
            'totalGames' => $totalGames,
            'totalInteractions' => $totalInteractions,
            'sparsity' => $sparsity,
            'topGames' => $topGames
        ])->layout('layouts.app');
    }
}
