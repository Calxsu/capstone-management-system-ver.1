<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ChecklistItem;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChecklistController extends Controller
{
    /**
     * Display the checklist management page.
     */
    public function index(): View
    {
        return view('checklists.index');
    }

    /**
     * Display the checklist items management page (admin).
     */
    public function items(): View
    {
        return view('checklists.items');
    }

    /**
     * Display the checklist for a specific group.
     */
    public function groupChecklist(Group $group): View
    {
        return view('checklists.group', compact('group'));
    }
}
