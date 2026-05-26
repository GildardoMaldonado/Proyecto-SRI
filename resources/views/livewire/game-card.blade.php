<div class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-lg transition-transform transform hover:scale-105 border border-gray-200 dark:border-gray-700 relative flex flex-col h-full">
            
    @if($index !== null)
        <div class="absolute top-2 left-2 bg-indigo-600 text-white font-bold w-8 h-8 rounded-full flex items-center justify-center z-10 shadow-md">
            #{{ $index + 1 }}
        </div>
    @endif

    <img 
        src="{{ $game->cover_url ?: 'https://via.placeholder.com/300x400?text=Sin+Portada' }}" 
        alt="{{ $game->title }}" 
        class="w-full h-64 object-cover"
    >
    
    <div class="p-4 flex-1 flex flex-col justify-between bg-white dark:bg-gray-800">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white line-clamp-1" title="{{ $game->title }}">
                {{ $game->title }}
            </h3>
            
            <ul class="mt-3 text-xs font-medium text-indigo-600 dark:text-indigo-400 leading-snug list-disc list-inside space-y-1">
                @if(is_array($game->explanation) && count($game->explanation) > 0)
                    @foreach($game->explanation as $reason)
                        <li>{{ $reason }}</li>
                    @endforeach
                @else
                    <li>Recomendado para ti</li>
                @endif
            </ul>
        </div>
        
        <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700">
            <div class="flex flex-wrap items-center gap-1 mb-4">
                <span class="bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300 text-[10px] font-bold px-2 py-1 rounded flex items-center gap-1">
                    🕒 {{ $game->release_year }}
                </span>

                @foreach($game->genres->take(2) as $genre)
                    <span class="bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 text-[10px] font-bold px-2 py-1 rounded">
                        {{ $genre->name }}
                    </span>
                @endforeach
                
                @if($game->is_high_performance)
                    <span class="bg-red-100 text-red-800 text-[10px] font-bold px-2 py-1 rounded">PC</span>
                @endif
            </div>

            <div class="flex items-center justify-center space-x-1 mt-2">
                @for($i = 1; $i <= 5; $i++)
                    <button 
                        wire:click="rateGame({{ $i }})"
                        class="focus:outline-none transition-transform hover:scale-125"
                    >
                        <svg class="w-6 h-6 transition-colors duration-200 {{ $i <= $currentRating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600 hover:text-yellow-200' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    </button>
                @endfor
            </div>
            
            @if($currentRating > 0)
                <div class="text-center text-[10px] text-green-500 mt-2 font-semibold">
                    ¡Calificado con {{ $currentRating }} estrellas!
                </div>
            @endif
        </div>
    </div>
</div>
