<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\ClassroomEnrollment;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    public function index()
    {
        $classrooms = Classroom::with(['subject', 'lecturer', 'enrollments.student'])
            ->orderBy('name')
            ->get();
        $subjects = Subject::orderBy('code')->get();
        $lecturers = User::where('role', User::ROLE_LECTURER)->orderBy('name')->get();
        $students = User::where('role', User::ROLE_STUDENT)->orderBy('name')->get();

        return view('admin.classrooms.index', compact('classrooms', 'subjects', 'lecturers', 'students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'subject_id' => 'required|exists:subjects,id',
            'lecturer_id' => 'nullable|exists:users,id',
        ]);

        Classroom::create($validated);

        if (! empty($validated['lecturer_id'])) {
            User::whereKey($validated['lecturer_id'])
                ->update(['role' => User::ROLE_LECTURER]);
        }

        return redirect()->route('admin.classrooms.index')
            ->with('success', 'Class created.');
    }

    public function storeEnrollment(Request $request)
    {
        $validated = $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'student_id' => 'required|exists:users,id',
        ]);

        ClassroomEnrollment::firstOrCreate($validated);

        User::whereKey($validated['student_id'])
            ->where('role', '!=', User::ROLE_ADMIN)
            ->update(['role' => User::ROLE_STUDENT]);

        return redirect()->route('admin.classrooms.index')
            ->with('success', 'Student assigned to class.');
    }
}
