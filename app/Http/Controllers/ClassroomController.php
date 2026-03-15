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
        $students = User::where('role', User::ROLE_STUDENT)->orderBy('name')->get();

        return view('admin.classrooms.index', compact('classrooms', 'subjects', 'students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'subject_id' => 'required|exists:subjects,id',
            'lecturer_id' => 'nullable|exists:users,id',
        ]);


        if (! empty($validated['lecturer_id'])) {
            $lecturerExists = User::query()
                ->whereKey($validated['lecturer_id'])
                ->where('role', '!=', User::ROLE_ADMIN)
                ->exists();

            if (! $lecturerExists) {
                return redirect()
                    ->route('admin.classrooms.index')
                    ->withErrors(['lecturer_id' => 'Selected user cannot be assigned as lecturer.'])
                    ->withInput();
            }
        }



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

        $studentExists = User::query()
            ->whereKey($validated['student_id'])
            ->where('role', '!=', User::ROLE_ADMIN)
            ->exists();

        if (! $studentExists) {
            return redirect()
                ->route('admin.classrooms.index')
                ->withErrors(['student_id' => 'Selected user cannot be enrolled as student.'])
                ->withInput();
        }

        $enrollment = ClassroomEnrollment::firstOrCreate($validated);

        if (! $enrollment->wasRecentlyCreated) {
            return redirect()->route('admin.classrooms.index')
                ->with('success', 'Student is already assigned to this class.');
        }

        User::whereKey($validated['student_id'])
            ->where('role', '!=', User::ROLE_ADMIN)
            ->update(['role' => User::ROLE_STUDENT]);

        return redirect()->route('admin.classrooms.index')
            ->with('success', 'Student assigned to class.');
    }


    public function destroy(Classroom $classroom)
    {
        $lecturerId = $classroom->lecturer_id;

        $classroom->delete();

        if ($lecturerId) {
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

        return redirect()->route('admin.classrooms.index')
            ->with('success', 'Class deleted.');
    }
}
