<!doctype html>
<html>

<head>
    <title>Admin - Classes</title>
</head>

<body>
    <h2>Classes</h2>

    @if (session('success'))
        <p style="color:green;">{{ session('success') }}</p>
    @endif

    @if ($errors->any())
        <ul style="color:red;">
            @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('admin.classrooms.store') }}">
        @csrf
        <label>Class Name</label><br>
        <input type="text" name="name" value="{{ old('name') }}" required><br><br>

        <label>Subject</label><br>
        <select name="subject_id" required>
            <option value="">Select subject</option>
            @foreach ($subjects as $subject)
                <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                    {{ $subject->code }} - {{ $subject->name }}
                </option>
            @endforeach
        </select><br><br>

        <label>Lecturer</label><br>
        <select name="lecturer_id">
            <option value="">Not assigned</option>
            @foreach ($lecturers as $lecturer)
                <option value="{{ $lecturer->id }}" {{ old('lecturer_id') == $lecturer->id ? 'selected' : '' }}>
                    {{ $lecturer->name }}
                </option>
            @endforeach
        </select><br><br>

        <button type="submit">Add Class</button>
    </form>

    <h3>Existing Classes</h3>
    <table border="1" cellpadding="8">
        <tr>
            <th>Class</th>
            <th>Subject</th>
            <th>Lecturer</th>
            <th>Students</th>
        </tr>
        @foreach ($classrooms as $classroom)
            <tr>
                <td>{{ $classroom->name }}</td>
                <td>{{ $classroom->subject?->code }} - {{ $classroom->subject?->name }}</td>
                <td>{{ $classroom->lecturer?->name ?? 'Not assigned' }}</td>
                <td>{{ $classroom->enrollments->count() }}</td>
            </tr>
        @endforeach
    </table>

    <h3>Assign Student to Class</h3>
    <form method="POST" action="{{ route('admin.classrooms.enrollments.store') }}">
        @csrf
        <label>Class</label><br>
        <select name="classroom_id" required>
            <option value="">Select class</option>
            @foreach ($classrooms as $classroom)
                <option value="{{ $classroom->id }}" {{ old('classroom_id') == $classroom->id ? 'selected' : '' }}>
                    {{ $classroom->name }} ({{ $classroom->subject?->code }})
                </option>
            @endforeach
        </select><br><br>

        <label>Student</label><br>
        <select name="student_id" required>
            <option value="">Select student</option>
            @foreach ($students as $student)
                <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                    {{ $student->name }}
                </option>
            @endforeach
        </select><br><br>

        <button type="submit">Assign Student</button>
    </form>

    <p><a href="{{ route('admin.subjects.index') }}">Manage Subjects</a></p>
    <p><a href="{{ route('admin.classrooms.index') }}">Manage Classes</a></p>
    <p><a href="/admin/feedback">Back to Feedback</a></p>
</body>

</html>
