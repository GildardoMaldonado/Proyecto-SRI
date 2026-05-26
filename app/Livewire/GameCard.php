<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Interaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class GameCard extends Component
{
    public $game;
    public $index; // Para guardar la posición del ranking (#1, #2, etc.)
    public $currentRating = 0;

    public function mount($game, $index = null)
    {
        $this->game = $game;
        $this->index = $index;

        // Al cargar la tarjeta, verificamos si el usuario ya había calificado este juego antes
        $interaction = Interaction::where('user_id', Auth::id())
                                  ->where('game_id', $this->game->id)
                                  ->first();
        
        if ($interaction) {
            $this->currentRating = $interaction->rating ?? 0;
        }
    }

    public function rateGame($rating)
    {
        $this->currentRating = $rating;

        // updateOrCreate buscará si ya existe la interacción. Si sí, la actualiza; si no, la crea.
        Interaction::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'game_id' => $this->game->id,
            ],
            [
                'rating' => $rating,
                // Regla de negocio extra: Si le da 4 o 5 estrellas, lo marcamos como favorito automático
                'is_favorite' => $rating >= 4 ? true : false, 
            ]
        );
        Cache::forget('user_recommendations_' . Auth::id());
    }

    public function render()
    {
        return view('livewire.game-card');
    }
}
