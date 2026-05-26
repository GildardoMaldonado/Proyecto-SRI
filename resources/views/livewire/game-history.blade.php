<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Mi Historial de Calificaciones') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if($interactions->isEmpty())
                <div class="text-center py-12 text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Aún no has calificado ningún juego. ¡Ve al catálogo a explorar!
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg border border-gray-200 dark:border-gray-700">
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($interactions as $interaction)
                            <li class="p-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <div class="flex items-center space-x-4">
                                    <img 
                                        src="{{ $interaction->game->cover_url ?: 'https://via.placeholder.com/150' }}" 
                                        alt="{{ $interaction->game->title }}" 
                                        class="w-16 h-16 object-cover rounded shadow-sm"
                                    >
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $interaction->game->title }}</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                            Calificaste con: <span class="text-yellow-500 font-bold ml-1">{{ $interaction->rating ?? '5 (Favorito)' }} ⭐</span>
                                        </p>
                                    </div>
                                </div>
                                
                                <button 
                                    wire:click="deleteInteraction({{ $interaction->id }})"
                                    class="text-red-500 hover:text-red-700 focus:outline-none transition-transform hover:scale-125 bg-red-100 dark:bg-red-900/30 p-2 rounded-full"
                                    title="Eliminar calificación"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
                
                <div class="mt-6">
                    {{ $interactions->links() }}
                </div>
            @endif

        </div>
    </div>
</div>
