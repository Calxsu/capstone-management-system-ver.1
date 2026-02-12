<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SchoolYear;
use Illuminate\Http\Request;

class SchoolYearController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('school-years.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('school-years.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'year' => 'required|string|max:20|unique:school_years,year',
        ]);

        // Parse the year to create default start and end dates
        $yearParts = explode('-', $request->year);
        $startYear = $yearParts[0] ?? date('Y');
        $endYear = $yearParts[1] ?? ($startYear + 1);

        SchoolYear::create([
            'year' => $request->year,
            'start_date' => "{$startYear}-06-01",
            'end_date' => "{$endYear}-05-31",
        ]);

        return redirect()->route('school-years.index')->with('success', 'School year created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SchoolYear $schoolYear)
    {
        return view('school-years.show', compact('schoolYear'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SchoolYear $schoolYear)
    {
        return view('school-years.edit', compact('schoolYear'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SchoolYear $schoolYear)
    {
        $request->validate([
            'year' => 'required|string|max:20|unique:school_years,year,' . $schoolYear->id,
        ]);

        // Parse the year to update start and end dates
        $yearParts = explode('-', $request->year);
        $startYear = $yearParts[0] ?? date('Y');
        $endYear = $yearParts[1] ?? ($startYear + 1);

        $schoolYear->update([
            'year' => $request->year,
            'start_date' => "{$startYear}-06-01",
            'end_date' => "{$endYear}-05-31",
        ]);

        return redirect()->route('school-years.index')->with('success', 'School year updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SchoolYear $schoolYear)
    {
        $schoolYear->delete();

        return redirect()->route('school-years.index')->with('success', 'School year deleted successfully.');
    }
}
