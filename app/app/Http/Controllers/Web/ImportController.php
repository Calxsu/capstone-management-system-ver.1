<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\ImportController as ApiImportController;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ImportController extends Controller
{
    public function __construct(
        private ApiImportController $apiController
    ) {}

    /**
     * Display the import page.
     */
    public function index(): View
    {
        return view('import.index');
    }

    /**
     * Download import template.
     */
    public function downloadTemplate(string $type)
    {
        return $this->apiController->downloadTemplate($type);
    }

    /**
     * Handle file import (delegates to API controller).
     */
    public function import(Request $request)
    {
        return $this->apiController->import($request);
    }
}
