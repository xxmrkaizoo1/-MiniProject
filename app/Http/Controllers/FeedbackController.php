<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Feedback;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class FeedbackController extends Controller
{
    public function create()
    {
        return view('feedback.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:100',
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'is_anonymous' => 'nullable|boolean',
        ]);

        Feedback::create([
            'subject' => $validated['subject'],
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'is_anonymous' => $request->has('is_anonymous'),
        ]);

        return redirect('/feedback')->with('success', 'Feedback submitted!');
    }


  public function index(Request $request)
{
    $subject = $request->query('subject');

    $query = \App\Models\Feedback::query();

    if ($subject) {
        $query->where('subject', $subject);
    }

    $feedbacks = $query->latest()->get();

    $avgRating = $query->avg('rating');

    $subjects = \App\Models\Feedback::select('subject')->distinct()->pluck('subject');

    return view('admin.index', compact('feedbacks', 'avgRating', 'subjects', 'subject'));
}

}
