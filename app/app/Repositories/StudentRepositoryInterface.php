<?php

namespace App\Repositories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;

interface StudentRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Student;
    public function create(array $data): Student;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function findByStudentId(string $studentId): ?Student;
}