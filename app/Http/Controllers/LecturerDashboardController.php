<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Feedback;
use Carbon\Carbon;
use Illuminate\Support\Str;

class LecturerDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $classrooms = Classroom::with('subject')
            ->withCount('enrollments')
            ->where('lecturer_id', $user->id)
            ->orderBy('name')
            ->get();

        $subjectNames = $classrooms
            ->pluck('subject.name')
            ->filter()
            ->unique()
            ->values();

        $feedbacks = Feedback::query()
            ->when($subjectNames->isNotEmpty(), function ($query) use ($subjectNames) {
                $query->whereIn('subject', $subjectNames);
            }, function ($query) {
                $query->whereRaw('1 = 0');
            })
            ->get(['rating', 'comments', 'created_at']);

        $avgRating = $feedbacks->avg('rating');
        $totalFeedback = max($feedbacks->count(), 1);

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

        $positiveKeywords = [
            'baik',
            'bagus',
            'hebat',
            'jelas',
            'mantap',
            'cepat',
            'great',
            'good',
            'excellent',
            'clear',
            'helpful',
            'awesome',
            'fantastic',
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
        $ratingTrendMonths = collect(range(5, 0))->map(function (int $offset) use ($now) {
            return $now->copy()->subMonths($offset)->startOfMonth();
        });

        $ratingTrendLabels = $ratingTrendMonths->map(fn (Carbon $month) => $month->format('M'));
        $ratingTrendData = $ratingTrendMonths->map(function (Carbon $month) use ($feedbacks) {
            $monthFeedback = $feedbacks->filter(fn ($feedback) => $feedback->created_at && $feedback->created_at->isSameMonth($month));
            return round($monthFeedback->avg('rating') ?? 0, 2);
        });

        $currentMonthAverage = $ratingTrendData->last() ?? 0;
        $previousMonthAverage = $ratingTrendData->count() > 1 ? $ratingTrendData->get($ratingTrendData->count() - 2) : 0;
        $ratingMoMChange = $previousMonthAverage > 0
            ? round((($currentMonthAverage - $previousMonthAverage) / $previousMonthAverage) * 100)
            : 0;

        $weeklyDays = collect(range(6, 0))->map(function (int $offset) use ($now) {
            return $now->copy()->subDays($offset)->startOfDay();
        });

        $sentimentTrendLabels = $weeklyDays->map(fn (Carbon $day) => $day->format('D'));
        $sentimentTrendData = $weeklyDays->map(function (Carbon $day) use ($feedbacks, $inferSentiment) {
            $dayFeedback = $feedbacks->filter(fn ($feedback) => $feedback->created_at && $feedback->created_at->isSameDay($day));
            $total = $dayFeedback->count();

            if ($total === 0) {
                return 0;
            }

            $positiveCount = $dayFeedback->filter(function ($feedback) use ($inferSentiment) {
                return $inferSentiment($feedback->rating, (string) $feedback->comments) === 'positive';
            })->count();

            return round(($positiveCount / $total) * 100);
        });

        $weeklyFeedback = $feedbacks->filter(fn ($feedback) => $feedback->created_at && $feedback->created_at->greaterThanOrEqualTo($now->copy()->subDays(6)->startOfDay()));
        $weeklyTotal = $weeklyFeedback->count();
        $weeklyPositive = $weeklyFeedback->filter(function ($feedback) use ($inferSentiment) {
            return $inferSentiment($feedback->rating, (string) $feedback->comments) === 'positive';
        })->count();
        $weeklyPositiveRate = $weeklyTotal > 0 ? round(($weeklyPositive / $weeklyTotal) * 100) : 0;

        $issueCategories = [
            'Wi-Fi' => ['wifi', 'wi-fi', 'internet', 'connection', 'connectivity'],
            'Projector' => ['projector', 'projek', 'proyektor', 'display', 'screen'],
            'LMS' => ['lms', 'portal', 'login', 'sync', 'synchronization', 'sinkron'],
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
        $hasLowRating = $avgRating !== null && $avgRating < 3;
        $hasNegativeSpike = $negativeCount >= 3 && $negativeRatio >= 30;

        $notification = null;

        if ($hasLowRating || $hasNegativeSpike) {
            $notification = [
                'title' => 'Lecturer Alert',
                'message' => sprintf(
                    'Attention: average rating %s/5 with %s%% negative comments. Please review the main classroom issues.',
                    $avgRating ? number_format($avgRating, 2) : '0.00',
                    $negativeRatio
                ),
            ];
        }

        return view('dashboard', [
            'notification' => $notification,
            'avgRating' => $avgRating,
            'negativeRatio' => $negativeRatio,
            'negativeCount' => $negativeCount,
            'totalFeedback' => $feedbacks->count(),
            'classrooms' => $classrooms,
            'ratingTrendLabels' => $ratingTrendLabels,
            'ratingTrendData' => $ratingTrendData,
            'ratingMoMChange' => $ratingMoMChange,
            'currentMonthAverage' => $currentMonthAverage,
            'weeklyPositiveRate' => $weeklyPositiveRate,
            'sentimentTrendLabels' => $sentimentTrendLabels,
            'sentimentTrendData' => $sentimentTrendData,
            'issueLabels' => $issuePercentages->keys()->values(),
            'issueData' => $issuePercentages->values(),
        ]);
    }
}
