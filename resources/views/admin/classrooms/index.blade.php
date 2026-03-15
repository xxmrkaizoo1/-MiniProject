<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - Classes</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 font-sans text-slate-900">
    <div class="min-h-screen px-4 py-12 sm:px-6 lg:px-8">
        <div class="mx-auto flex max-w-6xl flex-col gap-8">
            <header class="flex flex-col gap-4 text-center sm:text-left">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">Class Management</p>
                    <h1 class="text-3xl font-bold text-slate-900 sm:text-4xl">Manage Classes</h1>
                    <p class="text-base text-slate-600">Create new classes, assign lecturers, and enroll students.</p>
                </div>
                <div class="flex flex-wrap items-center justify-center gap-3 sm:justify-start">
                    <a href="/admin/feedback"
                        class="inline-flex items-center rounded-full bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        Feedback
                    </a>
                    <a href="{{ route('admin.subjects.index') }}"
                        class="inline-flex items-center rounded-full bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        Subjects
                    </a>
                    <a href="{{ route('admin.classrooms.index') }}"
                        class="inline-flex items-center rounded-full bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        Classes
                    </a>
                    <a href="/feedback"
                        class="inline-flex items-center rounded-full bg-slate-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-200">
                        Back to Form
                    </a>
                </div>
            </header>

            @if (session('success'))
                <div
                    class="rounded-2xl border border-indigo-200 bg-indigo-50 px-6 py-4 text-sm font-medium text-indigo-800">
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

            <div class="grid gap-6 lg:grid-cols-2">
                <section class="rounded-2xl bg-white p-6 shadow-lg ring-1 ring-slate-200">
                    <h2 class="text-lg font-semibold text-slate-900">Create a class</h2>
                    <p class="mt-1 text-sm text-slate-500">Link each class to a subject and optional lecturer.</p>

                    <form method="POST" action="{{ route('admin.classrooms.store') }}" class="mt-6 space-y-5">
                        @csrf
                        <div>
                            <label class="text-sm font-medium text-slate-700" for="name">Class Name</label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required
                                class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700" for="subject_id">Subject</label>
                            <select id="subject_id" name="subject_id" required
                                class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                <option value="">Select subject</option>
                                @foreach ($subjects as $subject)
                                    <option value="{{ $subject->id }}"
                                        {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->code }} - {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700" for="lecturer_id">Lecturer</label>
                            <select id="lecturer_id" name="lecturer_id"
                                class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                <option value="">Not assigned</option>
                                @foreach ($students as $assignableUser)
                                    <option value="{{ $assignableUser->id }}"
                                        {{ old('lecturer_id') == $assignableUser->id ? 'selected' : '' }}>
                                        {{ $assignableUser->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit"
                            class="inline-flex w-full items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            Add Class
                        </button>
                    </form>
                </section>

                <section class="rounded-2xl bg-white p-6 shadow-lg ring-1 ring-slate-200">
                    <h2 class="text-lg font-semibold text-slate-900">Assign student</h2>
                    <p class="mt-1 text-sm text-slate-500">Enroll students quickly into existing classes.</p>

                    <form method="POST" action="{{ route('admin.classrooms.enrollments.store') }}"
                        class="mt-6 space-y-5">
                        @csrf
                        <div>
                            <label class="text-sm font-medium text-slate-700" for="enrollment_subject_id">Subject</label>
                            <select id="enrollment_subject_id" name="enrollment_subject_id" required
                                class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                <option value="">Select subject</option>
                                @foreach ($subjects as $subject)
                                    <option value="{{ $subject->id }}"
                                        {{ old('enrollment_subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->code }} - {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700" for="classroom_id">Class</label>
                            <select id="classroom_id" name="classroom_id" required
                                class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                <option value="">Select class</option>
                                @foreach ($classrooms as $classroom)
                                    <option value="{{ $classroom->id }}"
                                        {{ old('classroom_id') == $classroom->id ? 'selected' : '' }}>
                                        {{ $classroom->name }} ({{ $classroom->subject?->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700" for="student_id">Student</label>
                            <select id="student_id" name="student_id" required
                                class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                <option value="">Select student</option>
                                @foreach ($students as $student)
                                    <option value="{{ $student->id }}"
                                        {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                        {{ $student->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit"
                            class="inline-flex w-full items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-300">
                            Assign Student
                        </button>
                    </form>
                </section>

            </div>

            <section class="overflow-hidden rounded-2xl bg-white shadow-lg ring-1 ring-slate-200">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-6 py-4">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Existing classes</h2>
                        <p class="text-sm text-slate-500">Review class sizes and lecturer assignments.</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('admin.subjects.index') }}"
                            class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">Subjects</a>
                        <a href="/admin/feedback"
                            class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">Feedback</a>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                        <thead class="bg-slate-100 text-slate-600">
                            <tr>
                                <th class="px-6 py-3 font-semibold">Class</th>
                                <th class="px-6 py-3 font-semibold">Subject</th>
                                <th class="px-6 py-3 font-semibold">Lecturer</th>
                                <th class="px-6 py-3 font-semibold">Students</th>
                                <th class="px-6 py-3 text-right font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($classrooms as $classroom)
                                <tr class="text-slate-700">
                                    <td class="whitespace-nowrap px-6 py-4 font-medium text-slate-900">
                                        {{ $classroom->name }}</td>
                                    <td class="px-6 py-4 text-slate-600">{{ $classroom->subject?->code }} -
                                        {{ $classroom->subject?->name }}</td>
                                    <td class="px-6 py-4 text-slate-600">
                                        {{ $classroom->lecturer?->name ?? 'Not assigned' }}</td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700">
                                            {{ $classroom->enrollments->count() }} students
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <form method="POST"
                                            action="{{ route('admin.classrooms.destroy', $classroom) }}"
                                            onsubmit="return confirm('Delete this class? This will also remove student enrollments for it.');"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center rounded-lg bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 transition hover:bg-rose-100 focus:outline-none focus:ring-2 focus:ring-rose-200">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-sm text-slate-500">
                                        No classes have been created yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</body>

</html>
