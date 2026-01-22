<!doctype html>
<html>
<head>
    <title>Submit Feedback</title>
</head>
<body>
    <h2>Student Feedback Form</h2>

    @if(session('success'))
        <p style="color:green;">{{ session('success') }}</p>
    @endif

    @if($errors->any())
        <ul style="color:red;">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="/feedback">
        @csrf

        <label>Subject</label><br>
        <input type="text" name="subject" value="{{ old('subject') }}" required><br><br>

        <label>Rating (1-5)</label><br>
        <input type="number" name="rating" min="1" max="5" value="{{ old('rating') }}" required><br><br>

        <label>Comment</label><br>
        <textarea name="comment" rows="4">{{ old('comment') }}</textarea><br><br>

        <label>
            <input type="checkbox" name="is_anonymous" value="1">
            Submit as anonymous
        </label><br><br>

        <button type="submit">Send</button>
    </form>

    <p><a href="/admin/feedback">View Admin Page</a></p>
</body>
</html>
