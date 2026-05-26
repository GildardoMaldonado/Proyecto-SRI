<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Game;

class GameCatalog extends Component
{
    use WithPagination;

    public $search = ''; // Almacena el texto del buscador

    // Si el usuario escribe algo, reseteamos la paginación a la página 1
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Buscamos juegos que coincidan con el texto, y traemos de 15 en 15
        $games = Game::where('title', 'like', '%' . $this->search . '%')
            ->orderBy('title', 'asc')
            ->paginate(15);

        // Retornamos la vista inyectando un layout de página completa
        return view('livewire.game-catalog', [
            'games' => $games
        ])->layout('layouts.app');
    }
}
