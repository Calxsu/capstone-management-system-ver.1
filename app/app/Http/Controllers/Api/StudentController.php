<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    /**
     * Display a listing of students.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Student::query()->with('groups');

            // Search functionality
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('specialization', 'like', "%{$search}%");
                });
            }

            // Filter by specialization
            if ($request->has('specialization')) {
                $query->where('specialization', $request->get('specialization'));
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'name');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination or all
            if ($request->has('per_page')) {
                $students = $query->paginate($request->get('per_page', 15));
            } else {
                $students = $query->get();
            }

            // Transform to include group status
            $transformed = $students->map(function($student) {
                $data = $student->toArray();
                $data['in_group'] = $student->groups->isNotEmpty();
                $data['group_id'] = $student->groups->first()?->id;
                $data['group_name'] = $student->groups->first()?->project_title ?? ($student->groups->first() ? 'Group #' . $student->groups->first()->id : null);
                return $data;
            });

            return response()->json($transformed);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch students',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created student.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'student_id' => 'required|string|max:50|unique:students,student_id',
                'specialization' => 'required|in:Networking,Systems Development',
            ]);

            $student = Student::create($validated);

            return response()->json([
                'message' => 'Student created successfully',
                'data' => $student
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create student',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified student.
     */
    public function show(Student $student): JsonResponse
    {
        try {
            $student->load('groups');
            return response()->json($student);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch student',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified student.
     */
    public function update(Request $request, Student $student): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'specialization' => 'nullable|in:Networking,Systems Development',
            ]);

            $student->update($validated);

            return response()->json([
                'message' => 'Student updated successfully',
                'data' => $student
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update student',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified student.
     */
    public function destroy(Student $student): JsonResponse
    {
        try {
            // Check if student is assigned to any groups
            if ($student->groups()->count() > 0) {
                return response()->json([
                    'error' => 'Cannot delete student',
                    'message' => 'This student is assigned to one or more groups'
                ], 409);
            }

            $student->delete();

            return response()->json([
                'message' => 'Student deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to delete student',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available students (not assigned to any group)
     */
    public function available(): JsonResponse
    {
        try {
            $available = Student::whereDoesntHave('groups')->get();
            return response()->json($available);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch available students',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get students by specialization
     */
    public function bySpecialization(string $specialization): JsonResponse
    {
        try {
            $students = Student::where('specialization', $specialization)->get();
            return response()->json($students);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch students by specialization',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get distinct specializations
     */
    public function specializations(): JsonResponse
    {
        try {
            $specializations = Student::distinct()->pluck('specialization');
            return response()->json($specializations);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch specializations',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
