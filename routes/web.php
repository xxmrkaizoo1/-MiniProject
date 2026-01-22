<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FeedbackController;
use App\Models\Feedback;
Route::get('/', function () {
    return view('welcome');
});

Route::get('/feedback' , [FeedbackController::class, 'create'])->name('feedback.create');
Route::post('/feedback' , [FeedbackController::class, 'store']);
Route::get('/Admin/feedback' , [FeedbackController::class, 'index'])->name('admin.index');


