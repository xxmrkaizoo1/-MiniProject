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
                $ollamaStatus = $ollamaStatus ?? [
                    'connected' => false,
                    'message' => 'Ollama not configured',
                ];

                $ratingPoints = collect($ratingTrendData)->values();
                $ratingPointCount = max($ratingPoints->count(), 1);
                $ratingSvgPoints = $ratingPoints
                    ->map(function ($value, $index) use ($ratingPointCount) {
                        $x = $ratingPointCount > 1 ? round(($index / ($ratingPointCount - 1)) * 100, 2) : 50;
                        $normalized = max(0, min(5, (float) $value));
                        $y = round(100 - ($normalized / 5) * 100, 2);

                        return "{$x},{$y}";
                    })
                    ->implode(' ');

                $sentimentPairs = collect($sentimentTrendLabels)
                    ->values()
                    ->map(function ($label, $index) use ($sentimentTrendData) {
                        return [
                            'label' => $label,
                            'value' => max(0, min(100, (int) ($sentimentTrendData[$index] ?? 0))),
                        ];
                    });
            @endphp
            <header class="animate-fade-in flex flex-wrap items-center justify-between gap-4">
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
                        class="animate-fade-up rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900 shadow-sm transition duration-300 hover:-translate-y-0.5 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-100">
                        <p class="font-semibold">{{ $notification['title'] }}</p>
                        <p>{{ $notification['message'] }}</p>
                    </div>
                @endif
            </header>
            <section
                class="animate-fade-up flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-5 text-sm shadow-sm transition duration-300 hover:shadow-md dark:border-slate-700 dark:bg-gray-900 sm:flex-row sm:items-center sm:justify-between">
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
                        class="animate-glow-pulse inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-slate-900">
                        Apply
                    </button>
                </form>
            </section>


            <section class="grid gap-6 lg:grid-cols-3">
                <div
                    class="animate-fade-up-delay-1 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition duration-300 hover:-translate-y-0.5 hover:shadow-md dark:border-slate-700 dark:bg-gray-900">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-semibold text-slate-500 dark:text-slate-300">Rating Trend</p>
                            <p class="mt-2 text-3xl font-semibold text-slate-900 dark:text-white">
                                {{ number_format($currentMonthAverage, 2) }} / 5
                            </p>
                        </div>
                        <span
                            class="animate-breathe rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-200">
                            {{ $ratingMoMChange >= 0 ? '+' : '' }}{{ $ratingMoMChange }}% MoM
                        </span>
                    </div>
                    <div
                        class="mt-4 h-40 rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-950">
                        @if ($ratingPoints->isNotEmpty() && $ratingPoints->sum() > 0)
                            <svg viewBox="0 0 100 100" class="h-full w-full" preserveAspectRatio="none" role="img"
                                aria-label="Rating trend graph">
                                <polyline points="{{ $ratingSvgPoints }}" fill="none" stroke="#6366F1"
                                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></polyline>
                            </svg>
                        @else
                            <div class="flex h-full items-center justify-center text-xs text-slate-500 dark:text-slate-400">
                                Not enough rating data to draw trend.
                            </div>
                        @endif
                    </div>
                    <div class="mt-2 flex justify-between text-[11px] text-slate-500 dark:text-slate-400">
                        @foreach ($ratingTrendLabels as $label)
                            <span>{{ $label }}</span>
                        @endforeach
                    </div>
                </div>

                <div
                    class="animate-fade-up-delay-2 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition duration-300 hover:-translate-y-0.5 hover:shadow-md dark:border-slate-700 dark:bg-gray-900">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-semibold text-slate-500 dark:text-slate-300">Weekly Sentiment</p>
                            <p class="mt-2 text-3xl font-semibold text-slate-900 dark:text-white">
                                {{ $weeklyPositiveRate }}%
                                Positive</p>
                        </div>
                        <span
                            class="animate-breathe rounded-full bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-600 dark:bg-rose-500/10 dark:text-rose-200">
                            Needs attention
                        </span>
                    </div>
                    <div
                        class="mt-4 h-40 rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-950">
                        @if ($sentimentPairs->isNotEmpty())
                            <div class="grid h-full grid-cols-7 items-end gap-2">
                                @foreach ($sentimentPairs as $item)
                                    <div class="flex h-full flex-col items-center justify-end gap-2">
                                        <div class="w-full rounded-t bg-orange-500/80"
                                            style="height: {{ max($item['value'], 4) }}%"></div>
                                        <span
                                            class="text-[10px] text-slate-500 dark:text-slate-400">{{ $item['label'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="flex h-full items-center justify-center text-xs text-slate-500 dark:text-slate-400">
                                No weekly sentiment data yet.
                            </div>
                        @endif
                    </div>
                </div>

                <div
                    class="animate-fade-up rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition duration-300 hover:-translate-y-0.5 hover:shadow-md dark:border-slate-700 dark:bg-gray-900">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-semibold text-slate-500 dark:text-slate-300">Top Issues</p>
                            <p class="mt-2 text-3xl font-semibold text-slate-900 dark:text-white">
                                {{ $issueLabelsValue->count() }} Focus Areas</p>
                        </div>
                        <span
                            class="animate-breathe rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-600 dark:bg-amber-500/10 dark:text-amber-200">
                            Needs follow-up
                        </span>
                    </div>
                    <div class="mt-4 space-y-3">
                        @foreach ($issueLabelsValue as $index => $label)
                            @php
                                $issuePercent = max(0, min(100, (int) $issueDataValue->get($index, 0)));
                            @endphp
                            <div>
                                <div
                                    class="mb-1 flex items-center justify-between text-sm text-gray-600 dark:text-gray-300">
                                    <span>{{ $label }}</span>
                                    <span
                                        class="font-semibold text-gray-900 dark:text-gray-100">{{ $issuePercent }}%</span>
                                </div>
                                <div class="h-2 rounded-full bg-slate-200 dark:bg-slate-700">
                                    <div class="h-2 rounded-full bg-indigo-500" style="width: {{ $issuePercent }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
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
                class="animate-fade-up-delay-3 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition duration-300 hover:shadow-md dark:border-slate-700 dark:bg-gray-900">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Lecturer Chatbot Assistant</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-300">
                            Get teaching suggestions based on your classes, subjects, and feedback statistics.
                        </p>
                    </div>
                    <span
                        class="animate-slide-shimmer inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-200">
                        Smart Advice
                    </span>
                </div>
                <div
                    class="mt-4 rounded-lg border px-3 py-2 text-xs {{ $ollamaStatus['connected'] ? 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-100' : 'border-amber-200 bg-amber-50 text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-100' }}">
                    <p class="font-semibold">Ollama status</p>
                    <p class="mt-1">{{ $ollamaStatus['message'] }}</p>
                    @if (!$ollamaStatus['connected'])
                        <p class="mt-2">Set <code>OLLAMA_BASE_URL</code> and <code>OLLAMA_MODEL</code> in your
                            <code>.env</code>, then run <code>ollama pull &lt;model&gt;</code>.
                        </p>
                    @endif
                </div>

                @php
                    $chatbotResponse = trim((string) session('chatbot_response', ''));
                    $responseSections = [];
                    $hasChatbotResponse = $chatbotResponse !== '';

                    if ($hasChatbotResponse) {
                        $sectionPattern = '/^\s*(?:#{1,3}\s*)?(Overview|Themes\s*&\s*Issues|Action)\s*:?\s*$/im';
                        preg_match_all($sectionPattern, $chatbotResponse, $sectionMatches, PREG_OFFSET_CAPTURE);

                        if (!empty($sectionMatches[1])) {
                            foreach ($sectionMatches[1] as $index => $sectionMatch) {
                                $sectionTitle = \Illuminate\Support\Str::title(
                                    str_replace('&', ' & ', preg_replace('/\s+/', ' ', trim($sectionMatch[0]))),
                                );
                                $sectionStart = $sectionMatches[0][$index][1] + strlen($sectionMatches[0][$index][0]);
                                $nextSectionStart = $sectionMatches[0][$index + 1][1] ?? strlen($chatbotResponse);
                                $sectionContent = trim(
                                    substr($chatbotResponse, $sectionStart, $nextSectionStart - $sectionStart),
                                );

                                if ($sectionContent !== '') {
                                    $responseSections[] = [
                                        'title' => $sectionTitle,
                                        'content' => $sectionContent,
                                    ];
                                }
                            }
                        }
                    }
                @endphp

                @if ($hasChatbotResponse)
                    <div
                        class="mt-5 rounded-2xl border border-indigo-100 bg-gradient-to-br from-white to-indigo-50/60 p-4 dark:border-indigo-500/20 dark:from-slate-900 dark:to-indigo-500/5">
                        <div class="mb-4 flex items-center justify-between gap-2">
                            <h4 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Chatbot output</h4>
                            <span
                                class="inline-flex items-center rounded-full bg-indigo-100 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wide text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-100">
                                Latest response
                            </span>
                        </div>

                        @forelse ($responseSections as $section)
                            @if ($loop->first)
                                <div class="grid gap-3 md:grid-cols-3">
                            @endif

                            <article
                                class="rounded-xl border border-slate-200 bg-white/90 p-4 text-sm text-slate-700 shadow-sm dark:border-slate-700 dark:bg-slate-950/80 dark:text-slate-200">
                                <p
                                    class="mb-2 text-xs font-semibold uppercase tracking-wide text-indigo-600 dark:text-indigo-300">
                                    {{ $section['title'] }}
                                </p>
                                @php
                                    $sectionLines = preg_split('/\R/', $section['content']) ?: [];
                                    $sectionItems = [];
                                    $sectionParagraphs = [];

                                    foreach ($sectionLines as $line) {
                                        $trimmedLine = trim($line);

                                        if ($trimmedLine === '') {
                                            continue;
                                        }

                                        if (preg_match('/^[\-•*]\s*(.+)$/u', $trimmedLine, $matches)) {
                                            $sectionItems[] = trim($matches[1]);
                                            continue;
                                        }

                                        $sectionParagraphs[] = $trimmedLine;
                                    }
                                @endphp

                                @if (!empty($sectionParagraphs))
                                    <div class="space-y-2 leading-6">
                                        @foreach ($sectionParagraphs as $paragraph)
                                            <p>{{ $paragraph }}</p>
                                        @endforeach
                                    </div>
                                @endif

                                @if (!empty($sectionItems))
                                    <ul class="mt-3 space-y-2">
                                        @foreach ($sectionItems as $item)
                                            <li
                                                class="rounded-lg border border-indigo-100 bg-indigo-50/70 px-3 py-2 text-sm leading-6 text-slate-700 dark:border-indigo-400/30 dark:bg-indigo-500/10 dark:text-slate-100">
                                                {{ $item }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </article>

                            @if ($loop->last)
                    </div>
                @endif
            @empty
                <div
                    class="whitespace-pre-line rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-100">
                    {{ $chatbotResponse }}
                </div>
                @endforelse
        </div>
        @endif

        <form method="POST" action="{{ route('lecturer.chatbot.respond') }}"
            class="mt-6 grid gap-4 rounded-2xl border border-slate-200 bg-slate-50/70 p-4 lg:grid-cols-3 dark:border-slate-700 dark:bg-slate-950/40">
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
                        <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                    @endforeach
                </select>
                @error('subject_id')
                    <p class="mt-2 text-xs text-rose-600 dark:text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            {{-- chatbot Assist for AI auto generated notes and action d fix  - Fix eacdd --}}


            <div>
                <label for="prompt" class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                    Lecturer notes (optional)
                </label>
                <div
                    class="mt-2 rounded-xl border border-slate-200 bg-white/95 p-3 shadow-sm transition focus-within:border-indigo-400 focus-within:ring-2 focus-within:ring-indigo-200 dark:border-slate-700 dark:bg-slate-950/90 dark:focus-within:border-indigo-400 dark:focus-within:ring-indigo-500/20">
                    <textarea id="prompt" name="prompt" rows="4" maxlength="500"
                        class="w-full resize-none border-0 bg-transparent p-0 text-sm leading-6 text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-0 dark:text-slate-100 dark:placeholder:text-slate-500"
                        placeholder="Share context, pain points, or goals for your next session">{{ old('prompt') }}</textarea>

                    <div
                        class="mt-3 flex items-center justify-between gap-3 border-t border-slate-100 pt-3 dark:border-slate-800">
                        <p class="text-xs text-slate-500 dark:text-slate-400">Tip: add goals like “increase participation”
                            or “improve quiz results”.</p>
                        <p id="prompt-counter" class="text-xs font-medium text-slate-500 dark:text-slate-400">0/500</p>
                    </div>
                </div>

                <div class="mt-3 flex flex-wrap gap-2" aria-label="Prompt suggestions">
                    <button type="button"
                        class="prompt-chip rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-100 dark:border-indigo-500/40 dark:bg-indigo-500/10 dark:text-indigo-200 dark:hover:bg-indigo-500/20"
                        data-value="Focus on students who seem disengaged and suggest one activity for next class.">
                        Disengaged students
                    </button>
                    <button type="button"
                        class="prompt-chip rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-100 dark:border-indigo-500/40 dark:bg-indigo-500/10 dark:text-indigo-200 dark:hover:bg-indigo-500/20"
                        data-value="Give a 1-week action plan to improve understanding before the next quiz.">
                        1-week action plan
                    </button>
                    <button type="button"
                        class="prompt-chip rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-100 dark:border-indigo-500/40 dark:bg-indigo-500/10 dark:text-indigo-200 dark:hover:bg-indigo-500/20"
                        data-value="Suggest interactive teaching strategies for mixed performance students.">
                        Interactive strategies
                    </button>
                </div>



            @error('prompt')
                <p class="mt-2 text-xs text-rose-600 dark:text-rose-300">{{ $message }}</p>
            @enderror
    </div>

    <div class="lg:col-span-3">
        <button type="submit"
            class="animate-glow-pulse inline-flex w-full items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-slate-900">
            Generate Advice
        </button>
    </div>
    </form>
    </section>

    <section
        class="animate-fade-up-delay-3 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition duration-300 hover:shadow-md dark:border-slate-700 dark:bg-gray-900">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Student Feedback</h3>
        <p class="text-sm text-slate-600 dark:text-slate-300">Latest feedback submitted by students.</p>

        <div class="mt-6 space-y-4">
            @forelse ($feedbacks as $feedback)
                <div
                    class="animate-fade-up flex flex-col gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition duration-300 hover:-translate-y-0.5 hover:shadow-md dark:border-slate-700 dark:bg-slate-950">
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
        document.addEventListener('DOMContentLoaded', () => {
            const prompt = document.getElementById('prompt');
            const counter = document.getElementById('prompt-counter');
            const chips = document.querySelectorAll('.prompt-chip');

            if (!prompt || !counter) {
                return;
            }

            const updatePromptUi = () => {
                const length = prompt.value.length;
                counter.textContent = `${length}/500`;
                counter.classList.toggle('text-rose-500', length > 450);

                prompt.style.height = 'auto';
                prompt.style.height = `${Math.min(prompt.scrollHeight, 220)}px`;
            };

            prompt.addEventListener('input', updatePromptUi);

            chips.forEach((chip) => {
                chip.addEventListener('click', () => {
                    const suggestion = chip.dataset.value || '';

                    if (prompt.value.trim().length > 0) {
                        prompt.value = `${prompt.value.trim()}\n${suggestion}`;
                    } else {
                        prompt.value = suggestion;
                    }

                    prompt.dispatchEvent(new Event('input', {
                        bubbles: true
                    }));
                    prompt.focus();
                });
            });

            updatePromptUi();
        });
    </script>
@endpush
