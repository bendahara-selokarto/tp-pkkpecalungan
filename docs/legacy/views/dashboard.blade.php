<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-5">
                    <p class="text-sm text-gray-600 dark:text-gray-300">Total Activity</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $dashboardStats['total'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-5">
                    <p class="text-sm text-gray-600 dark:text-gray-300">Bulan Ini</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $dashboardStats['this_month'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-5">
                    <p class="text-sm text-gray-600 dark:text-gray-300">Published</p>
                    <p class="text-2xl font-semibold text-green-600 dark:text-green-400">{{ $dashboardStats['published'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-5">
                    <p class="text-sm text-gray-600 dark:text-gray-300">Draft</p>
                    <p class="text-2xl font-semibold text-yellow-600 dark:text-yellow-400">{{ $dashboardStats['draft'] }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">Trend Activity 6 Bulan</h3>
                        <canvas id="monthlyActivityChart" height="120"></canvas>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">Komposisi Status</h3>
                        <canvas id="statusActivityChart" height="120"></canvas>
                    </div>
                </div>
            </div>

            <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">Komposisi Level Activity</h3>
                    <canvas id="levelActivityChart" height="90"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script id="dashboard-chart-data" type="application/json">@json($dashboardCharts)</script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        const chartPayloadElement = document.getElementById('dashboard-chart-data');
        const chartPayload = chartPayloadElement ? JSON.parse(chartPayloadElement.textContent) : null;

        if (chartPayload) {
            new Chart(document.getElementById('monthlyActivityChart'), {
                type: 'line',
                data: {
                    labels: chartPayload.monthly.labels,
                    datasets: [{
                        label: 'Jumlah Activity',
                        data: chartPayload.monthly.values,
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.2)',
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } }
                }
            });

            new Chart(document.getElementById('statusActivityChart'), {
                type: 'doughnut',
                data: {
                    labels: chartPayload.status.labels,
                    datasets: [{
                        data: chartPayload.status.values,
                        backgroundColor: ['#f59e0b', '#10b981']
                    }]
                },
                options: { responsive: true }
            });

            new Chart(document.getElementById('levelActivityChart'), {
                type: 'bar',
                data: {
                    labels: chartPayload.level.labels,
                    datasets: [{
                        label: 'Jumlah Activity',
                        data: chartPayload.level.values,
                        backgroundColor: ['#06b6d4', '#6366f1']
                    }]
                },
                options: {
                    responsive: true,
                    scales: { y: { beginAtZero: true, precision: 0 } },
                    plugins: { legend: { display: false } }
                }
            });
        }
    </script>
</x-app-layout>
