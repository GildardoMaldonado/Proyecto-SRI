<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Interaction;
use Illuminate\Support\Facades\Cache;

class GameHistory extends Component
{
    use WithPagination;

    // Función para borrar una calificación de la base de datos
    public function deleteInteraction($interactionId)
    {
        Interaction::where('id', $interactionId)
            ->where('user_id', Auth::id())
            ->delete();

        Cache::forget('user_recommendations_' . Auth::id());
    }

    public function render()
    {
        // Traemos el historial del usuario, incluyendo los datos del juego asociado
        $interactions = Interaction::with('game')
            ->where('user_id', Auth::id())
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return view('livewire.game-history', [
            'interactions' => $interactions
        ])->layout('layouts.app'); // Inyectamos el layout maestro con la barra de navegación
    }
}
