<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - Feedback List</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 bg-slate-50">
    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        <div class="mx-auto flex max-w-6xl flex-col gap-8">
            <div class="flex flex-col gap-4 text-center sm:text-left">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">Feedback Overview</p>
                    <h1 class="text-3xl font-bold text-slate-900 sm:text-4xl">Admin Feedback List</h1>
                    <p class="text-base text-slate-600">Review submissions, filter by subject, and track average ratings.</p>
                </div>
                <div class="flex flex-wrap items-center justify-center gap-3 sm:justify-start">
                    <a href="/admin/feedback"
                        class="inline-flex items-center rounded-full bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        Feedback
                    </a>
                    <a href="{{ route('admin.subjects.index') }}"
                        class="inline-flex items-center rounded-full bg-sky-500 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-600 focus:outline-none focus:ring-2 focus:ring-sky-200">
                        Subjects
                    </a>
                    <a href="{{ route('admin.classrooms.index') }}"
                        class="inline-flex items-center rounded-full bg-emerald-500 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-200">
                        Classes
                    </a>
                    <a href="/feedback"
                        class="inline-flex items-center rounded-full bg-slate-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-200">
                        Back to Form
                    </a>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="rounded-2xl bg-white p-6 shadow-lg ring-1 ring-slate-200">
                    <h2 class="text-lg font-semibold text-slate-900">Filter Feedback</h2>
                    <p class="mt-1 text-sm text-slate-500">Choose a subject to narrow down the list.</p>
                    <form method="GET" action="/admin/feedback" class="mt-4 space-y-4">
                        <div>
                            <label for="subject" class="block text-sm font-medium text-slate-700">Subject</label>
                            <select id="subject" name="subject"
                                class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                                <option value="">All</option>
                                @foreach ($subjects as $s)
                                    <option value="{{ $s }}" {{ $subject === $s ? 'selected' : '' }}>
                                        {{ $s }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit"
                            class="inline-flex w-full items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            Apply Filter
                        </button>
                    </form>
                </div>

                <div class="rounded-2xl bg-indigo-600 p-6 text-white shadow-lg lg:col-span-2">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm uppercase tracking-[0.2em] text-indigo-100">Average Rating</p>
                            <p class="mt-1 text-3xl font-bold">
                                {{ $avgRating ? number_format($avgRating, 2) : '0.00' }}
                            </p>
                        </div>
                        <div class="rounded-xl bg-white/10 px-4 py-3 text-sm text-indigo-50">
                            Based on {{ $feedbacks->count() }} submission{{ $feedbacks->count() === 1 ? '' : 's' }}.
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="rounded-2xl bg-white p-6 shadow-lg ring-1 ring-slate-200 lg:col-span-2">
                    <div class="flex flex-col gap-3">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">Analisis AI</p>
                            <h2 class="text-xl font-semibold text-slate-900">Ringkasan & Sentimen</h2>
                        </div>
                        <p class="text-sm text-slate-600">{{ $analysis['summary'] }}</p>
                        <div class="grid gap-4 sm:grid-cols-3">
                            <div class="rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                                <p class="text-xs uppercase tracking-[0.2em] text-emerald-500">Positif</p>
                                <p class="mt-1 text-2xl font-semibold">{{ $analysis['sentimentCounts']['positive'] }}</p>
                            </div>
                            <div class="rounded-xl bg-slate-100 px-4 py-3 text-sm text-slate-700">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Neutral</p>
                                <p class="mt-1 text-2xl font-semibold">{{ $analysis['sentimentCounts']['neutral'] }}</p>
                            </div>
                            <div class="rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-700">
                                <p class="text-xs uppercase tracking-[0.2em] text-rose-500">Negatif</p>
                                <p class="mt-1 text-2xl font-semibold">{{ $analysis['sentimentCounts']['negative'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="rounded-2xl bg-white p-6 shadow-lg ring-1 ring-slate-200">
                    <h3 class="text-base font-semibold text-slate-900">Komen Utama</h3>
                    <p class="mt-1 text-sm text-slate-500">Sorotan ringkas dari maklum balas.</p>
                    <div class="mt-4 space-y-3 text-sm text-slate-600">
                        @forelse ($analysis['highlights'] as $highlight)
                            <div class="rounded-xl bg-slate-50 px-4 py-3">{{ $highlight }}</div>
                        @empty
                            <p class="text-slate-400">Tiada komen untuk diringkaskan.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="rounded-2xl bg-white p-6 shadow-lg ring-1 ring-slate-200">
                    <h3 class="text-base font-semibold text-slate-900">Tema Utama</h3>
                    <p class="mt-1 text-sm text-slate-500">Topik yang kerap disebut dalam semua komen.</p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        @forelse ($analysis['themes'] as $theme)
                            <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">{{ $theme }}</span>
                        @empty
                            <span class="text-sm text-slate-400">Tiada tema dikesan.</span>
                        @endforelse
                    </div>
                </div>
                <div class="rounded-2xl bg-white p-6 shadow-lg ring-1 ring-slate-200">
                    <h3 class="text-base font-semibold text-slate-900">Isu Berulang</h3>
                    <p class="mt-1 text-sm text-slate-500">Isu yang banyak muncul dalam maklum balas negatif.</p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        @forelse ($analysis['issues'] as $issue)
                            <span class="rounded-full bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700">{{ $issue }}</span>
                        @empty
                            <span class="text-sm text-slate-400">Tiada isu ketara dikesan.</span>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl bg-white shadow-lg ring-1 ring-slate-200">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-6 py-4">
                    <h2 class="text-lg font-semibold text-slate-900">Latest Feedback</h2>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('admin.subjects.index') }}"
                            class="inline-flex items-center rounded-full bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700 hover:bg-sky-100">Subjects</a>
                        <a href="{{ route('admin.classrooms.index') }}"
                            class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 hover:bg-emerald-100">Classes</a>
                        <a href="/feedback"
                            class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">Back
                            to Form</a>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                        <thead class="bg-slate-100 text-slate-600">
                            <tr>
                                <th class="px-6 py-3 font-semibold">Subject</th>
                                <th class="px-6 py-3 font-semibold">Rating</th>
                                <th class="px-6 py-3 font-semibold">Sentiment</th>
                                <th class="px-6 py-3 font-semibold">Comment</th>
                                <th class="px-6 py-3 font-semibold">Anonymous</th>
                                <th class="px-6 py-3 font-semibold">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($feedbacks as $f)
                                <tr class="text-slate-700">
                                    <td class="whitespace-nowrap px-6 py-4 font-medium text-slate-900">{{ $f->subject }}</td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700">
                                            {{ $f->rating }} / 5
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold
                                            @if ($f->sentiment === 'positive')
                                                bg-emerald-50 text-emerald-700
                                            @elseif ($f->sentiment === 'negative')
                                                bg-rose-50 text-rose-700
                                            @else
                                                bg-slate-100 text-slate-700
                                            @endif">
                                            {{ ucfirst($f->sentiment) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600">
                                        <p class="line-clamp-2">{{ $f->comments }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $f->is_anonymous ? 'bg-slate-100 text-slate-700' : 'bg-emerald-50 text-emerald-700' }}">
                                            {{ $f->is_anonymous ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-500">{{ $f->created_at }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-sm text-slate-500">
                                        No feedback has been submitted yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
