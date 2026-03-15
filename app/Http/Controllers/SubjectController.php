<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Subject;
use App\Models\User;
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
            'name' => 'required|string|max:255|unique:subjects,name',
        ]);


        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['name'] = trim($validated['name']);


        Subject::create($validated);

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject created.');
    }


    public function destroy(Subject $subject)
    {
        $lecturerIds = $subject->classrooms()
            ->whereNotNull('lecturer_id')
            ->pluck('lecturer_id')
            ->unique();

        $subject->delete();

        foreach ($lecturerIds as $lecturerId) {
            $stillTeaching = Classroom::query()
                ->where('lecturer_id', $lecturerId)
                ->exists();

            if (! $stillTeaching) {
                User::query()
                    ->whereKey($lecturerId)
                    ->where('role', User::ROLE_LECTURER)
                    ->update(['role' => User::ROLE_STUDENT]);
            }
        }


        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject deleted.');
    }
}
