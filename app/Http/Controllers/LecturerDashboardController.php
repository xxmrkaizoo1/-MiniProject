<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Feedback;
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
            ->get(['rating', 'comments']);

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

        $negativeRatio = $totalFeedback > 0 ? round(($negativeCount / $totalFeedback) * 100) : 0;
        $hasLowRating = $avgRating !== null && $avgRating < 3;
        $hasNegativeSpike = $negativeCount >= 3 && $negativeRatio >= 30;

        $notification = null;

        if ($hasLowRating || $hasNegativeSpike) {
            $notification = [
                'title' => 'Notifikasi Pensyarah',
                'message' => sprintf(
                    'Perhatian: purata rating %s/5 dengan %s%% komen negatif. Sila semak isu utama kelas.',
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
        ]);
    }
}
