<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ClassroomController;
use App\Models\Feedback;
use Illuminate\Support\Str;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/feedback', [FeedbackController::class, 'create']);
Route::post('/feedback', [FeedbackController::class, 'store']);

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/feedback', [FeedbackController::class, 'index']);
    Route::get('/admin/subjects', [SubjectController::class, 'index'])->name('admin.subjects.index');
    Route::post('/admin/subjects', [SubjectController::class, 'store'])->name('admin.subjects.store');
    Route::get('/admin/classes', [ClassroomController::class, 'index'])->name('admin.classrooms.index');
    Route::post('/admin/classes', [ClassroomController::class, 'store'])->name('admin.classrooms.store');
    Route::post('/admin/classes/enrollments', [ClassroomController::class, 'storeEnrollment'])
        ->name('admin.classrooms.enrollments.store');
});

Route::get('/dashboard', function () {
    $feedbacks = Feedback::query()->get(['rating', 'comments']);
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

    $negativeRatio = round(($negativeCount / $totalFeedback) * 100);
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
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
