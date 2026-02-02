@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-slate-100 pb-24 pt-10 text-slate-900 dark:bg-slate-900 dark:text-slate-100">
        <div class="mx-auto flex w-full max-w-6xl flex-col gap-8 px-6">
            @php
                $feedbacks = $feedbacks ?? collect();
                $currentMonthAverage = $currentMonthAverage ?? 0;
                $ratingMoMChange = $ratingMoMChange ?? 0;
                $weeklyPositiveRate = $weeklyPositiveRate ?? 0;
                $ratingTrendLabels = $ratingTrendLabels ?? [];
                $ratingTrendData = $ratingTrendData ?? [];
                $sentimentTrendLabels = $sentimentTrendLabels ?? [];
                $sentimentTrendData = $sentimentTrendData ?? [];
                $issueLabelsValue = collect($issueLabels ?? []);
                $issueDataValue = collect($issueData ?? []);
                $focusAreaAdvice = $focusAreaAdvice ?? null;
            @endphp
            <header class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold">Lecturer Dashboard</h1>
                    <p class="text-sm text-slate-600 dark:text-slate-300">
                        Track performance, insights, and student feedback at a glance.
                    </p>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        Showing statistics for
                        <span class="font-semibold text-slate-700 dark:text-slate-200">
                            {{ $selectedSubject?->name ?? 'all subjects' }}
                        </span>.
                    </p>
                </div>
                @if ($notification)
                    <div
                        class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900 shadow-sm dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-100">
                        <p class="font-semibold">{{ $notification['title'] }}</p>
                        <p>{{ $notification['message'] }}</p>
                    </div>
                @endif
            </header>
            <section
                class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-5 text-sm shadow-sm dark:border-slate-700 dark:bg-gray-900 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="font-semibold text-slate-900 dark:text-white">Filter dashboard statistics</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Choose a subject to see rating, sentiment, and issues for that course only.
                    </p>
                </div>
                <form method="GET" action="{{ route('dashboard') }}" class="flex w-full gap-2 sm:w-auto">
                    <select name="subject_id"
                        class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 sm:w-56">
                        <option value="">All subjects</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}"
                                {{ $selectedSubject && $selectedSubject->id === $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-slate-900">
                        Apply
                    </button>
                </form>
            </section>


            <section class="grid gap-6 lg:grid-cols-3">
                <div
                    class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-gray-900">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-semibold text-slate-500 dark:text-slate-300">Rating Trend</p>
                            <p class="mt-2 text-3xl font-semibold text-slate-900 dark:text-white">
                                {{ number_format($currentMonthAverage, 2) }} / 5
                            </p>
                        </div>
                        <span
                            class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-200">
                            {{ $ratingMoMChange >= 0 ? '+' : '' }}{{ $ratingMoMChange }}% MoM
                        </span>
                    </div>
                    <div class="mt-4 h-40">
                        <canvas id="ratingChart" aria-label="Rating trend chart"></canvas>
                    </div>
                </div>

                <div
                    class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-gray-900">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-semibold text-slate-500 dark:text-slate-300">Weekly Sentiment</p>
                            <p class="mt-2 text-3xl font-semibold text-slate-900 dark:text-white">
                                {{ $weeklyPositiveRate }}%
                                Positive</p>
                        </div>
                        <span
                            class="rounded-full bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-600 dark:bg-rose-500/10 dark:text-rose-200">
                            Needs attention
                        </span>
                    </div>
                    <div class="mt-4 h-40">
                        <canvas id="sentimentChart" aria-label="Weekly sentiment chart"></canvas>
                    </div>
                </div>

                <div
                    class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-gray-900">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-semibold text-slate-500 dark:text-slate-300">Top Issues</p>
                            <p class="mt-2 text-3xl font-semibold text-slate-900 dark:text-white">
                                {{ $issueLabelsValue->count() }} Focus Areas</p>
                        </div>
                        <span
                            class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-600 dark:bg-amber-500/10 dark:text-amber-200">
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
                                <span
                                    class="font-semibold text-gray-900 dark:text-gray-100">{{ $issueDataValue->get($index, 0) }}%</span>
                            </li>
                        @endforeach
                    </ul>
                    @if ($focusAreaAdvice)
                        <div
                            class="mt-4 rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-3 text-xs text-indigo-900 dark:border-indigo-500/30 dark:bg-indigo-500/10 dark:text-indigo-100">
                            <p class="font-semibold">AI focus actions</p>
                            <p class="mt-1 whitespace-pre-line">{{ $focusAreaAdvice }}</p>
                        </div>
                    @endif
                </div>
            </section>

            <section
                class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-gray-900">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Lecturer Chatbot Assistant</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-300">
                            Get teaching suggestions based on your classes and subjects.
                        </p>
                    </div>
                    <span
                        class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-200">
                        Smart Advice
                    </span>
                </div>

                @if (session('chatbot_response'))
                    <div
                        class="mt-4 whitespace-pre-line rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-100">
                        {{ session('chatbot_response') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('lecturer.chatbot.respond') }}"
                    class="mt-6 grid gap-4 lg:grid-cols-3">
                    @csrf
                    <div>
                        <label for="classroom_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                            Class
                        </label>
                        <select id="classroom_id" name="classroom_id" {{ $classrooms->isNotEmpty() ? 'required' : '' }}
                            class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                            <option value="">Select class</option>
                            @foreach ($classrooms as $classroom)
                                <option value="{{ $classroom->id }}"
                                    {{ old('classroom_id') == $classroom->id ? 'selected' : '' }}>
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
                                <option value="{{ $subject->id }}"
                                    {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('subject_id')
                            <p class="mt-2 text-xs text-rose-600 dark:text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="prompt" class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                            Lecturer notes (optional)
                        </label>
                        <textarea id="prompt" name="prompt" rows="3"
                            class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100"
                            placeholder="Share context or goals for your next session">{{ old('prompt') }}</textarea>
                        @error('prompt')
                            <p class="mt-2 text-xs text-rose-600 dark:text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="lg:col-span-3">
                        <button type="submit"
                            class="inline-flex w-full items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-slate-900">
                            Generate Advice
                        </button>
                    </div>
                </form>
            </section>

            <section
                class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-gray-900">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Student Feedback</h3>
                <p class="text-sm text-slate-600 dark:text-slate-300">Latest feedback submitted by students.</p>

                <div class="mt-6 space-y-4">
                    @forelse ($feedbacks as $feedback)
                        <div
                            class="flex flex-col gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-950">
                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900 dark:text-white">
                                        {{ $feedback->subject }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-300">
                                        {{ optional($feedback->created_at)->format('d M Y, H:i') }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span
                                        class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-500/10 dark:text-amber-100">
                                        Rating {{ $feedback->rating }}/5
                                    </span>
                                    <span
                                        class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-100">
                                        Mood {{ $feedback->mood_rating }}/5
                                    </span>
                                </div>
                            </div>
                            <p class="text-sm text-slate-600 dark:text-slate-300">
                                {{ $feedback->comments ?: 'No comment provided.' }}
                            </p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500 dark:text-slate-300">No feedback submissions yet.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const ratingCtx = document.getElementById('ratingChart');
        if (ratingCtx) {
            new Chart(ratingCtx, {
                type: 'line',
                data: {
                    labels: @json($ratingTrendLabels),
                    datasets: [{
                        label: 'Average Rating',
                        data: @json($ratingTrendData),
                        borderColor: '#6366F1',
                        backgroundColor: 'rgba(99, 102, 241, 0.2)',
                        tension: 0.4,
                        fill: true,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            min: 0,
                            max: 5,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        const sentimentCtx = document.getElementById('sentimentChart');
        if (sentimentCtx) {
            new Chart(sentimentCtx, {
                type: 'line',
                data: {
                    labels: @json($sentimentTrendLabels),
                    datasets: [{
                        label: 'Positive Sentiment %',
                        data: @json($sentimentTrendData),
                        borderColor: '#F97316',
                        backgroundColor: 'rgba(249, 115, 22, 0.2)',
                        tension: 0.4,
                        fill: true,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            min: 0,
                            max: 100,
                            ticks: {
                                callback: value => `${value}%`
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        const issuesCtx = document.getElementById('issuesChart');
        if (issuesCtx) {
            new Chart(issuesCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($issueLabelsValue),
                    datasets: [{
                        data: @json($issueDataValue),
                        backgroundColor: ['#F97316', '#FACC15', '#22C55E', '#6366F1'],
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }
    </script>
@endpush
