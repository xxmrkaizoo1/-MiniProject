<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Feedback;
use App\Models\Subject;
use Illuminate\Http\Request;

class LecturerChatbotController extends Controller
{
    public function dashboard()
    {
        $classrooms = Classroom::with('subject')
            ->withCount('enrollments')
            ->where('lecturer_id', auth()->id())
            ->orderBy('name')
            ->get();

        $subjects = Subject::whereIn('id', $classrooms->pluck('subject_id')->filter())
            ->orderBy('name')
            ->get();

        $subjectNames = $subjects->pluck('name');
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
            'avgRating' => $avgRating,
            'negativeCount' => $negativeCount,
            'totalFeedback' => $totalFeedback,
            'negativeRatio' => $negativeRatio,
            'notification' => $notification,
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

        $response = collect([
            "Subject focus: {$subject->name}.",
            $classroomName ? "Class context: {$classroomName}." : 'Class context: not specified.',
            'Advice: Highlight the learning outcomes at the start, include one short activity, and end with a recap question.',
            'Tip: Invite anonymous feedback for the next session to confirm if the pacing works.',
            $prompt ? "Based on your note: \"{$prompt}\"." : null,
        ])->filter()->implode(' ');

        return back()->with('chatbot_response', $response);
    }
}
