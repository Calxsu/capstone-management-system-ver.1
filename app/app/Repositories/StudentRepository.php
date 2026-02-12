<?php

namespace App\Repositories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;

class StudentRepository implements StudentRepositoryInterface
{
    public function all(): Collection
    {
        return Student::all();
    }

    public function find(int $id): ?Student
    {
        return Student::find($id);
    }

    public function create(array $data): Student
    {
        return Student::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $student = Student::find($id);
        return $student ? $student->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $student = Student::find($id);
        return $student ? $student->delete() : false;
    }

    public function findByStudentId(string $studentId): ?Student
    {
        return Student::where('student_id', $studentId)->first();
    }
}