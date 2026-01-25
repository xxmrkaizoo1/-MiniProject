<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback;
use App\Models\Subject;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class FeedbackController extends Controller
{
    public function create()
    {
        $user = auth()->user();
        $subjects = Subject::whereHas('classrooms.enrollments', function ($query) use ($user) {
            $query->where('student_id', $user->id);
        })
            ->orderBy('name')
            ->get();

        return view('feedback.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $allowedSubjects = Subject::whereHas('classrooms.enrollments', function ($query) use ($user) {
            $query->where('student_id', $user->id);
        })
            ->pluck('name');

        $validated = $request->validate([
            'subject' => ['required', 'string', Rule::in($allowedSubjects->all())],
            'rating'  => 'required|integer|min:1|max:5',
            'mood_rating' => 'required|integer|min:1|max:5',
            'comments' => 'nullable|string|max:1000',
            'is_anonymous' => 'nullable|boolean',
        ]);

        Feedback::create([
            'subject' => $validated['subject'],
            'rating' => $validated['rating'],
            'mood_rating' => $validated['mood_rating'],
            'comments' => $validated['comments'] ?? null,
            'is_anonymous' => $request->has('is_anonymous'),
            'user_id' => auth()->id(),
        ]);

        return redirect('/feedback')->with('success', 'Feedback submitted!');
    }

    public function index(Request $request)
    {
        $subject = $request->query('subject');

        $query = Feedback::query();

        if ($subject) {
            $query->where('subject', $subject);
        }

        $feedbacks = $query->latest()->get();
        $avgRating = (clone $query)->avg('rating');

        $subjects = Subject::orderBy('name')->pluck('name');

        $analysis = $this->buildAiAnalysis($feedbacks, $avgRating);

        return view('admin.index', compact('feedbacks', 'avgRating', 'subjects', 'subject', 'analysis'));
    }

    public function export(Request $request, string $format)
    {
        $format = Str::lower($format);

        if (! in_array($format, ['pdf', 'excel'], true)) {
            abort(404);
        }

        $period = $request->query('period', 'weekly');
        $subject = $request->query('subject');

        $startDate = match ($period) {
            'monthly' => Carbon::now()->subMonth(),
            default => Carbon::now()->subDays(7),
        };

        $query = Feedback::query()->where('created_at', '>=', $startDate);

        if ($subject) {
            $query->where('subject', $subject);
        }

        $feedbacks = $query->latest()->get();
        $avgRating = $feedbacks->avg('rating');
        $periodLabel = $period === 'monthly' ? 'Bulanan' : 'Mingguan';
        $subjectLabel = $subject ?: 'Semua Subjek';
        $timestamp = Carbon::now()->format('Ymd_His');

        if ($format === 'pdf') {
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('admin.feedback.report', [
                'feedbacks' => $feedbacks,
                'avgRating' => $avgRating,
                'periodLabel' => $periodLabel,
                'subjectLabel' => $subjectLabel,
                'startDate' => $startDate,
            ]);

            return $pdf->download("laporan_feedback_{$period}_{$timestamp}.pdf");
        }

        $filename = "laporan_feedback_{$period}_{$timestamp}.csv";

        return response()->streamDownload(function () use ($feedbacks) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Subject', 'Rating', 'Mood', 'Comment', 'Anonymous', 'Date']);

            foreach ($feedbacks as $feedback) {
                fputcsv($handle, [
                    $feedback->subject,
                    $feedback->rating,
                    $feedback->mood_rating,
                    $feedback->comments,
                    $feedback->is_anonymous ? 'Yes' : 'No',
                    optional($feedback->created_at)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function buildAiAnalysis($feedbacks, $avgRating): array
    {
        $positiveKeywords = [
            'bagus',
            'terbaik',
            'mantap',
            'hebat',
            'puas',
            'baik',
            'menarik',
            'jelas',
            'efektif',
            'suka',
            'love',
            'great',
            'excellent',
            'good',
            'helpful',
            'clear',
            'awesome',
        ];
        $negativeKeywords = [
            'teruk',
            'buruk',
            'lemah',
            'bosan',
            'mengelirukan',
            'sukar',
            'lambat',
            'delay',
            'bad',
            'poor',
            'confusing',
            'hard',
            'difficult',
            'slow',
            'worst',
            'tidak puas',
            'tak puas',
        ];
        $stopwords = [
            'dan',
            'yang',
            'untuk',
            'pada',
            'dengan',
            'ini',
            'itu',
            'adalah',
            'saya',
            'kami',
            'kita',
            'the',
            'a',
            'an',
            'to',
            'of',
            'in',
            'is',
            'are',
            'was',
            'were',
            'be',
            'been',
            'this',
            'that',
            'for',
            'with',
            'it',
            'as',
            'by',
            'at',
            'or',
            'from',
            'so',
            'very',
            'lebih',
            'kurang',
            'boleh',
            'tidak',
            'tak',
            'pun',
            'lah',
        ];

        $sentimentCounts = [
            'positive' => 0,
            'neutral' => 0,
            'negative' => 0,
        ];
        $allKeywords = [];
        $issueKeywords = [];
        $summaryComments = [];

        foreach ($feedbacks as $feedback) {
            $comment = trim((string) $feedback->comments);
            $sentiment = $this->inferSentiment($feedback->rating, $comment, $positiveKeywords, $negativeKeywords);
            $feedback->setAttribute('sentiment', $sentiment);
            $sentimentCounts[$sentiment]++;

            if ($comment !== '') {
                $summaryComments[] = $comment;
                $tokens = $this->extractKeywords($comment, $stopwords);
                foreach ($tokens as $token) {
                    $allKeywords[$token] = ($allKeywords[$token] ?? 0) + 1;
                }
                if ($sentiment === 'negative') {
                    foreach ($tokens as $token) {
                        $issueKeywords[$token] = ($issueKeywords[$token] ?? 0) + 1;
                    }
                }
            }
        }

        arsort($allKeywords);
        arsort($issueKeywords);

        $topThemes = array_slice(array_keys($allKeywords), 0, 5);
        $topIssues = array_slice(array_keys($issueKeywords), 0, 5);
        $highlightComments = array_slice($summaryComments, 0, 3);

        $totalFeedback = max($feedbacks->count(), 1);
        $positiveRatio = round(($sentimentCounts['positive'] / $totalFeedback) * 100);
        $negativeRatio = round(($sentimentCounts['negative'] / $totalFeedback) * 100);

        $summary = sprintf(
            'Majoriti %s%% positif, %s%% negatif. Purata rating %s/5.',
            $positiveRatio,
            $negativeRatio,
            $avgRating ? number_format($avgRating, 2) : '0.00'
        );

        return [
            'sentimentCounts' => $sentimentCounts,
            'summary' => $summary,
            'themes' => $topThemes,
            'issues' => $topIssues,
            'highlights' => $highlightComments,
        ];
    }

    private function inferSentiment(int $rating, string $comment, array $positiveKeywords, array $negativeKeywords): string
    {
        $score = 0;
        if ($rating >= 4) {
            $score += 2;
        } elseif ($rating <= 2) {
            $score -= 2;
        }

        $lowerComment = Str::lower($comment);

        foreach ($positiveKeywords as $keyword) {
            if ($keyword !== '' && Str::contains($lowerComment, $keyword)) {
                $score++;
            }
        }

        foreach ($negativeKeywords as $keyword) {
            if ($keyword !== '' && Str::contains($lowerComment, $keyword)) {
                $score--;
            }
        }

        if ($score >= 1) {
            return 'positive';
        }
        if ($score <= -1) {
            return 'negative';
        }

        return 'neutral';
    }

    private function extractKeywords(string $comment, array $stopwords): array
    {
        $clean = preg_replace('/[^\pL\pN\s]+/u', ' ', $comment);
        $tokens = preg_split('/\s+/', Str::lower($clean), -1, PREG_SPLIT_NO_EMPTY);
        $filtered = [];

        foreach ($tokens as $token) {
            if (mb_strlen($token) < 3) {
                continue;
            }
            if (in_array($token, $stopwords, true)) {
                continue;
            }
            $filtered[] = $token;
        }

        return $filtered;
    }
}
