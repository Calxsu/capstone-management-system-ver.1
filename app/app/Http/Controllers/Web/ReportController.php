<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Display the reports index page.
     */
    public function index(): View
    {
        return view('reports.index');
    }

    /**
     * Display the ETL report page.
     */
    public function etl(): View
    {
        return view('reports.etl');
    }

    /**
     * Display the CAP Progress report page.
     */
    public function capProgress(): View
    {
        return view('reports.cap_progress');
    }

    /**
     * Display a specific report.
     */
    public function show(string $id): View
    {
        return view('reports.show', ['reportId' => $id]);
    }
}
