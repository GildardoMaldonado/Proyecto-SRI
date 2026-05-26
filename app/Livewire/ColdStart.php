<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Game;
use App\Models\Interaction;
use Illuminate\Support\Facades\Auth;

class ColdStart extends Component
{
    public $games; // Aquí guardaremos los juegos a mostrar
    public $selectedGames = []; // Arreglo para guardar los IDs que el usuario seleccione

    public function mount()
    {
        // Traemos 12 juegos al azar para que el usuario elija
        // (En el futuro puedes cambiar inRandomOrder() por los más populares)
        $this->games = Game::inRandomOrder()->take(12)->get();
    }

    // Esta función se activará cuando el usuario haga clic en un juego
    public function toggleGame($gameId)
    {
        if (in_array($gameId, $this->selectedGames)) {
            // Si ya lo había seleccionado, lo quitamos de la lista
            $this->selectedGames = array_diff($this->selectedGames, [$gameId]);
        } else {
            // Si no estaba, lo agregamos
            $this->selectedGames[] = $gameId;
        }
    }

    // Esta función se ejecuta al darle clic al botón "Guardar mis gustos"
    public function savePreferences()
    {
        $userId = Auth::id(); // Obtenemos el usuario que inició sesión

        // Guardamos cada juego seleccionado en la tabla de interacciones
        foreach ($this->selectedGames as $gameId) {
            Interaction::create([
                'user_id' => $userId,
                'game_id' => $gameId,
                'is_favorite' => true,
                'rating' => 5 // Le damos calificación máxima inicial para el algoritmo
            ]);
        }

        // Una vez guardado, lo redirigimos a la página principal del recomendador
        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.cold-start');
    }
}
