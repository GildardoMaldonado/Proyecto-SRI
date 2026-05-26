<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tus Recomendaciones') }}
        </h2>
    </x-slot>

    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        /* Clase para el cursor mientras se arrastra */
        .grabbing {
            cursor: grabbing !important;
        }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 relative" x-data="{
            isDown: false,
            startX: 0,
            scrollLeftState: 0,
            
            scrollLeft() {
                this.$refs.slider.scrollBy({ left: -320, behavior: 'smooth' });
            },
            
            scrollRight() {
                this.$refs.slider.scrollBy({ left: 320, behavior: 'smooth' });
            },
            
            startDrag(e) {
                this.isDown = true;
                this.startX = e.pageX - this.$refs.slider.offsetLeft;
                this.scrollLeftState = this.$refs.slider.scrollLeft;
            },
            
            stopDrag() {
                this.isDown = false;
            },
            
            drag(e) {
                if (!this.isDown) return;
                e.preventDefault();
                const x = e.pageX - this.$refs.slider.offsetLeft;
                const walk = (x - this.startX) * 1.5;
                this.$refs.slider.scrollLeft = this.scrollLeftState - walk;
            }
        }">
            
            @if($recommendedGames->isEmpty())
                <div class="text-center py-12 text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Aún no tenemos recomendaciones suficientes para ti. ¡Califica más juegos en el catálogo!
                </div>
            @else
                
                <button 
                    @click="scrollLeft()" 
                    class="absolute left-2 sm:-left-4 top-1/2 transform -translate-y-1/2 z-20 bg-white dark:bg-gray-800 rounded-full p-2 shadow-lg border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none transition"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </button>

                <div 
                    x-ref="slider"
                    @mousedown="startDrag"
                    @mouseleave="stopDrag"
                    @mouseup="stopDrag"
                    @mousemove="drag"
                    class="flex overflow-x-auto gap-6 pb-8 pt-4 px-4 no-scrollbar snap-x snap-mandatory cursor-grab"
                    :class="{ 'grabbing snap-none': isDown }"
                >
                    @foreach($recommendedGames as $index => $game)
                        <div class="flex-none w-72 sm:w-80 snap-center transition-transform duration-300 select-none">
                            <livewire:game-card 
                                :game="$game" 
                                :index="$index" 
                                wire:key="game-{{ $game->id }}" 
                            />
                        </div>
                    @endforeach
                </div>

                <button 
                    @click="scrollRight()" 
                    class="absolute right-2 sm:-right-4 top-1/2 transform -translate-y-1/2 z-20 bg-white dark:bg-gray-800 rounded-full p-2 shadow-lg border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none transition"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>

            @endif
        </div>
    </div>
</x-app-layout>
