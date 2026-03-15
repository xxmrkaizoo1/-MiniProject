<?php

use App\Models\Classroom;
use App\Models\ClassroomEnrollment;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function adminUser(): User
{
    return User::factory()->create([
        'role' => User::ROLE_ADMIN,
        'is_admin' => true,
    ]);
}

test('admin can create subject and input is normalized', function () {
    $admin = adminUser();

    $response = $this->actingAs($admin)->post('/admin/subjects', [
        'code' => '  csc101 ',
        'name' => '  Intro to Computing  ',
    ]);

    $response->assertRedirect(route('admin.subjects.index'));

    $this->assertDatabaseHas('subjects', [
        'code' => 'CSC101',
        'name' => 'Intro to Computing',
    ]);
});

test('admin cannot assign admin user as lecturer', function () {
    $admin = adminUser();
    $subject = Subject::create([
        'code' => 'SUB001',
        'name' => 'Subject 001',
    ]);
    $adminLecturer = adminUser();

    $response = $this->actingAs($admin)
        ->from(route('admin.classrooms.index'))
        ->post('/admin/classes', [
            'name' => 'A1',
            'subject_id' => $subject->id,
            'lecturer_id' => $adminLecturer->id,
        ]);

    $response->assertRedirect(route('admin.classrooms.index'));
    $response->assertSessionHasErrors('lecturer_id');

    $this->assertDatabaseMissing('classrooms', ['name' => 'A1']);
});

test('admin cannot enroll admin user as student', function () {
    $admin = adminUser();
    $subject = Subject::create([
        'code' => 'SUB002',
        'name' => 'Subject 002',
    ]);
    $classroom = Classroom::create([
        'name' => 'B1',
        'subject_id' => $subject->id,
    ]);
    $adminStudent = adminUser();

    $response = $this->actingAs($admin)
        ->from(route('admin.classrooms.index'))
        ->post('/admin/classes/enrollments', [
            'classroom_id' => $classroom->id,
            'student_id' => $adminStudent->id,
        ]);

    $response->assertRedirect(route('admin.classrooms.index'));
    $response->assertSessionHasErrors('student_id');

    $this->assertDatabaseCount('classroom_enrollments', 0);
});

test('duplicated enrollment is not created twice', function () {
    $admin = adminUser();
    $subject = Subject::create([
        'code' => 'SUB003',
        'name' => 'Subject 003',
    ]);
    $classroom = Classroom::create([
        'name' => 'C1',
        'subject_id' => $subject->id,
    ]);
    $student = User::factory()->create(['role' => User::ROLE_STUDENT]);

    ClassroomEnrollment::create([
        'classroom_id' => $classroom->id,
        'student_id' => $student->id,
    ]);

    $response = $this->actingAs($admin)->post('/admin/classes/enrollments', [
        'classroom_id' => $classroom->id,
        'student_id' => $student->id,
    ]);

    $response->assertRedirect(route('admin.classrooms.index'));

    $this->assertDatabaseCount('classroom_enrollments', 1);
});
