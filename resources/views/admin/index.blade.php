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
        @foreach ($feedbacks as $f)
            <tr>
                <td>{{ $f->subject }}</td>
                <td>{{ $f->rating }}</td>
                <td>{{ $f->comments }}</td>
                <td>{{ $f->is_anonymous ? 'Yes' : 'No' }}</td>
                <td>{{ $f->created_at }}</td>
            </tr>
        @endforeach
    </table>


    <form method="GET" action="/admin/feedback">
        <label>Filter by subject:</label>
        <select name="subject">
            <option value="">All</option>
            @foreach ($subjects as $s)
                <option value="{{ $s }}" {{ $subject === $s ? 'selected' : '' }}>
                    {{ $s }}
                </option>
            @endforeach
        </select>
        <button type="submit">Filter</button>
    </form>

    <p><b>Average rating:</b> {{ $avgRating ? number_format($avgRating, 2) : '0.00' }}</p>
    <hr>



    <p><a href="/feedback">Back to Form</a></p>

</body>

</html>
