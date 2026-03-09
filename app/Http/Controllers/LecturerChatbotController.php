<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Feedback;
use App\Models\Subject;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class LecturerChatbotController extends Controller
{
    public function dashboard(Request $request)
    {
        $classrooms = Classroom::with('subject')
            ->withCount('enrollments')
            ->where('lecturer_id', auth()->id())
            ->orderBy('name')
            ->get();

        $subjects = Subject::whereIn('id', $classrooms->pluck('subject_id')->filter())
            ->orderBy('name')
            ->get();

        $selectedSubjectId = $request->query('subject_id');
        $selectedSubject = $selectedSubjectId ? $subjects->firstWhere('id', (int) $selectedSubjectId) : null;
        $subjectNames = $selectedSubject ? collect([$selectedSubject->name]) : $subjects->pluck('name');
        $feedbackQuery = Feedback::query();
        if ($subjectNames->isNotEmpty()) {
            $feedbackQuery->whereIn('subject', $subjectNames);
        }

        $totalFeedback = (clone $feedbackQuery)->count();
        $avgRating = (clone $feedbackQuery)->avg('rating');
        $negativeCount = (clone $feedbackQuery)->where('rating', '<=', 2)->count();
        $negativeRatio = $totalFeedback > 0 ? round(($negativeCount / $totalFeedback) * 100) : 0;

        $notification = null;
        if ($totalFeedback > 0 && ($avgRating < 3 || $negativeRatio >= 30)) {
            $notification = [
                'title' => 'Tindakan diperlukan',
                'message' => 'Maklum balas menunjukkan isu berulang. Pertimbangkan tindakan susulan untuk kelas minggu ini.',
            ];
        }

        return view('dashboard', [
            'classrooms' => $classrooms,
            'subjects' => $subjects,
            'selectedSubject' => $selectedSubject,
            'avgRating' => $avgRating,
            'negativeCount' => $negativeCount,
            'totalFeedback' => $totalFeedback,
            'negativeRatio' => $negativeRatio,
            'notification' => $notification,
            'ollamaStatus' => $this->getOllamaStatus(),
        ]);
    }

    public function respond(Request $request)
    {
        $classes = Classroom::where('lecturer_id', auth()->id())->get();

        $validated = $request->validate([
            'classroom_id' => 'nullable|exists:classrooms,id',
            'subject_id' => 'required|exists:subjects,id',
            'prompt' => 'nullable|string|max:500',
        ]);

        if ($validated['classroom_id'] && ! $classes->pluck('id')->contains($validated['classroom_id'])) {
            return back()->withErrors(['classroom_id' => 'Selected class is not assigned to you.']);
        }

        $classroom = $validated['classroom_id']
            ? $classes->firstWhere('id', $validated['classroom_id'])
            : null;

        $subject = Subject::find($validated['subject_id']);
        $prompt = $validated['prompt'] ?? '';
        $insights = $this->buildFeedbackInsights($subject, $classroom);

        $status = $this->getOllamaStatus();
        $ollamaResponse = $status['connected']
            ? $this->generateOllamaResponse($subject, $classroom, $prompt, $insights)
            : null;

        if ($ollamaResponse) {
            $response = $ollamaResponse;
        } else {
            $fallbackMessage = $this->buildFallbackResponse($subject, $classroom, $prompt, $insights);
            if (! $status['connected']) {
                $response = "Ollama is not connected right now ({$status['message']}).\n\n{$fallbackMessage}";
            } else {
                $response = $fallbackMessage;
            }
        }

        return back()->with('chatbot_response', $response);
    }

    private function generateOllamaResponse(
        Subject $subject,
        ?Classroom $classroom,
        string $prompt,
        array $insights
    ): ?string {
        $baseUrl = rtrim((string) config('services.ollama.base_url'), '/');
        $model = (string) config('services.ollama.model');

        if ($baseUrl === '' || $model === '') {
            return null;
        }

        $classroomName = $classroom?->name;
        $systemPrompt = 'You are a helpful teaching assistant for lecturers. Use lecturer notes + feedback statistics as primary evidence. Provide concise, actionable advice in 4-6 sentences with priorities.';
        $themesLine = $this->formatList($insights['themes'], 'none yet');
        $issuesLine = $this->formatList($insights['issues'], 'none yet');
        $highlightsLine = $this->formatList($insights['highlights'], 'none yet', ' | ');
        $context = collect([
            "Subject: {$subject->name}.",
            $classroomName ? "Classroom: {$classroomName}." : 'Classroom: not specified.',
            $prompt !== '' ? "Lecturer note: {$prompt}." : null,
            $insights['summary'],
            'Rating distribution (1-5): ' . $this->formatRatingDistribution($insights['ratingDistribution'] ?? []),
            "Low-rating feedback (1-2 stars): {$insights['lowRatingCount']} ({$insights['lowRatingRatio']}%).",
            "Common themes: {$themesLine}.",
            "Top issues: {$issuesLine}.",
            'Most frequent bad-comment keywords: ' . $this->formatList($insights['badCommentThemes'] ?? [], 'none yet') . '.',
            'Worst comments (lowest ratings first): ' . $this->formatList($insights['worstComments'] ?? [], 'none yet', ' | ') . '.',
            'Priority action plan (notes + stats): ' . $this->buildActionPlanFromInsights($insights, $prompt) . '.',
            "Sample comments: {$highlightsLine}.",
        ])->filter()->implode("\n");

        $payload = [
            'model' => $model,
            'prompt' => "{$systemPrompt}\n{$context}",
            'stream' => false,
            'options' => [
                'temperature' => (float) config('services.ollama.temperature', 0.4),
            ],
        ];

        $timeout = (int) config('services.ollama.timeout', 10);
        try {
            $response = Http::timeout($timeout)->post("{$baseUrl}/api/generate", $payload);
        } catch (ConnectionException) {
            return null;
        }

        if (! $response->ok()) {
            return null;
        }

        $generated = trim((string) $response->json('response'));

        return $generated !== '' ? $generated : null;
    }

    private function getOllamaStatus(): array
    {
        $baseUrl = rtrim((string) config('services.ollama.base_url'), '/');
        $model = (string) config('services.ollama.model');

        if ($baseUrl === '' || $model === '') {
            return [
                'connected' => false,
                'message' => 'missing OLLAMA_BASE_URL or OLLAMA_MODEL',
            ];
        }

        $cacheKey = 'ollama_status_' . md5($baseUrl . '|' . $model);

        return Cache::remember($cacheKey, now()->addSeconds(20), function () use ($baseUrl, $model) {
            try {
                $response = Http::timeout(2)->get("{$baseUrl}/api/tags");
            } catch (ConnectionException) {
                return [
                    'connected' => false,
                    'message' => 'cannot reach Ollama server',
                ];
            }

            if (! $response->ok()) {
                return [
                    'connected' => false,
                    'message' => 'Ollama server returned an error',
                ];
            }

            $models = collect($response->json('models', []))
                ->pluck('name')
                ->filter()
                ->values();

            if (! $models->contains($model)) {
                return [
                    'connected' => false,
                    'message' => "model {$model} is not pulled",
                ];
            }

            return [
                'connected' => true,
                'message' => "connected to {$model}",
            ];
        });
    }

    private function buildFeedbackInsights(Subject $subject, ?Classroom $classroom): array
    {
        $since = now()->subDays(30);
        $feedbackQuery = Feedback::query()
            ->where('subject', $subject->name)
            ->where('created_at', '>=', $since);

        $classroomName = $classroom?->name;
        if ($classroom) {
            $studentIds = $classroom->enrollments()->pluck('student_id');

            if ($studentIds->isEmpty()) {
                $feedbackQuery->whereRaw('1 = 0');
            } else {
                $feedbackQuery->whereIn('user_id', $studentIds->all());
            }
        }

        $feedbacks = $feedbackQuery->get(['rating', 'comments', 'created_at']);

        if ($feedbacks->isEmpty()) {
            $summary = sprintf(
                'Last 30 days: 0 feedback items for %s.',
                $subject->name
            );

            if ($classroomName) {
                $summary .= " Classroom selected: {$classroomName} (class + subject scoped feedback).";
            }

            return [
                'summary' => $summary,
                'themes' => [],
                'issues' => [],
                'highlights' => [],
                'badCommentThemes' => [],
                'worstComments' => [],
                'avgRating' => null,
                'positiveRatio' => 0,
                'negativeRatio' => 0,
                'lowRatingCount' => 0,
                'lowRatingRatio' => 0,
                'ratingDistribution' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0],
            ];
        }

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
        $badCommentKeywords = [];
        $highlightComments = [];
        $ratingDistribution = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        $lowRatingCount = 0;

        foreach ($feedbacks as $feedback) {
            $comment = trim((string) $feedback->comments);
            $rating = (int) $feedback->rating;
            if ($rating >= 1 && $rating <= 5) {
                $ratingDistribution[$rating]++;
            }

            $sentiment = $this->inferSentiment($feedback->rating, $comment, $positiveKeywords, $negativeKeywords);
            $sentimentCounts[$sentiment]++;

            if ($rating <= 2) {
                $lowRatingCount++;
            }

            if ($comment !== '') {
                $highlightComments[] = $comment;
                $tokens = $this->extractKeywords($comment, $stopwords);
                foreach ($tokens as $token) {
                    $allKeywords[$token] = ($allKeywords[$token] ?? 0) + 1;
                }
                if ($sentiment === 'negative' || $rating <= 2) {
                    foreach ($tokens as $token) {
                        $issueKeywords[$token] = ($issueKeywords[$token] ?? 0) + 1;
                        $badCommentKeywords[$token] = ($badCommentKeywords[$token] ?? 0) + 1;
                    }
                }
            }
        }

        arsort($allKeywords);
        arsort($issueKeywords);
        arsort($badCommentKeywords);

        $topThemes = array_slice(array_keys($allKeywords), 0, 5);
        $topIssues = array_slice(array_keys($issueKeywords), 0, 5);
        $topBadCommentThemes = array_slice(array_keys($badCommentKeywords), 0, 5);
        $topHighlights = array_slice($highlightComments, 0, 3);
        $worstComments = $feedbacks
            ->filter(fn($feedback) => trim((string) $feedback->comments) !== '')
            ->sortBy([
                ['rating', 'asc'],
                ['created_at', 'desc'],
            ])
            ->take(3)
            ->map(fn($feedback) => trim((string) $feedback->comments))
            ->values()
            ->all();

        $avgRating = $feedbacks->avg('rating');
        $total = max($feedbacks->count(), 1);
        $positiveRatio = round(($sentimentCounts['positive'] / $total) * 100);
        $negativeRatio = round(($sentimentCounts['negative'] / $total) * 100);
        $lowRatingRatio = round(($lowRatingCount / $total) * 100);

        $summary = sprintf(
            'Last 30 days: %s feedback items for %s. Avg rating %s/5. %s%% positive, %s%% negative, %s%% low rating (1-2 stars).',
            $feedbacks->count(),
            $subject->name,
            $avgRating ? number_format($avgRating, 2) : '0.00',
            $positiveRatio,
            $negativeRatio,
            $lowRatingRatio
        );

        if ($classroomName) {
            $summary .= " Classroom selected: {$classroomName} (class + subject scoped feedback).";
        }

        return [
            'summary' => $summary,
            'themes' => $topThemes,
            'issues' => $topIssues,
            'highlights' => $topHighlights,
            'badCommentThemes' => $topBadCommentThemes,
            'worstComments' => $worstComments,
            'avgRating' => $avgRating,
            'positiveRatio' => $positiveRatio,
            'negativeRatio' => $negativeRatio,
            'lowRatingCount' => $lowRatingCount,
            'lowRatingRatio' => $lowRatingRatio,
            'ratingDistribution' => $ratingDistribution,
        ];
    }

    private function buildFallbackResponse(
        Subject $subject,
        ?Classroom $classroom,
        string $prompt,
        array $insights
    ): string {
        $lines = [
            'Overview',
            "- Subject: {$subject->name}",
            $classroom ? "- Class: {$classroom->name}" : '- Class: not specified',
            "- {$insights['summary']}",
            '',
            'Themes & Issues',
            '- Common themes: ' . $this->formatList($insights['themes'], 'none yet'),
            '- Top issues: ' . $this->formatList($insights['issues'], 'none yet'),
            '- Rating distribution (1-5): ' . $this->formatRatingDistribution($insights['ratingDistribution'] ?? []),
            "- Low-rating feedback (1-2 stars): {$insights['lowRatingCount']} ({$insights['lowRatingRatio']}%)",
            '- Frequent bad-comment keywords: ' . $this->formatList($insights['badCommentThemes'] ?? [], 'none yet'),
            '',
        ];

        $action = $this->buildActionPlanFromInsights($insights, $prompt);
        $lines[] = 'Action';
        $lines[] = "- {$action}";
        $lines[] = '';
        $lines[] = 'Sample comments';
        $lines[] = '- ' . $this->formatList($insights['highlights'], 'none yet', "\n- ");
        $lines[] = '';
        $lines[] = 'Lowest-rating comments';
        $lines[] = '- ' . $this->formatList($insights['worstComments'] ?? [], 'none yet', "\n- ");
        $lines[] = '';
        $lines[] = $prompt ? "Lecturer note: \"{$prompt}\"." : null;

        return collect($lines)->filter(fn($line) => $line !== null)->implode("\n");
    }
    private function buildActionPlanFromInsights(array $insights, string $lecturerNote): string
    {
        $actions = [];

        if (trim($lecturerNote) !== '') {
            $actions[] = sprintf('Prioritize lecturer note: %s', trim($lecturerNote));
        }

        $negativeRatio = (int) ($insights['negativeRatio'] ?? 0);
        $lowRatingRatio = (int) ($insights['lowRatingRatio'] ?? 0);
        $topIssue = $insights['issues'][0] ?? null;

        if ($negativeRatio >= 30 || $lowRatingRatio >= 30) {
            $issuePart = $topIssue ? sprintf(' focusing on "%s"', $topIssue) : '';
            $actions[] = 'Address highest-risk feedback first' . $issuePart . ', slow pacing, and run a quick understanding check.';
        } elseif ((float) ($insights['avgRating'] ?? 0) >= 4) {
            $actions[] = 'Preserve high-performing methods and ask students for one improvement suggestion.';
        } else {
            $actions[] = 'Tighten lesson structure, add a short active-learning task, and end with recap questions.';
        }

        if (! empty($insights['badCommentThemes'] ?? [])) {
            $actions[] = 'Target recurring bad-comment keywords: ' . implode(', ', array_slice($insights['badCommentThemes'], 0, 3)) . '.';
        }

        return 'Action: ' . implode(' ', $actions);
    }

    private function formatList(array $items, string $emptyValue, string $separator = ', '): string
    {
        if ($items === []) {
            return $emptyValue;
        }

        return implode($separator, $items);
    }

    private function formatRatingDistribution(array $distribution): string
    {
        $normalized = [];

        foreach ([1, 2, 3, 4, 5] as $rating) {
            $normalized[] = sprintf('%s★:%s', $rating, $distribution[$rating] ?? 0);
        }

        return implode(', ', $normalized);
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
