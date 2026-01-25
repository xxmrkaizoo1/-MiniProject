<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Submit Feedback</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 bg-slate-50">
    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl">
            <div class="mb-8 text-center">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">Student Voice</p>
                <h1 class="mt-2 text-3xl font-bold text-slate-900 sm:text-4xl">Student Feedback Form</h1>
                <p class="mt-3 text-base text-slate-600">Share your thoughts so we can keep improving your learning experience.</p>
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-lg ring-1 ring-slate-200 sm:p-10">
                @if (session('success'))
                    <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        <p class="font-semibold">Please fix the following:</p>
                        <ul class="mt-2 list-disc space-y-1 pl-5">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="/feedback" class="space-y-6">
                    @csrf

                    <div>
                        <label for="subject" class="block text-sm font-medium text-slate-700">Subject</label>
                        <select id="subject" name="subject" required {{ $subjects->isEmpty() ? 'disabled' : '' }}
                            class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                            <option value="">Select a subject</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->name }}" {{ old('subject') === $subject->name ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                        @if ($subjects->isEmpty())
                            <p class="mt-2 text-xs text-rose-600">No subjects assigned to your class yet. Please contact your lecturer or admin.</p>
                        @endif
                    </div>

                    <div>
                        <label for="rating" class="block text-sm font-medium text-slate-700">Rating (1-5)</label>
                        <input id="rating" type="number" name="rating" min="1" max="5" value="{{ old('rating') }}" required
                            class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                    </div>

                    <div>
                        <label for="mood_rating" class="block text-sm font-medium text-slate-700">Mood Check-In (Emosi Pelajar)</label>
                        <p class="mt-1 text-sm text-slate-500">Bagaimana perasaan anda selepas kelas?</p>
                        <input id="mood_rating" type="range" name="mood_rating" min="1" max="5" value="{{ old('mood_rating', 3) }}" required
                            class="mt-4 w-full accent-indigo-600">
                        <div class="mt-3 flex items-center justify-between text-xl">
                            <span title="Sangat sedih">😞</span>
                            <span title="Sedih">😕</span>
                            <span title="Neutral">😐</span>
                            <span title="Gembira">🙂</span>
                            <span title="Sangat gembira">😄</span>
                        </div>
                        <div class="mt-2 flex items-center justify-between text-xs text-slate-500">
                            <span>Rendah</span>
                            <span>Tinggi</span>
                        </div>
                    </div>

                    <div>
                        <label for="comments" class="block text-sm font-medium text-slate-700">Comment</label>
                        <textarea id="comments" name="comments" rows="5"
                            class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">{{ old('comments') }}</textarea>
                    </div>

                    <div class="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <input id="is_anonymous" type="checkbox" name="is_anonymous" value="1"
                            class="mt-1 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <div>
                            <label for="is_anonymous" class="text-sm font-medium text-slate-700">Submit as anonymous</label>
                            <p class="text-xs text-slate-500">We store your user ID for audit purposes, but it remains hidden in admin views.</p>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <button type="submit" {{ $subjects->isEmpty() ? 'disabled' : '' }}
                            class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-300 disabled:cursor-not-allowed disabled:bg-indigo-300">
                            Send Feedback
                        </button>
                        <div class="flex flex-col gap-2 text-sm font-semibold sm:flex-row sm:items-center sm:gap-4">
                            <a href="{{ url('/') }}"
                                class="text-slate-600 transition hover:text-slate-500">
                                Back to Home
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="text-rose-600 transition hover:text-rose-500">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
