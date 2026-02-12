<?php

use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\EvaluationController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\ImportController;
use App\Http\Controllers\Api\PanelMemberController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SchoolYearController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\ChecklistController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Dashboard API
Route::get('dashboard/stats', [DashboardController::class, 'stats'])->name('api.dashboard.stats');
Route::get('dashboard/recent-activity', [DashboardController::class, 'recentActivity'])->name('api.dashboard.recent-activity');

// School Years API
Route::apiResource('school-years', SchoolYearController::class)->names([
    'index' => 'api.school-years.index',
    'store' => 'api.school-years.store',
    'show' => 'api.school-years.show',
    'update' => 'api.school-years.update',
    'destroy' => 'api.school-years.destroy',
]);

// Panel Members API
Route::get('panel-members/available', [PanelMemberController::class, 'available'])->name('api.panel-members.available');
Route::apiResource('panel-members', PanelMemberController::class)->names([
    'index' => 'api.panel-members.index',
    'store' => 'api.panel-members.store',
    'show' => 'api.panel-members.show',
    'update' => 'api.panel-members.update',
    'destroy' => 'api.panel-members.destroy',
]);

// Students API
Route::get('students/available', [StudentController::class, 'available'])->name('api.students.available');
Route::get('students/course/{course}', [StudentController::class, 'byCourse'])->name('api.students.by-course');
Route::get('students/courses', [StudentController::class, 'courses'])->name('api.students.courses');
Route::apiResource('students', StudentController::class)->names([
    'index' => 'api.students.index',
    'store' => 'api.students.store',
    'show' => 'api.students.show',
    'update' => 'api.students.update',
    'destroy' => 'api.students.destroy',
]);

// Groups API
Route::apiResource('groups', GroupController::class)->names([
    'index' => 'api.groups.index',
    'store' => 'api.groups.store',
    'show' => 'api.groups.show',
    'update' => 'api.groups.update',
    'destroy' => 'api.groups.destroy',
]);
Route::post('groups/{group}/students', [GroupController::class, 'addStudent'])->name('api.groups.add-student');
Route::delete('groups/{group}/students/{student}', [GroupController::class, 'removeStudent'])->name('api.groups.remove-student');
Route::patch('groups/{id}/status', [GroupController::class, 'updateStatus'])->name('api.groups.update-status');

// Evaluations API
Route::apiResource('evaluations', EvaluationController::class)->names([
    'index' => 'api.evaluations.index',
    'store' => 'api.evaluations.store',
    'show' => 'api.evaluations.show',
    'update' => 'api.evaluations.update',
    'destroy' => 'api.evaluations.destroy',
]);
Route::get('groups/{groupId}/evaluations/summary', [EvaluationController::class, 'groupSummary'])->name('api.evaluations.group-summary');
Route::put('evaluations/group/{group}', [EvaluationController::class, 'updateGroup'])->name('api.evaluations.update-group');

// Reports API
Route::get('reports/etl', [ReportController::class, 'etlReport'])->name('api.reports.etl');
Route::get('reports/cap-progress', [ReportController::class, 'capProgressReport'])->name('api.reports.cap-progress');
Route::get('reports/etl/export', [ReportController::class, 'exportETLReport'])->name('api.reports.etl.export');
Route::get('reports/cap-progress/export', [ReportController::class, 'exportCAPProgressReport'])->name('api.reports.cap-progress.export');
Route::apiResource('reports', ReportController::class)->names([
    'index' => 'api.reports.index',
    'store' => 'api.reports.store',
    'show' => 'api.reports.show',
    'update' => 'api.reports.update',
    'destroy' => 'api.reports.destroy',
]);

// Import API
Route::post('import', [ImportController::class, 'import'])->name('api.import.store');
Route::get('import/template', [ImportController::class, 'template'])->name('api.import.template');
Route::get('import/template-types', [ImportController::class, 'templateTypes'])->name('api.import.template-types');
Route::get('import/template/{type}', [ImportController::class, 'downloadTemplate'])->name('api.import.download-template');

// Profile API
Route::put('profile', [App\Http\Controllers\Web\ProfileController::class, 'update'])->name('api.profile.update');
Route::put('profile/password', [App\Http\Controllers\Web\ProfileController::class, 'updatePassword'])->name('api.profile.password.update');
Route::delete('profile', [App\Http\Controllers\Web\ProfileController::class, 'destroy'])->name('api.profile.destroy');

// ETL Role Values API
Route::put('etl/role-values', [App\Http\Controllers\Web\EtlController::class, 'updateRoleValues'])->name('api.etl.role-values.update');

// Checklist API (CAPSTONE 2 Completion)
Route::get('checklist-items', [ChecklistController::class, 'items'])->name('api.checklist-items.index');
Route::post('checklist-items', [ChecklistController::class, 'storeItem'])->name('api.checklist-items.store');
Route::put('checklist-items/{checklistItem}', [ChecklistController::class, 'updateItem'])->name('api.checklist-items.update');
Route::delete('checklist-items/{checklistItem}', [ChecklistController::class, 'destroyItem'])->name('api.checklist-items.destroy');
Route::get('groups/{group}/checklist', [ChecklistController::class, 'groupChecklist'])->name('api.groups.checklist');
Route::post('groups/{group}/checklist/{checklistItem}', [ChecklistController::class, 'toggleItem'])->name('api.groups.checklist.toggle');
Route::get('checklists/cap2-progress', [ChecklistController::class, 'cap2GroupsProgress'])->name('api.checklists.cap2-progress');