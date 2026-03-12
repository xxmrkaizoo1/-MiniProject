<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Feedback;
use App\Models\Subject;
use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class LecturerDashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = auth()->user();

        $classrooms = Classroom::with('subject')
            ->withCount('enrollments')
            ->where('lecturer_id', $user->id)
            ->orderBy('name')
            ->get();

        $subjects = Subject::whereIn('id', $classrooms->pluck('subject_id')->filter())
            ->orderBy('name')
            ->get();

        $selectedSubjectId = $request->query('subject_id');
        $selectedSubject = $selectedSubjectId ? $subjects->firstWhere('id', (int) $selectedSubjectId) : null;
        $subjectNames = $selectedSubject ? collect([$selectedSubject->name]) : $subjects->pluck('name');
        $normalizedSubjectNames = $subjectNames
            ->filter()
            ->map(fn($name) => Str::lower((string) $name))
            ->values();
        $feedbacks = Feedback::query()
            ->when($normalizedSubjectNames->isNotEmpty(), function ($query) use ($normalizedSubjectNames) {
                $placeholders = $normalizedSubjectNames->map(fn() => '?')->implode(',');
                $query->whereRaw("LOWER(subject) IN ({$placeholders})", $normalizedSubjectNames->all());
            }, function ($query) {
                $query->whereRaw('1 = 0');
            })
            ->get(['subject', 'rating', 'mood_rating', 'comments', 'created_at']);

        $avgRating = $feedbacks->avg('rating');
        $totalFeedback = max($feedbacks->count(), 1);

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

        $negativeCount = 0;

        foreach ($feedbacks as $feedback) {
            $isNegative = $feedback->rating < 3;
            $comment = Str::lower(trim((string) $feedback->comments));

            if (! $isNegative && $comment !== '') {
                foreach ($negativeKeywords as $keyword) {
                    if ($keyword !== '' && Str::contains($comment, $keyword)) {
                        $isNegative = true;
                        break;
                    }
                }
            }

            if ($isNegative) {
                $negativeCount++;
            }
        }

        $inferSentiment = function (?int $rating, string $comment) use ($negativeKeywords, $positiveKeywords): string {
            $normalized = Str::lower(trim($comment));
            $score = $rating ?? 0;

            if ($score >= 4) {
                return 'positive';
            }

            if ($score <= 2) {
                return 'negative';
            }

            foreach ($negativeKeywords as $keyword) {
                if ($keyword !== '' && $normalized !== '' && Str::contains($normalized, $keyword)) {
                    return 'negative';
                }
            }

            foreach ($positiveKeywords as $keyword) {
                if ($keyword !== '' && $normalized !== '' && Str::contains($normalized, $keyword)) {
                    return 'positive';
                }
            }

            return 'neutral';
        };

        $now = Carbon::now();
        $referenceDate = $feedbacks->max('created_at');
        $referenceDate = $referenceDate ? Carbon::parse($referenceDate) : $now;
        $ratingTrendMonths = collect(range(5, 0))->map(function (int $offset) use ($referenceDate) {
            return $referenceDate->copy()->subMonths($offset)->startOfMonth();
        });

        $ratingTrendLabels = $ratingTrendMonths->map(fn(Carbon $month) => $month->format('M'));
        $ratingTrendData = $ratingTrendMonths->map(function (Carbon $month) use ($feedbacks) {
            $monthFeedback = $feedbacks->filter(fn($feedback) => $feedback->created_at && $feedback->created_at->isSameMonth($month));
            return round($monthFeedback->avg('rating') ?? 0, 2);
        });

        $currentMonthAverage = $ratingTrendData->last() ?? 0;
        $previousMonthAverage = $ratingTrendData->count() > 1 ? $ratingTrendData->get($ratingTrendData->count() - 2) : 0;
        $ratingMoMChange = $previousMonthAverage > 0
            ? round((($currentMonthAverage - $previousMonthAverage) / $previousMonthAverage) * 100)
            : 0;

        $weeklyDays = collect(range(6, 0))->map(function (int $offset) use ($referenceDate) {
            return $referenceDate->copy()->subDays($offset)->startOfDay();
        });

        $sentimentTrendLabels = $weeklyDays->map(fn(Carbon $day) => $day->format('D'));
        $sentimentTrendData = $weeklyDays->map(function (Carbon $day) use ($feedbacks, $inferSentiment) {
            $dayFeedback = $feedbacks->filter(fn($feedback) => $feedback->created_at && $feedback->created_at->isSameDay($day));
            $total = $dayFeedback->count();

            if ($total === 0) {
                return 0;
            }

            $positiveCount = $dayFeedback->filter(function ($feedback) use ($inferSentiment) {
                return $inferSentiment($feedback->rating, (string) $feedback->comments) === 'positive';
            })->count();

            return round(($positiveCount / $total) * 100);
        });

        $weeklyFeedback = $feedbacks->filter(fn($feedback) => $feedback->created_at && $feedback->created_at->greaterThanOrEqualTo($referenceDate->copy()->subDays(6)->startOfDay()));
        $weeklyTotal = $weeklyFeedback->count();
        $weeklyPositive = $weeklyFeedback->filter(function ($feedback) use ($inferSentiment) {
            return $inferSentiment($feedback->rating, (string) $feedback->comments) === 'positive';
        })->count();
        $weeklyPositiveRate = $weeklyTotal > 0 ? round(($weeklyPositive / $weeklyTotal) * 100) : 0;

        $issueCategories = [
            'Wi-Fi' => [
                'wifi',
                'wi-fi',
                'wi fi',
                'internet',
                'connection',
                'connectivity',
                'network',
                'hotspot',
                'router',
            ],
            'Projector' => [
                'projector',
                'projek',
                'proyektor',
                'display',
                'screen',
                'lcd',
                'hdmi',
                'slide',
                'slides',
            ],
        ];

        $issueCounts = array_fill_keys(array_keys($issueCategories), 0);
        $issueCounts['Other'] = 0;

        foreach ($feedbacks as $feedback) {
            $comment = Str::lower(trim((string) $feedback->comments));
            if ($comment === '') {
                continue;
            }

            $matched = false;
            foreach ($issueCategories as $category => $keywords) {
                foreach ($keywords as $keyword) {
                    if (Str::contains($comment, $keyword)) {
                        $issueCounts[$category]++;
                        $matched = true;
                        break 2;
                    }
                }
            }

            if (! $matched) {
                $issueCounts['Other']++;
            }
        }

        $issueTotal = array_sum($issueCounts);
        $issuePercentages = collect($issueCounts)->map(function ($count) use ($issueTotal) {
            return $issueTotal > 0 ? round(($count / $issueTotal) * 100) : 0;
        });

        $negativeRatio = $totalFeedback > 0 ? round(($negativeCount / $totalFeedback) * 100) : 0;
        $focusAreaAdvice = $this->buildFocusAreaAdvice($subjectNames, $issuePercentages, $negativeRatio);



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
            'feedbacks' => $feedbacks,
            'avgRating' => $avgRating,
            'negativeCount' => $negativeCount,
            'totalFeedback' => $feedbacks->count(),
            'negativeRatio' => $negativeRatio,
            'notification' => $notification,
            'ratingTrendLabels' => $ratingTrendLabels,
            'ratingTrendData' => $ratingTrendData,
            'ratingMoMChange' => $ratingMoMChange,
            'currentMonthAverage' => $currentMonthAverage,
            'weeklyPositiveRate' => $weeklyPositiveRate,
            'sentimentTrendLabels' => $sentimentTrendLabels,
            'sentimentTrendData' => $sentimentTrendData,
            'issueLabels' => $issuePercentages->keys()->values(),
            'issueData' => $issuePercentages->values(),
            'focusAreaAdvice' => $focusAreaAdvice,
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

        $classroomName = $classes->firstWhere('id', $validated['classroom_id'])?->name;
        $subject = Subject::find($validated['subject_id']);
        $prompt = $validated['prompt'] ?? '';
        $insights = $this->buildFeedbackInsights($subject, $classroomName);

        $ollamaStatus = $this->generateOllamaResponse($subject, $classroomName, $prompt, $insights);

        if ($ollamaStatus) {
            $response = $ollamaStatus;
        } else {
            $response = $this->buildFallbackResponse($subject, $classroomName, $prompt, $insights);
        }

        return back()->with('chatbot_response', $response);
    }

    private function generateOllamaResponse(
        Subject $subject,
        ?string $classroomName,
        string $prompt,
        array $insights
    ): ?string {
        $baseUrl = rtrim((string) config('services.ollama.base_url'), '/');
        $model = (string) config('services.ollama.model');

        if ($baseUrl === '' || $model === '') {
            return null;
        }

        $systemPrompt = 'You are a helpful teaching assistant for lecturers. Use lecturer notes + feedback statistics as primary evidence. Provide concise, actionable advice in 4-6 sentences with priorities.';
        $themesLine = $this->formatList($insights['themes'], 'none yet');
        $issuesLine = $this->formatList($insights['issues'], 'none yet');
        $highlightsLine = $this->formatList($insights['highlights'], 'none yet', ' | ');
        $context = collect([
            "Subject: {$subject->name}.",
            $classroomName ? "Classroom: {$classroomName}." : 'Classroom: not specified.',
            $prompt !== '' ? "Lecturer note: {$prompt}." : null,
            $insights['summary'],
            "Common themes: {$themesLine}.",
            "Top issues: {$issuesLine}.",
            'Priority action plan (notes + stats): ' . $this->buildActionPlanFromInsights($insights, $prompt) . '.',
            "Sample comments: {$highlightsLine}.",
        ])->filter()->implode("\n");

        $temperature = (float) config('services.ollama.temperature', 0.4);
        $timeout = max((int) config('services.ollama.timeout', 10), 30);

        $chatPayload = [
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $context],
            ],
            'stream' => false,
            'options' => [
                'temperature' => $temperature,
            ],
        ];

        try {
            $chatResponse = Http::timeout($timeout)->post("{$baseUrl}/api/chat", $chatPayload);

            if ($chatResponse->ok()) {
                $chatText = trim((string) ($chatResponse->json('message.content') ?? $chatResponse->json('response')));
                if ($chatText !== '') {
                    return $chatText;
                }
            }
        } catch (ConnectionException) {
            return null;
        }

        $generatePayload = [
            'model' => $model,
            'prompt' => "{$systemPrompt}\n{$context}",
            'stream' => false,
            'options' => [
                'temperature' => $temperature,
            ],
        ];


        try {
            $generateResponse = Http::timeout($timeout)->post("{$baseUrl}/api/generate", $generatePayload);
        } catch (ConnectionException) {
            return null;
        }

        if (! $generateResponse->ok()) {
            return null;
        }

        $generated = trim((string) ($generateResponse->json('response') ?? $generateResponse->json('message.content')));

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

    private function buildFeedbackInsights(Subject $subject, ?string $classroomName): array
    {
        $since = now()->subDays(30);
        $feedbacks = Feedback::query()
            ->where('subject', $subject->name)
            ->where('created_at', '>=', $since)
            ->get(['rating', 'comments', 'created_at']);

        if ($feedbacks->isEmpty()) {
            $summary = sprintf(
                'Last 30 days: 0 feedback items for %s.',
                $subject->name
            );

            return [
                'summary' => $summary,
                'themes' => [],
                'issues' => [],
                'highlights' => [],
                'avgRating' => null,
                'positiveRatio' => 0,
                'negativeRatio' => 0,
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
        $highlightComments = [];

        foreach ($feedbacks as $feedback) {
            $comment = trim((string) $feedback->comments);
            $sentiment = $this->inferSentiment($feedback->rating, $comment, $positiveKeywords, $negativeKeywords);
            $sentimentCounts[$sentiment]++;

            if ($comment !== '') {
                $highlightComments[] = $comment;
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
        $topHighlights = array_slice($highlightComments, 0, 3);

        $avgRating = $feedbacks->avg('rating');
        $total = max($feedbacks->count(), 1);
        $positiveRatio = round(($sentimentCounts['positive'] / $total) * 100);
        $negativeRatio = round(($sentimentCounts['negative'] / $total) * 100);

        $summary = sprintf(
            'Last 30 days: %s feedback items for %s. Avg rating %s/5. %s%% positive, %s%% negative.',
            $feedbacks->count(),
            $subject->name,
            $avgRating ? number_format($avgRating, 2) : '0.00',
            $positiveRatio,
            $negativeRatio
        );

        if ($classroomName) {
            $summary .= " Classroom selected: {$classroomName} (feedback is per subject).";
        }

        return [
            'summary' => $summary,
            'themes' => $topThemes,
            'issues' => $topIssues,
            'highlights' => $topHighlights,
            'avgRating' => $avgRating,
            'positiveRatio' => $positiveRatio,
            'negativeRatio' => $negativeRatio,
        ];
    }

    private function buildFallbackResponse(
        Subject $subject,
        ?string $classroomName,
        string $prompt,
        array $insights
    ): string {
        $lines = [
            'Overview',
            "- Subject: {$subject->name}",
            $classroomName ? "- Class: {$classroomName}" : '- Class: not specified',
            "- {$insights['summary']}",
            '',
            'Themes & Issues',
            '- Common themes: ' . $this->formatList($insights['themes'], 'none yet'),
            '- Top issues: ' . $this->formatList($insights['issues'], 'none yet'),
            '',
        ];

        $action = $this->buildActionPlanFromInsights($insights, $prompt);
        $lines[] = 'Action';
        $lines[] = "- {$action}";
        $lines[] = '';
        $lines[] = 'Sample comments';
        $lines[] = '- ' . $this->formatList($insights['highlights'], 'none yet', "\n- ");
        $lines[] = '';
        $lines[] = $prompt ? "Lecturer note: \"{$prompt}\"." : null;

        return collect($lines)->filter(fn($line) => $line !== null)->implode("\n");
    }
    private function buildFocusAreaAdvice($subjectNames, $issuePercentages, int $negativeRatio): string
    {
        if ($issuePercentages->sum() === 0) {
            return 'No focus areas identified yet. Encourage students to share quick feedback after each class.';
        }

        $topIssues = $issuePercentages
            ->sortDesc()
            ->filter(fn($value) => $value > 0)
            ->take(3)
            ->map(fn($value, $label) => sprintf('%s (%s%%)', $label, $value))
            ->values()
            ->all();

        $subjectsLine = $subjectNames->isNotEmpty()
            ? $subjectNames->take(5)->implode(', ')
            : 'no subjects';
        $issuesLine = $topIssues !== [] ? implode(', ', $topIssues) : 'no major issues';

        $baseUrl = rtrim((string) config('services.ollama.base_url'), '/');
        $model = (string) config('services.ollama.model');
        if ($baseUrl !== '' && $model !== '') {
            $systemPrompt = 'You are an academic performance analyst. Provide 2-3 concise action steps.';
            $prompt = "Subjects: {$subjectsLine}.\nTop issues: {$issuesLine}.\nNegative ratio: {$negativeRatio}%.\nSuggest focus actions for the lecturer.";
            $payload = [
                'model' => $model,
                'prompt' => "{$systemPrompt}\n{$prompt}",
                'stream' => false,
                'options' => [
                    'temperature' => (float) config('services.ollama.temperature', 0.4),
                ],
            ];

            $timeout = (int) config('services.ollama.timeout', 10);
            try {
                $response = Http::timeout($timeout)->post("{$baseUrl}/api/generate", $payload);
                if ($response->ok()) {
                    $generated = trim((string) $response->json('response'));
                    if ($generated !== '') {
                        return $generated;
                    }
                }
            } catch (ConnectionException) {
                // Fallback below.
            }
        }

        $action = 'Action: Schedule a quick check-in and clarify expectations next class.';
        if ($negativeRatio >= 30) {
            $action = 'Saya akan memberi fokus kepada isu-isu yang paling kritikal terlebih dahulu sebelum membincangkan perkara sampingan';
        }

        return "Focus areas: {$issuesLine}. {$action}";
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
