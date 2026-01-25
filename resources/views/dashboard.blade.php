<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 space-y-8">
                    <div class="space-y-4">
                        <p class="text-sm text-gray-600 dark:text-gray-300">Ringkasan prestasi untuk kelas yang anda kendalikan.</p>
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-200">
                            <p class="font-semibold text-slate-900 dark:text-white">Senarai kelas anda</p>
                            @if ($classrooms->isEmpty())
                                <p class="mt-2 text-xs text-rose-600">Tiada kelas ditetapkan buat masa ini.</p>
                            @else
                                <ul class="mt-3 space-y-2">
                                    @foreach ($classrooms as $classroom)
                                        <li class="flex flex-wrap items-center justify-between gap-2 text-sm">
                                            <span class="font-medium text-slate-800 dark:text-slate-100">{{ $classroom->name }}</span>
                                            <span class="text-xs text-slate-500 dark:text-slate-400">
                                                {{ $classroom->subject?->name ?? 'Subjek belum ditetapkan' }} · {{ $classroom->enrollments_count }} pelajar
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>

                    @if ($notification)
                        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-amber-900 shadow-sm dark:border-amber-500/40 dark:bg-amber-500/10 dark:text-amber-100">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold uppercase tracking-wide">{{ $notification['title'] }}</p>
                                    <p class="mt-1 text-sm text-amber-800 dark:text-amber-100">
                                        {{ $notification['message'] }}
                                    </p>
                                </div>
                                <div class="text-sm text-amber-700 dark:text-amber-200">
                                    <p>Rating rendah: {{ $avgRating ? number_format($avgRating, 2) : '0.00' }}/5</p>
                                    <p>{{ $negativeCount }} daripada {{ $totalFeedback }} komen negatif ({{ $negativeRatio }}%)</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <section class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Smart Classroom Insights</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Ringkasan visual untuk meningkatkan nilai Smart Classroom dari sisi rating, sentimen, dan isu utama.
                            </p>
                        </div>

                        <div class="grid gap-6 lg:grid-cols-3">
                            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Trend Rating</p>
                                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">4.3 / 5</p>
                                    </div>
                                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200">
                                        +8% MoM
                                    </span>
                                </div>
                                <div class="mt-4 h-40">
                                    <canvas id="ratingTrendChart" aria-label="Trend rating kelas pintar"></canvas>
                                </div>
                            </div>

                            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sentimen Mingguan</p>
                                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">72% Positif</p>
                                    </div>
                                    <span class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-200">
                                        Stabil
                                    </span>
                                </div>
                                <div class="mt-4 h-40">
                                    <canvas id="sentimentChart" aria-label="Sentimen mingguan"></canvas>
                                </div>
                            </div>

                            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Isu Utama</p>
                                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">3 Fokus</p>
                                    </div>
                                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-900/40 dark:text-amber-200">
                                        Perlu tindak
                                    </span>
                                </div>
                                <div class="mt-4 h-40">
                                    <canvas id="issuesChart" aria-label="Distribusi isu utama"></canvas>
                                </div>
                                <ul class="mt-4 space-y-2 text-sm text-gray-600 dark:text-gray-300">
                                    <li class="flex items-center justify-between">
                                        <span>Konektivitas Wi-Fi</span>
                                        <span class="font-semibold text-gray-900 dark:text-gray-100">38%</span>
                                    </li>
                                    <li class="flex items-center justify-between">
                                        <span>Stabilitas proyektor</span>
                                        <span class="font-semibold text-gray-900 dark:text-gray-100">27%</span>
                                    </li>
                                    <li class="flex items-center justify-between">
                                        <span>Sinkronisasi LMS</span>
                                        <span class="font-semibold text-gray-900 dark:text-gray-100">21%</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const sharedOptions = {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#111827',
                            titleColor: '#f9fafb',
                            bodyColor: '#e5e7eb',
                            borderColor: '#374151',
                            borderWidth: 1
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: '#9ca3af' }
                        },
                        y: {
                            grid: { color: 'rgba(148, 163, 184, 0.2)' },
                            ticks: { color: '#9ca3af' }
                        }
                    }
                };

                new Chart(document.getElementById('ratingTrendChart'), {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                        datasets: [{
                            data: [3.8, 3.9, 4.1, 4.0, 4.2, 4.3],
                            borderColor: '#4f46e5',
                            backgroundColor: 'rgba(79, 70, 229, 0.15)',
                            fill: true,
                            tension: 0.4,
                            pointRadius: 3,
                            pointBackgroundColor: '#4f46e5'
                        }]
                    },
                    options: {
                        ...sharedOptions,
                        scales: {
                            ...sharedOptions.scales,
                            y: { ...sharedOptions.scales.y, min: 3.5, max: 5 }
                        }
                    }
                });

                new Chart(document.getElementById('sentimentChart'), {
                    type: 'bar',
                    data: {
                        labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                        datasets: [{
                            data: [68, 70, 74, 71, 73, 75, 72],
                            backgroundColor: '#22c55e',
                            borderRadius: 6
                        }]
                    },
                    options: {
                        ...sharedOptions,
                        scales: {
                            ...sharedOptions.scales,
                            y: { ...sharedOptions.scales.y, min: 0, max: 100 }
                        }
                    }
                });

                new Chart(document.getElementById('issuesChart'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Wi-Fi', 'Proyektor', 'LMS', 'Lainnya'],
                        datasets: [{
                            data: [38, 27, 21, 14],
                            backgroundColor: ['#f97316', '#facc15', '#38bdf8', '#94a3b8'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { color: '#9ca3af' }
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
