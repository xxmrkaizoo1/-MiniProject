<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - Subjects</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 font-sans text-slate-900">
    <div class="min-h-screen px-4 py-12 sm:px-6 lg:px-8">
        <div class="mx-auto flex max-w-6xl flex-col gap-8">
            <header class="flex flex-col gap-4 text-center sm:text-left">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">Subject Catalog</p>
                    <h1 class="text-3xl font-bold text-slate-900 sm:text-4xl">Manage Subjects</h1>
                    <p class="text-base text-slate-600">Create new subjects and keep the list organized for class setup.</p>
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
            </header>

            @if (session('success'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-6 py-4 text-sm font-medium text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-2xl border border-rose-200 bg-rose-50 px-6 py-4 text-sm text-rose-700">
                    <p class="font-semibold">Please fix the following:</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,2fr)]">
                <section class="rounded-2xl bg-white p-6 shadow-lg ring-1 ring-slate-200">
                    <h2 class="text-lg font-semibold text-slate-900">Add a subject</h2>
                    <p class="mt-1 text-sm text-slate-500">Keep codes consistent with your official catalog.</p>

                    <form method="POST" action="{{ route('admin.subjects.store') }}" class="mt-6 space-y-5">
                        @csrf
                        <div>
                            <label class="text-sm font-medium text-slate-700" for="code">Subject Code</label>
                            <input id="code" type="text" name="code" value="{{ old('code') }}" required
                                class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700" for="name">Subject Name</label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required
                                class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200">
                        </div>
                        <button type="submit"
                            class="inline-flex w-full items-center justify-center rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-300">
                            Add Subject
                        </button>
                    </form>
                </section>

                <section class="overflow-hidden rounded-2xl bg-white shadow-lg ring-1 ring-slate-200">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-6 py-4">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">Existing subjects</h2>
                            <p class="text-sm text-slate-500">Manage the current list used in class creation.</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('admin.classrooms.index') }}"
                                class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 hover:bg-emerald-100">Classes</a>
                            <a href="/admin/feedback"
                                class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">Feedback</a>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                            <thead class="bg-slate-100 text-slate-600">
                                <tr>
                                    <th class="px-6 py-3 font-semibold">Code</th>
                                    <th class="px-6 py-3 font-semibold">Name</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($subjects as $subject)
                                    <tr class="text-slate-700">
                                        <td class="whitespace-nowrap px-6 py-4 font-medium text-slate-900">{{ $subject->code }}</td>
                                        <td class="px-6 py-4 text-slate-600">{{ $subject->name }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="px-6 py-10 text-center text-sm text-slate-500">
                                            No subjects have been added yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </div>
</body>

</html>
