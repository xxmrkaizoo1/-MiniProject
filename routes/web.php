<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\LecturerChatbotController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/feedback', [FeedbackController::class, 'create']);
Route::post('/feedback', [FeedbackController::class, 'store']);

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/feedback', [FeedbackController::class, 'index'])->name('admin.feedback.index');
    Route::get('/admin/subjects', [SubjectController::class, 'index'])->name('admin.subjects.index');
    Route::post('/admin/subjects', [SubjectController::class, 'store'])->name('admin.subjects.store');
    Route::get('/admin/classes', [ClassroomController::class, 'index'])->name('admin.classrooms.index');
    Route::post('/admin/classes', [ClassroomController::class, 'store'])->name('admin.classrooms.store');
    Route::post('/admin/classes/enrollments', [ClassroomController::class, 'storeEnrollment'])
        ->name('admin.classrooms.enrollments.store');
});

Route::get('/dashboard', [LecturerChatbotController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::post('/lecturer/chatbot', [LecturerChatbotController::class, 'respond'])
    ->middleware(['auth', 'verified'])
    ->name('lecturer.chatbot.respond');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
