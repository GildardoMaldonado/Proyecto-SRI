<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Panel de Administración (Métricas del Algoritmo)') }}
        </h2>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border-l-4 border-indigo-500">
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-bold uppercase">Usuarios Totales</p>
                    <p class="text-3xl font-extrabold text-gray-900 dark:text-white mt-2">{{ $totalUsers }}</p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border-l-4 border-green-500">
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-bold uppercase">Juegos en Catálogo</p>
                    <p class="text-3xl font-extrabold text-gray-900 dark:text-white mt-2">{{ $totalGames }}</p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border-l-4 border-yellow-500">
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-bold uppercase">Calificaciones (Interacciones)</p>
                    <p class="text-3xl font-extrabold text-gray-900 dark:text-white mt-2">{{ $totalInteractions }}</p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border-l-4 {{ $sparsity > 95 ? 'border-red-500' : 'border-blue-500' }}">
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-bold uppercase" title="Porcentaje del catálogo no calificado">Dispersión de Datos</p>
                    <p class="text-3xl font-extrabold text-gray-900 dark:text-white mt-2">{{ $sparsity }}%</p>
                </div>

            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Top 5 Juegos con más Interacciones</h3>
                    <canvas id="topGamesChart" height="200"></canvas>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border border-gray-200 dark:border-gray-700 flex items-center justify-center">
                    <p class="text-gray-500 dark:text-gray-400">Espacio reservado para expansión de analíticas</p>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            const ctx = document.getElementById('topGamesChart').getContext('2d');
            
            // Pasamos los datos de PHP a Javascript de forma segura
            const labels = @json($topGames->pluck('title'));
            const data = @json($topGames->pluck('interactions_count'));

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Calificaciones Recibidas',
                        data: data,
                        backgroundColor: 'rgba(99, 102, 241, 0.5)', // Color Índigo de Tailwind
                        borderColor: 'rgb(99, 102, 241)',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 } // Solo números enteros
                        }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        });
    </script>
</div>
