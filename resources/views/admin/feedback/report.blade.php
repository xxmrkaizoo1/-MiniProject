<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Laporan Feedback</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #1f2937;
        }

        h1,
        h2 {
            margin: 0 0 8px;
        }

        .meta {
            margin-bottom: 16px;
        }

        .meta p {
            margin: 2px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #e5e7eb;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #f3f4f6;
        }

        .summary {
            margin-bottom: 16px;
            padding: 10px;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
        }
    </style>
</head>

<body>
    <h1>Laporan Feedback {{ $periodLabel }}</h1>
    <div class="meta">
        <p><strong>Subjek:</strong> {{ $subjectLabel }}</p>
        <p><strong>Tarikh Laporan:</strong> {{ now()->format('d/m/Y H:i') }}</p>
        <p><strong>Tempoh Dari:</strong> {{ $startDate->format('d/m/Y') }}</p>
    </div>

    <div class="summary">
        <p><strong>Purata Rating:</strong> {{ $avgRating ? number_format($avgRating, 2) : '0.00' }}</p>
        <p><strong>Jumlah Feedback:</strong> {{ $feedbacks->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Subject</th>
                <th>Rating</th>
                <th>Mood</th>
                <th>Comment</th>
                <th>Anonymous</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($feedbacks as $feedback)
                <tr>
                    <td>{{ $feedback->subject }}</td>
                    <td>{{ $feedback->rating }} / 5</td>
                    <td>{{ $feedback->mood_rating ?? 3 }} / 5</td>
                    <td>{{ $feedback->comments }}</td>
                    <td>{{ $feedback->is_anonymous ? 'Yes' : 'No' }}</td>
                    <td>{{ optional($feedback->created_at)->format('Y-m-d H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Tiada feedback untuk tempoh ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
