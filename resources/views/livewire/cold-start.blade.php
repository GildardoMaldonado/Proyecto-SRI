<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="text-center mb-10">
        <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white tracking-tight">
            ¡Construye tu perfil de jugador!
        </h1>
        <p class="mt-4 text-lg text-gray-500 dark:text-gray-400">
            Para que nuestro motor híbrido comience a recomendarte joyas ocultas, selecciona al menos 3 juegos que hayas disfrutado.
        </p>
        
        <div class="mt-4 inline-block px-4 py-2 rounded-full font-semibold text-sm transition-colors duration-300
            {{ count($selectedGames) >= 3 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200' }}">
            Juegos seleccionados: {{ count($selectedGames) }} / 3 mínimo
        </div>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6">
        @foreach($games as $game)
            <div 
                wire:click="toggleGame({{ $game->id }})"
                class="relative cursor-pointer rounded-xl overflow-hidden transition-all duration-200 transform hover:-translate-y-2 shadow-md
                {{ in_array($game->id, $selectedGames) ? 'ring-4 ring-indigo-500 shadow-indigo-500/50' : 'hover:shadow-xl hover:ring-2 hover:ring-gray-300' }}"
            >
                @if(in_array($game->id, $selectedGames))
                    <div class="absolute inset-0 bg-indigo-600 bg-opacity-40 z-10 flex items-center justify-center">
                        <svg class="w-16 h-16 text-white drop-shadow-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                @endif

                <img 
                    src="{{ $game->cover_url ?: 'https://via.placeholder.com/300x400?text=Sin+Portada' }}" 
                    alt="{{ $game->title }}" 
                    class="w-full h-72 object-cover"
                >
                
                <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-gray-900 to-transparent p-4 pt-12 z-0">
                    <h3 class="text-white font-bold text-sm leading-tight line-clamp-2">
                        {{ $game->title }}
                    </h3>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-12 flex justify-center">
        <button 
            wire:click="savePreferences"
            @if(count($selectedGames) < 3) disabled @endif
            class="px-8 py-4 rounded-full font-bold text-white transition-all duration-300 
            {{ count($selectedGames) >= 3 
                ? 'bg-indigo-600 hover:bg-indigo-700 cursor-pointer shadow-lg hover:shadow-indigo-500/50 transform hover:scale-105' 
                : 'bg-gray-400 cursor-not-allowed opacity-70' }}"
        >
            @if(count($selectedGames) < 3)
                Selecciona {{ 3 - count($selectedGames) }} más para continuar...
            @else
                ¡Generar mis recomendaciones!
            @endif
        </button>
    </div>
</div>