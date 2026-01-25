<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::orderBy('code')->get();

        return view('admin.subjects.index', compact('subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:subjects,code',
            'name' => 'required|string|max:255',
        ]);

        Subject::create($validated);

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject created.');
    }
}
