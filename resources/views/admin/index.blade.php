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
            <div class="flex flex-col gap-3 text-center sm:text-left">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">Feedback Overview</p>
                <h1 class="text-3xl font-bold text-slate-900 sm:text-4xl">Admin Feedback List</h1>
                <p class="text-base text-slate-600">Review submissions, filter by subject, and track average ratings.</p>
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

            <div class="overflow-hidden rounded-2xl bg-white shadow-lg ring-1 ring-slate-200">
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                    <h2 class="text-lg font-semibold text-slate-900">Latest Feedback</h2>
                    <a href="/feedback" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500">Back to Form</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                        <thead class="bg-slate-100 text-slate-600">
                            <tr>
                                <th class="px-6 py-3 font-semibold">Subject</th>
                                <th class="px-6 py-3 font-semibold">Rating</th>
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
                                        <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700">
                                            {{ $f->rating }} / 5
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
                                    <td colspan="5" class="px-6 py-10 text-center text-sm text-slate-500">
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
