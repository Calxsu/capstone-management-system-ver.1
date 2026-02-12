<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\SchoolYearRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SchoolYearController extends Controller
{
    public function __construct(
        private SchoolYearRepositoryInterface $schoolYearRepository
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $schoolYears = $this->schoolYearRepository->all();
        return response()->json($schoolYears);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'year' => 'required|string|max:20|unique:school_years,year',
            'semester' => 'required|string|max:50',
            'is_active' => 'boolean',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
        ]);

        $schoolYear = $this->schoolYearRepository->create($validated);
        return response()->json($schoolYear, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $schoolYear = $this->schoolYearRepository->find($id);

        if (!$schoolYear) {
            return response()->json(['message' => 'School year not found'], 404);
        }

        return response()->json($schoolYear);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'year' => 'sometimes|string|max:20|unique:school_years,year,' . $id,
            'semester' => 'sometimes|string|max:50',
            'is_active' => 'boolean',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
        ]);

        $updated = $this->schoolYearRepository->update($id, $validated);

        if (!$updated) {
            return response()->json(['message' => 'School year not found'], 404);
        }

        return response()->json(['message' => 'School year updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->schoolYearRepository->delete($id);

        if (!$deleted) {
            return response()->json(['message' => 'School year not found'], 404);
        }

        return response()->json(['message' => 'School year deleted successfully']);
    }
}
