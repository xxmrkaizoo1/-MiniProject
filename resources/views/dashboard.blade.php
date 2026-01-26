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
                        <p class="text-sm text-gray-600 dark:text-gray-300">Performance summary for the classes you manage.</p>
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-200">
                            <p class="font-semibold text-slate-900 dark:text-white">Your classes</p>
                            @if ($classrooms->isEmpty())
                                <p class="mt-2 text-xs text-rose-600">No classes assigned yet.</p>
                            @else
                                <ul class="mt-3 space-y-2">
                                    @foreach ($classrooms as $classroom)
                                        <li class="flex flex-wrap items-center justify-between gap-2 text-sm">
                                            <span class="font-medium text-slate-800 dark:text-slate-100">{{ $classroom->name }}</span>
                                            <span class="text-xs text-slate-500 dark:text-slate-400">
                                                {{ $classroom->subject?->name ?? 'Subject not set' }} · {{ $classroom->enrollments_count }} students
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
                                    <p>Low rating: {{ $avgRating ? number_format($avgRating, 2) : '0.00' }}/5</p>
                                    <p>{{ $negativeCount }} of {{ $totalFeedback }} comments are negative ({{ $negativeRatio }}%)</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <section class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Smart Classroom Insights</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Visual summary to improve Smart Classroom performance across ratings, sentiment, and key issues.
                            </p>
                        </div>

                        <div class="grid gap-6 lg:grid-cols-3">
                            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                                @php
                                    $ratingMoMChangeValue = $ratingMoMChange ?? 0;
                                    $currentMonthAverageValue = $currentMonthAverage ?? 0;
                                    $ratingChangeLabel = ($ratingMoMChangeValue >= 0 ? '+' : '') . $ratingMoMChangeValue . '% MoM';
                                    $ratingChangeClasses = $ratingMoMChangeValue >= 0
                                        ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200'
                                        : 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-200';
                                @endphp
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Rating Trend</p>
                                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                            {{ $currentMonthAverageValue > 0 ? number_format($currentMonthAverageValue, 2) : '0.00' }} / 5
                                        </p>
                                    </div>
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $ratingChangeClasses }}">
                                        {{ $ratingChangeLabel }}
                                    </span>
                                </div>
                                <div class="mt-4 h-40">
                                    <canvas id="ratingTrendChart" aria-label="Smart classroom rating trend"></canvas>
                                </div>
                            </div>

                            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                                @php
                                    $weeklyPositiveRateValue = $weeklyPositiveRate ?? 0;
                                    $sentimentStatus = $weeklyPositiveRateValue >= 70 ? 'Stable' : ($weeklyPositiveRateValue >= 50 ? 'Watch' : 'Needs attention');
                                    $sentimentClasses = $weeklyPositiveRateValue >= 70
                                        ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-200'
                                        : ($weeklyPositiveRateValue >= 50
                                            ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-200'
                                            : 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-200');
                                @endphp
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Weekly Sentiment</p>
                                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $weeklyPositiveRateValue }}% Positive</p>
                                    </div>
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $sentimentClasses }}">
                                        {{ $sentimentStatus }}
                                    </span>
                                </div>
                                <div class="mt-4 h-40">
                                    <canvas id="sentimentChart" aria-label="Weekly sentiment"></canvas>
                                </div>
                            </div>

                            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                                @php
                                    $issueLabelsValue = collect($issueLabels ?? []);
                                    $issueDataValue = collect($issueData ?? []);
                                    $issueFocusCount = $issueDataValue->filter(fn ($value) => $value > 0)->count();
                                @endphp
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Top Issues</p>
                                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $issueFocusCount }} Focus Areas</p>
                                    </div>
                                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-900/40 dark:text-amber-200">
                                        Needs follow-up
                                    </span>
                                </div>
                                <div class="mt-4 h-40">
                                    <canvas id="issuesChart" aria-label="Top issue distribution"></canvas>
                                </div>
                                <ul class="mt-4 space-y-2 text-sm text-gray-600 dark:text-gray-300">
                                    @foreach ($issueLabelsValue as $index => $label)
                                        <li class="flex items-center justify-between">
                                            <span>{{ $label }}</span>
                                            <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $issueDataValue->get($index, 0) }}%</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-gray-900">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Lecturer Chatbot Assistant</h3>
                                <p class="text-sm text-slate-600 dark:text-slate-300">
                                    Get teaching suggestions based on your classes and subjects.
                                </p>
                            </div>
                            <span class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-200">
                                Smart Advice
                            </span>
                        </div>

                        @if (session('chatbot_response'))
                            <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-100">
                                {{ session('chatbot_response') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('lecturer.chatbot.respond') }}" class="mt-6 grid gap-4 lg:grid-cols-3">
                            @csrf
                            <div>
                                <label for="classroom_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                    Class
                                </label>
                                <select id="classroom_id" name="classroom_id" {{ $classrooms->isNotEmpty() ? 'required' : '' }}
                                    class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                                    <option value="">Select class</option>
                                    @foreach ($classrooms as $classroom)
                                        <option value="{{ $classroom->id }}" {{ old('classroom_id') == $classroom->id ? 'selected' : '' }}>
                                            {{ $classroom->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($classrooms->isEmpty())
                                    <p class="mt-2 text-xs text-rose-600 dark:text-rose-300">No classes assigned yet.</p>
                                @endif
                                @error('classroom_id')
                                    <p class="mt-2 text-xs text-rose-600 dark:text-rose-300">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="subject_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                    Subject
                                </label>
                                <select id="subject_id" name="subject_id" required
                                    class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                                    <option value="">Select subject</option>
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($subjects->isEmpty())
                                    <p class="mt-2 text-xs text-amber-600 dark:text-amber-300">No subject assigned to this class.</p>
                                @endif
                                @error('subject_id')
                                    <p class="mt-2 text-xs text-rose-600 dark:text-rose-300">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="prompt" class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                    Lecturer notes (optional)
                                </label>
                                <textarea id="prompt" name="prompt" rows="3"
                                    class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">{{ old('prompt') }}</textarea>
                                @error('prompt')
                                    <p class="mt-2 text-xs text-rose-600 dark:text-rose-300">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="lg:col-span-3 flex flex-col items-start gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <button type="submit"
                                    class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                    Get suggestions
                                </button>
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    Suggestions are generated from the selected class and subject.
                                </p>
                            </div>
                        </form>
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
                        labels: @json($ratingTrendLabels ?? []),
                        datasets: [{
                            data: @json($ratingTrendData ?? []),
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
                        labels: @json($sentimentTrendLabels ?? []),
                        datasets: [{
                            data: @json($sentimentTrendData ?? []),
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
                        labels: @json($issueLabels ?? []),
                        datasets: [{
                            data: @json($issueData ?? []),
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
