<!doctype html>
<html>
<head>
    <title>Admin - Feedback List</title>
</head>
<body>
    <h2>Admin Feedback List</h2>

    <table border="1" cellpadding="8">
        <tr>
            <th>Subject</th>
            <th>Rating</th>
            <th>Comment</th>
            <th>Anonymous?</th>
            <th>Date</th>
        </tr>

        @foreach($feedbacks as $f)
        <tr>
            <td>{{ $f->subject }}</td>
            <td>{{ $f->rating }}</td>
            <td>{{ $f->comment }}</td>
            <td>{{ $f->is_anonymous ? 'Yes' : 'No' }}</td>
            <td>{{ $f->created_at }}</td>
        </tr>
        @endforeach
    </table>

    <p><a href="/feedback">Back to Form</a></p>
</body>
</html>
