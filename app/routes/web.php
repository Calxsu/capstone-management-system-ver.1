<?php

use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\SchoolYearController;
use App\Http\Controllers\Web\PanelMemberController;
use App\Http\Controllers\Web\StudentController;
use App\Http\Controllers\Web\GroupController;
use App\Http\Controllers\Web\EvaluationController;
use App\Http\Controllers\Web\ReportController;
use App\Http\Controllers\Web\ImportController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\ChecklistController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});


Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// School Years Management
Route::resource('school-years', SchoolYearController::class)->names([
    'index' => 'school-years.index',
    'create' => 'school-years.create',
    'store' => 'school-years.store',
    'show' => 'school-years.show',
    'edit' => 'school-years.edit',
    'update' => 'school-years.update',
    'destroy' => 'school-years.destroy',
]);

// Panel Members Management
Route::resource('panel-members', PanelMemberController::class)->names([
    'index' => 'panel-members.index',
    'create' => 'panel-members.create',
    'store' => 'panel-members.store',
    'show' => 'panel-members.show',
    'edit' => 'panel-members.edit',
    'update' => 'panel-members.update',
    'destroy' => 'panel-members.destroy',
]);
Route::get('panel-members-data', [PanelMemberController::class, 'getData'])->name('panel-members.data');

// Students Management
Route::resource('students', StudentController::class)->names([
    'index' => 'students.index',
    'create' => 'students.create',
    'store' => 'students.store',
    'show' => 'students.show',
    'edit' => 'students.edit',
    'update' => 'students.update',
    'destroy' => 'students.destroy',
]);

// Groups Management
Route::resource('groups', GroupController::class)->names([
    'index' => 'groups.index',
    'create' => 'groups.create',
    'store' => 'groups.store',
    'show' => 'groups.show',
    'edit' => 'groups.edit',
    'update' => 'groups.update',
    'destroy' => 'groups.destroy',
]);

// Group specific routes
Route::get('groups/{group}/students/add', [GroupController::class, 'addStudents'])->name('groups.add-students');
Route::post('groups/{group}/students', [GroupController::class, 'storeStudents'])->name('groups.store-students');
Route::delete('groups/{group}/students/{student}', [GroupController::class, 'removeStudent'])->name('groups.remove-student');
Route::patch('groups/{group}/status', [GroupController::class, 'updateStatus'])->name('groups.update-status');

// Evaluations Management
Route::resource('evaluations', EvaluationController::class)->names([
    'index' => 'evaluations.index',
    'create' => 'evaluations.create',
    'store' => 'evaluations.store',
    'show' => 'evaluations.show',
    'edit' => 'evaluations.edit',
    'update' => 'evaluations.update',
    'destroy' => 'evaluations.destroy',
]);

// Evaluation specific routes
Route::get('groups/{group}/evaluations', [EvaluationController::class, 'groupEvaluations'])->name('evaluations.group');
Route::get('groups/{group}/evaluations/create', [EvaluationController::class, 'createForGroup'])->name('evaluations.create-for-group');
Route::get('evaluations/group/{group}/edit', [EvaluationController::class, 'editGroup'])->name('evaluations.edit-group');
Route::put('evaluations/group/{group}', [EvaluationController::class, 'updateGroup'])->name('evaluations.update-group');

// Reports
Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('reports/etl', [ReportController::class, 'etl'])->name('reports.etl');
Route::get('reports/cap-progress', [ReportController::class, 'capProgress'])->name('reports.cap-progress');
Route::get('reports/{report}', [ReportController::class, 'show'])->name('reports.show');

// ETL Management
Route::get('etl', [\App\Http\Controllers\Web\EtlController::class, 'index'])->name('etl.index');
Route::get('etl/data', [\App\Http\Controllers\Web\EtlController::class, 'getData'])->name('etl.data');
Route::get('etl/member/{panelMember}', [\App\Http\Controllers\Web\EtlController::class, 'show'])->name('etl.show');
Route::get('etl/member/{panelMember}/details', [\App\Http\Controllers\Web\EtlController::class, 'getMemberDetails'])->name('etl.member-details');
Route::get('etl/role-values', [\App\Http\Controllers\Web\EtlController::class, 'roleValues'])->name('etl.role-values');
Route::put('etl/role-values', [\App\Http\Controllers\Web\EtlController::class, 'updateRoleValues'])->name('etl.role-values.update');

// Import
Route::get('import', [ImportController::class, 'index'])->name('import.index');
Route::post('import', [ImportController::class, 'import'])->name('import.store');
Route::get('import/template/{type}', [ImportController::class, 'downloadTemplate'])->name('import.template');

// Profile
Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
Route::get('profile/password', [ProfileController::class, 'editPassword'])->name('profile.password');
Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
Route::get('profile/delete', [ProfileController::class, 'confirmDelete'])->name('profile.delete');
Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

// CAPSTONE 2 Completion Checklist
Route::get('checklists', [ChecklistController::class, 'index'])->name('checklists.index');
Route::get('checklists/items', [ChecklistController::class, 'items'])->name('checklists.items');
Route::get('groups/{group}/checklist', [ChecklistController::class, 'groupChecklist'])->name('checklists.group');
