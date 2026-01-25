<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback;
use App\Models\Subject;
use App\Http\Controllers\Controller;

class FeedbackController extends Controller
{
    public function create()
    {
        $subjects = Subject::orderBy('name')->get();

        return view('feedback.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|exists:subjects,name',
            'rating'  => 'required|integer|min:1|max:5',
            'comments' => 'nullable|string|max:1000',
            'is_anonymous' => 'nullable|boolean',
        ]);

        Feedback::create([
            'subject' => $validated['subject'],
            'rating' => $validated['rating'],
            'comments' => $validated['comments'] ?? null,
            'is_anonymous' => $request->has('is_anonymous'),
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

        $feedbacks = \App\Models\Feedback::latest()->get();
        $avgRating = (clone $query)->avg('rating');

        $subjects = Subject::orderBy('name')->pluck('name');

        return view('admin.index', compact('feedbacks', 'avgRating', 'subjects', 'subject'));
    }
}
