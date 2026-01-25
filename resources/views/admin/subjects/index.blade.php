<!doctype html>
<html>

<head>
    <title>Admin - Subjects</title>
</head>

<body>
    <h2>Subjects</h2>

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

    <form method="POST" action="{{ route('admin.subjects.store') }}">
        @csrf
        <label>Subject Code</label><br>
        <input type="text" name="code" value="{{ old('code') }}" required><br><br>

        <label>Subject Name</label><br>
        <input type="text" name="name" value="{{ old('name') }}" required><br><br>

        <button type="submit">Add Subject</button>
    </form>

    <h3>Existing Subjects</h3>
    <table border="1" cellpadding="8">
        <tr>
            <th>Code</th>
            <th>Name</th>
        </tr>
        @foreach ($subjects as $subject)
            <tr>
                <td>{{ $subject->code }}</td>
                <td>{{ $subject->name }}</td>
            </tr>
        @endforeach
    </table>

    <p><a href="{{ route('admin.classrooms.index') }}">Manage Classes</a></p>
    <p><a href="/admin/feedback">Back to Feedback</a></p>
</body>

</html>
