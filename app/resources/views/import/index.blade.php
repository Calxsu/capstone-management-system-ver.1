@extends('layouts.dashboard')

@section('title', 'Import Data')
@section('subtitle', 'Upload and import data from CSV files')

@section('content')
<div x-data="importData()" x-init="init()" class="max-w-4xl mx-auto">
    <!-- Import Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
        <!-- Header -->
        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-blue-500 to-purple-600">
            <div class="flex items-center">
                <div class="p-3 bg-white/20 rounded-xl mr-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">Import Data</h2>
                    <p class="text-white/80 text-sm">Upload CSV files to import students or panel members</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <!-- School Year Selection -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Select School Year</label>
                <select x-model="selectedSchoolYearId" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" :disabled="schoolYears.length === 0">
                    <option value="" x-show="schoolYears.length === 0">Loading school years...</option>
                    <option value="" x-show="schoolYears.length > 0">Select a school year...</option>
                    <template x-for="year in schoolYears" :key="year.id">
                        <option :value="year.id" x-text="year.year + ' (' + (year.semester == 1 ? '1st' : '2nd') + ' Semester)'"></option>
                    </template>
                </select>
                <p x-show="schoolYears.length === 0" class="text-sm text-gray-500 mt-1">Loading available school years...</p>
            </div>

            <!-- Import Type Selection -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Select Import Type</label>
                <select x-model="importType" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    <option value="students">Students - Import student records</option>
                    <option value="panel_members">Panel Members - Import professor/panelist data</option>
                    <option value="groups">Groups - Import groups with students and panels</option>
                </select>
                <div class="mt-2 flex gap-2">
                    <button type="button" @click="importType = 'students'" class="px-3 py-1 text-sm bg-blue-500 text-white rounded">Set Students</button>
                    <button type="button" @click="importType = 'panel_members'" class="px-3 py-1 text-sm bg-emerald-500 text-white rounded">Set Panel Members</button>
                    <button type="button" @click="importType = 'groups'" class="px-3 py-1 text-sm bg-purple-500 text-white rounded">Set Groups</button>
                </div>
            </div>

            <!-- File Upload -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Upload File (CSV or Excel)</label>
                <div class="relative">
                    <input type="file" @change="handleFileSelect" accept=".csv,.xlsx,.xls" class="hidden" x-ref="fileInput">
                    <div @click="$refs.fileInput.click()" 
                         @dragover.prevent="isDragging = true" 
                         @dragleave.prevent="isDragging = false"
                         @drop.prevent="handleDrop($event)"
                         :class="isDragging ? 'border-blue-500 bg-blue-50' : 'border-gray-300 hover:border-gray-400'"
                         class="border-2 border-dashed rounded-xl p-8 text-center cursor-pointer transition-all">
                        <div x-show="!selectedFile">
                            <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                            </div>
                            <p class="text-gray-600 mb-1">Drag and drop your file here, or <span class="text-blue-600 font-medium">browse</span></p>
                            <p class="text-sm text-gray-400">Supported: CSV, XLSX, XLS (max 10MB)</p>
                        </div>
                        <div x-show="selectedFile" class="flex items-center justify-center">
                            <div class="p-3 bg-green-100 rounded-lg mr-4">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="text-left">
                                <p class="font-medium text-gray-900" x-text="selectedFile?.name"></p>
                                <p class="text-sm text-gray-500" x-text="formatFileSize(selectedFile?.size)"></p>
                            </div>
                            <button type="button" @click.stop="removeFile()" class="ml-4 p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preview / Import Button -->
            <div class="flex gap-3">
                <button @click="previewData()"
                        :disabled="!selectedFile || !importType || !selectedSchoolYearId || previewing"
                        class="flex-1 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-xl transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center">
                    <svg x-show="previewing" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="previewing ? 'Previewing...' : 'Preview Data'"></span>
                </button>
            </div>

            <!-- Template Download -->
            <div class="mt-6 p-5 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border border-gray-200">
                <div class="flex items-center mb-4">
                    <div class="p-2 bg-emerald-100 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Download Excel Templates</p>
                        <p class="text-sm text-gray-500">Get the template file with sample data and clear column headers</p>
                    </div>
                </div>
                
                <div class="mb-2">
                    <p class="text-sm text-gray-500">Current import type: <span class="font-medium text-blue-600" x-text="importType"></span></p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <!-- Students Template -->
                    <a x-show="importType === 'students'" href="/api/import/template/students" 
                       class="flex items-center p-3 bg-white rounded-xl border border-gray-200 hover:border-blue-400 hover:shadow-md transition-all group">
                        <div class="p-2 bg-blue-100 rounded-lg mr-3 group-hover:bg-blue-500 transition-colors">
                            <svg class="w-5 h-5 text-blue-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 text-sm">Students Template</p>
                            <p class="text-xs text-gray-500">Name, Specialization</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                    </a>

                    <!-- Panel Members Template -->
                    <a x-show="importType === 'panel_members'" href="/api/import/template/panel-members" 
                       class="flex items-center p-3 bg-white rounded-xl border border-gray-200 hover:border-emerald-400 hover:shadow-md transition-all group">
                        <div class="p-2 bg-emerald-100 rounded-lg mr-3 group-hover:bg-emerald-500 transition-colors">
                            <svg class="w-5 h-5 text-emerald-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 text-sm">Panel Members Template</p>
                            <p class="text-xs text-gray-500">Email, Specialization</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                    </a>

                    <!-- Groups Template -->
                    <a x-show="importType === 'groups'" href="/api/import/template/groups" 
                       class="flex items-center p-3 bg-white rounded-xl border border-gray-200 hover:border-purple-400 hover:shadow-md transition-all group">
                        <div class="p-2 bg-purple-100 rounded-lg mr-3 group-hover:bg-purple-500 transition-colors">
                            <svg class="w-5 h-5 text-purple-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 text-sm">Groups Template</p>
                            <p class="text-xs text-gray-500">Students, Panels, Capstone Level</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                    </a>
                </div>

                <!-- Column Reference -->
                <div x-show="importType" class="mt-4 p-3 bg-white rounded-lg border border-gray-200">
                    <p class="text-xs font-medium text-gray-500 uppercase mb-2">Required Columns for <span x-text="importType.replace('_', ' ')"></span>:</p>
                    <div class="flex flex-wrap gap-2">
                        <template x-if="importType === 'students'">
                            <template x-for="col in ['name', 'specialization']">
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full" x-text="col"></span>
                            </template>
                        </template>
                        <template x-if="importType === 'panel_members'">
                            <template x-for="col in ['email', 'specialization']">
                                <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-xs rounded-full" x-text="col"></span>
                            </template>
                        </template>
                        <template x-if="importType === 'groups'">
                            <template x-for="col in ['name_of_students_per_group', 'advisee/chair_(clsu2_email)', 'panel_1_(clsu2_email)', 'panel_2_(clsu2_email)', 'capstone_level']">
                                <span class="px-2 py-1 bg-purple-100 text-purple-700 text-xs rounded-full" x-text="col"></span>
                            </template>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div x-show="showPreview" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" style="display: none;">
        <div class="bg-white rounded-2xl shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-hidden animate-slide-up">
            <!-- Preview Header -->
            <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-blue-500 to-purple-600">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-white">Import Preview</h3>
                        <p class="text-white/80 text-sm">Review the data before importing</p>
                    </div>
                    <button @click="closePreview()" class="p-2 bg-white/20 rounded-lg hover:bg-white/30 transition-colors">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Preview Content -->
            <div class="p-6 overflow-y-auto max-h-[60vh]">
                <!-- Summary Stats -->
                <div class="grid grid-cols-4 gap-4 mb-6">
                    <div class="text-center p-4 bg-blue-50 rounded-xl">
                        <p class="text-2xl font-bold text-blue-600" x-text="previewData?.summary?.new_students || 0"></p>
                        <p class="text-xs text-gray-500">New Students</p>
                    </div>
                    <div class="text-center p-4 bg-emerald-50 rounded-xl">
                        <p class="text-2xl font-bold text-emerald-600" x-text="previewData?.summary?.new_panel_members || 0"></p>
                        <p class="text-xs text-gray-500">New Panel Members</p>
                    </div>
                    <div class="text-center p-4 bg-purple-50 rounded-xl">
                        <p class="text-2xl font-bold text-purple-600" x-text="previewData?.summary?.new_groups || 0"></p>
                        <p class="text-xs text-gray-500">New Groups</p>
                    </div>
                    <div class="text-center p-4 bg-yellow-50 rounded-xl">
                        <p class="text-2xl font-bold text-yellow-600" x-text="(previewData?.summary?.duplicates_count || 0) + (previewData?.summary?.errors_count || 0)"></p>
                        <p class="text-xs text-gray-500">Skipped</p>
                    </div>
                </div>

                <!-- Duplicates Section -->
                <div x-show="previewData?.duplicates?.length > 0" class="mb-6">
                    <h4 class="text-sm font-semibold text-yellow-700 mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        Duplicates Found (Will be skipped)
                    </h4>
                    <div class="bg-yellow-50 rounded-xl border border-yellow-200 overflow-hidden">
                        <table class="w-full text-sm">
                            <thead class="bg-yellow-100">
                                <tr>
                                    <th class="px-4 py-2 text-left text-yellow-800">Type</th>
                                    <th class="px-4 py-2 text-left text-yellow-800">Identifier</th>
                                    <th class="px-4 py-2 text-left text-yellow-800">Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(dup, index) in previewData?.duplicates" :key="index">
                                    <tr class="border-t border-yellow-200">
                                        <td class="px-4 py-2 capitalize" x-text="dup?.type?.replace('_', ' ')"></td>
                                        <td class="px-4 py-2 font-medium" x-text="dup?.identifier"></td>
                                        <td class="px-4 py-2 text-gray-600" x-text="dup?.message"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Errors Section -->
                <div x-show="previewData?.errors?.length > 0" class="mb-6">
                    <h4 class="text-sm font-semibold text-red-700 mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Validation Errors (Will be skipped)
                    </h4>
                    <div class="bg-red-50 rounded-xl border border-red-200 p-3 space-y-2">
                        <template x-for="(error, index) in previewData?.errors" :key="index">
                            <p class="text-sm text-red-700 flex items-center">
                                <span class="w-6 h-6 bg-red-200 rounded-full flex items-center justify-center mr-2 text-xs" x-text="index + 1"></span>
                                <span x-text="error"></span>
                            </p>
                        </template>
                    </div>
                </div>

                <!-- New Data Preview -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">New Data to Import</h4>
                    
                    <!-- Students Preview -->
                    <div x-show="previewData?.preview?.students?.length > 0" class="mb-4">
                        <h5 class="text-xs font-medium text-blue-600 mb-2 uppercase">Students</h5>
                        <div class="bg-blue-50 rounded-xl border border-blue-200 overflow-hidden">
                            <table class="w-full text-sm">
                                <thead class="bg-blue-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-blue-800">#</th>
                                        <th class="px-4 py-2 text-left text-blue-800">Name</th>
                                        <th class="px-4 py-2 text-left text-blue-800">Specialization</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(student, index) in previewData?.preview?.students" :key="index">
                                        <tr class="border-t border-blue-200">
                                            <td class="px-4 py-2 text-gray-500" x-text="index + 1"></td>
                                            <td class="px-4 py-2 font-medium" x-text="student?.name"></td>
                                            <td class="px-4 py-2" x-text="student?.specialization || 'N/A'"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Panel Members Preview -->
                    <div x-show="previewData?.preview?.panel_members?.length > 0" class="mb-4">
                        <h5 class="text-xs font-medium text-emerald-600 mb-2 uppercase">Panel Members</h5>
                        <div class="bg-emerald-50 rounded-xl border border-emerald-200 overflow-hidden">
                            <table class="w-full text-sm">
                                <thead class="bg-emerald-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-emerald-800">#</th>
                                        <th class="px-4 py-2 text-left text-emerald-800">Email</th>
                                        <th class="px-4 py-2 text-left text-emerald-800">Specialization</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(panel, index) in previewData?.preview?.panel_members" :key="index">
                                        <tr class="border-t border-emerald-200">
                                            <td class="px-4 py-2 text-gray-500" x-text="index + 1"></td>
                                            <td class="px-4 py-2 font-medium" x-text="panel?.email"></td>
                                            <td class="px-4 py-2" x-text="panel?.specialization"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Groups Preview -->
                    <div x-show="previewData?.preview?.groups?.length > 0">
                        <h5 class="text-xs font-medium text-purple-600 mb-2 uppercase">Groups</h5>
                        <div class="bg-purple-50 rounded-xl border border-purple-200 overflow-hidden">
                            <table class="w-full text-sm">
                                <thead class="bg-purple-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-purple-800">#</th>
                                        <th class="px-4 py-2 text-left text-purple-800">Students</th>
                                        <th class="px-4 py-2 text-left text-purple-800">Chair/Adviser</th>
                                        <th class="px-4 py-2 text-left text-purple-800">Panels</th>
                                        <th class="px-4 py-2 text-left text-purple-800">Level</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(group, index) in previewData?.preview?.groups" :key="index">
                                        <tr class="border-t border-purple-200">
                                            <td class="px-4 py-2 text-gray-500" x-text="index + 1"></td>
                                            <td class="px-4 py-2 font-medium" x-text="group?.student_names?.substring(0, 50) + (group?.student_names?.length > 50 ? '...' : '')"></td>
                                            <td class="px-4 py-2" x-text="group?.chair_email"></td>
                                            <td class="px-4 py-2 text-xs" x-text="[group?.panel1_email, group?.panel2_email].filter(Boolean).join(', ')"></td>
                                            <td class="px-4 py-2">
                                                <span class="px-2 py-1 bg-purple-200 text-purple-700 rounded-full text-xs" x-text="group?.capstone_level"></span>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preview Footer -->
            <div class="p-6 border-t border-gray-100 bg-gray-50 flex justify-between items-center">
                <button @click="cancelImport()" 
                        :disabled="confirming"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors disabled:opacity-50">
                    Cancel
                </button>
                <button @click="confirmImport()" 
                        :disabled="confirming"
                        class="px-6 py-2 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-medium rounded-xl shadow-lg disabled:opacity-50 flex items-center">
                    <svg x-show="confirming" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="confirming ? 'Importing...' : 'Confirm Import'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Import Results -->
    <div x-show="importResult" class="mt-6 animate-slide-up">
        <div :class="importResult?.success ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'" class="rounded-2xl border p-6">
            <div class="flex items-start">
                <div :class="importResult?.success ? 'bg-green-100' : 'bg-red-100'" class="p-3 rounded-xl mr-4">
                    <svg x-show="importResult?.success" class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <svg x-show="!importResult?.success" class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <div>
                    <h3 :class="importResult?.success ? 'text-green-800' : 'text-red-800'" class="font-semibold text-lg mb-1" x-text="importResult?.success ? 'Import Successful!' : 'Import Failed'"></h3>
                    <p :class="importResult?.success ? 'text-green-600' : 'text-red-600'" class="text-sm" x-text="importResult?.message"></p>
                    
                    <div x-show="importResult?.success && importResult?.stats" class="mt-4 grid grid-cols-3 gap-4">
                        <div class="text-center p-3 bg-white rounded-lg">
                            <p class="text-2xl font-bold text-gray-900" x-text="importResult?.stats?.total || 0">0</p>
                            <p class="text-xs text-gray-500">Total Rows</p>
                        </div>
                        <div class="text-center p-3 bg-white rounded-lg">
                            <p class="text-2xl font-bold text-green-600" x-text="importResult?.stats?.imported || 0">0</p>
                            <p class="text-xs text-gray-500">Imported</p>
                        </div>
                        <div class="text-center p-3 bg-white rounded-lg">
                            <p class="text-2xl font-bold text-red-600" x-text="importResult?.stats?.failed || 0">0</p>
                            <p class="text-xs text-gray-500">Failed</p>
                        </div>
                    </div>

                    <template x-if="importResult?.success && importResult?.stats && importResult?.stats?.imported === 0">
                        <div class="mt-4 p-3 bg-yellow-50 border border-yellow-100 rounded-lg text-sm text-yellow-700">
                            No records were imported. Common causes:
                            <ul class="list-disc list-inside mt-2">
                                <li>The file contains only headers and no data rows.</li>
                                <li>Wrong delimiter (use comma ","). Try opening the CSV in a text editor.</li>
                                <li>Encoding issues — ensure UTF-8 without BOM.</li>
                            </ul>
                            <div class="mt-2">
                                <a href="/api/import/template/" class="text-blue-600 underline">Download the template</a>
                            </div>
                        </div>
                    </template>

                    <template x-if="importResult?.error && !importResult?.success">
                        <div class="mt-4 p-3 bg-yellow-50 border border-yellow-100 rounded-lg text-sm text-yellow-700">
                            <strong>Import note:</strong> <span x-text="importResult?.error"></span>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Import History -->
    <div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up" style="animation-delay: 0.1s">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900">Recent Imports</h3>
        </div>
        <div class="divide-y divide-gray-100">
            <template x-for="item in importHistory" :key="item.id">
                <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                    <div class="flex items-center">
                        <div :class="item.success ? 'bg-green-100' : 'bg-red-100'" class="p-2 rounded-lg mr-4">
                            <svg :class="item.success ? 'text-green-600' : 'text-red-600'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900" x-text="item.filename"></p>
                            <p class="text-sm text-gray-500" x-text="item.type + ' • ' + item.records + ' records'"></p>
                        </div>
                    </div>
                    <span class="text-sm text-gray-400" x-text="item.date"></span>
                </div>
            </template>
            <div x-show="importHistory.length === 0" class="p-8 text-center text-gray-500">
                No import history yet
            </div>
        </div>
    </div>
</div>

<script>
function importData() {
    return {
        importType: 'students',
        selectedFile: null,
        selectedSchoolYearId: null,
        schoolYears: [],
        isDragging: false,
        previewing: false,
        confirming: false,
        showPreview: false,
        previewData: null,
        previewToken: null,
        importResult: null,
        importHistory: [],

        isValidFile(filename) {
            const ext = filename.toLowerCase().split('.').pop();
            return ['csv', 'xlsx', 'xls'].includes(ext);
        },

        handleFileSelect(event) {
            const file = event.target.files[0];
            if (file && this.isValidFile(file.name)) {
                this.selectedFile = file;
            }
        },

        handleDrop(event) {
            this.isDragging = false;
            const file = event.dataTransfer.files[0];
            if (file && this.isValidFile(file.name)) {
                this.selectedFile = file;
            }
        },

        removeFile() {
            this.selectedFile = null;
            this.$refs.fileInput.value = '';
        },

        formatFileSize(bytes) {
            if (!bytes) return '';
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
        },

        formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        },

        async init() {
            try {
                const response = await fetch('/api/school-years');
                if (response.ok) {
                    this.schoolYears = await response.json();
                    if (this.schoolYears.length > 0 && !this.selectedSchoolYearId) {
                        this.selectedSchoolYearId = this.schoolYears[0].id;
                    }
                }
            } catch (error) {
                console.error('Failed to load school years:', error);
            }
            
            // Debug: Auto-change import type after 2 seconds
            setTimeout(() => {
                console.log('Debug: Changing import type to panel_members');
                this.importType = 'panel_members';
            }, 2000);
            
            // Debug: Auto-change import type after 5 seconds
            setTimeout(() => {
                console.log('Debug: Changing import type to groups');
                this.importType = 'groups';
            }, 5000);
        },

        async previewData() {
            if (!this.selectedFile || !this.importType || !this.selectedSchoolYearId) return;

            this.previewing = true;
            this.previewData = null;

            try {
                const formData = new FormData();
                formData.append('file', this.selectedFile);
                formData.append('type', this.importType);
                formData.append('school_year_id', this.selectedSchoolYearId);

                const response = await fetch('/api/import/preview', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok && data.data) {
                    this.previewData = data.data;
                    this.previewToken = data.data.preview_token;
                    this.showPreview = true;
                } else {
                    alert('Preview failed: ' + (data.message || data.error || 'Unknown error'));
                    this.importResult = {
                        success: false,
                        message: data.message || 'Preview failed',
                        error: data.error || ''
                    };
                }
            } catch (error) {
                alert('Preview failed: ' + error.message);
                this.importResult = {
                    success: false,
                    message: 'Preview failed: ' + error.message
                };
            } finally {
                this.previewing = false;
            }
        },

        closePreview() {
            this.showPreview = false;
            this.previewData = null;
            this.previewToken = null;
        },

        async cancelImport() {
            if (!this.previewToken) return;

            try {
                const response = await fetch('/api/import/cancel', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ preview_token: this.previewToken })
                });

                const data = await response.json();

                if (response.ok) {
                    this.closePreview();
                } else {
                    alert(data.message || 'Failed to cancel import');
                }
            } catch (error) {
                console.error('Cancel error:', error);
                this.closePreview();
            }
        },

        async confirmImport() {
            if (!this.previewToken) {
                alert('No preview token found. Please preview the data first.');
                return;
            }

            this.confirming = true;

            try {
                const response = await fetch('/api/import/confirm', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        preview_token: this.previewToken,
                        type: this.importType,
                        school_year_id: this.selectedSchoolYearId
                    })
                });

                const data = await response.json();

                this.closePreview();

                const rowsParsed = data.data?.rows_parsed ?? 0;
                const studentsImported = data.data?.students_imported ?? 0;
                const panelsImported = data.data?.panel_members_imported ?? 0;
                const groupsImported = data.data?.groups_imported ?? 0;
                const errors = Array.isArray(data.data?.errors) ? data.data.errors : (data.data?.errors ? [data.data.errors] : []);
                const totalImported = studentsImported + panelsImported + groupsImported;

                // Show alert based on result
                if (response.ok && totalImported > 0) {
                    alert('Import Successful!\n\nImported: ' + totalImported + ' record(s)\n- Students: ' + studentsImported + '\n- Panel Members: ' + panelsImported + '\n- Groups: ' + groupsImported);
                } else if (response.ok && totalImported === 0) {
                    alert('Import completed but no records were imported.\n\nPlease check the errors and try again.');
                } else {
                    alert('Import Failed!\n\n' + (data.message || data.error || 'An error occurred during import'));
                }

                this.importResult = {
                    success: response.ok,
                    message: data.message || (response.ok ? 'Import completed successfully' : 'Import failed'),
                    stats: {
                        total: rowsParsed,
                        imported: totalImported,
                        failed: errors.length,
                        raw: data.data ?? null
                    },
                    error: data.error ?? null
                };

                if (response.ok) {
                    this.importHistory.unshift({
                        id: Date.now(),
                        filename: this.selectedFile.name,
                        type: this.importType.replace('_', ' '),
                        records: totalImported,
                        success: true,
                        date: new Date().toLocaleDateString()
                    });
                    this.removeFile();
                }
            } catch (error) {
                alert('Import failed: ' + error.message);
                this.importResult = {
                    success: false,
                    message: 'Import failed: ' + error.message
                };
            } finally {
                this.confirming = false;
            }
        },

        async importData() {
            if (!this.selectedFile || !this.importType || !this.selectedSchoolYearId) return;

            this.importing = true;
            this.importResult = null;

            try {
                const formData = new FormData();
                formData.append('file', this.selectedFile);
                formData.append('type', this.importType);
                formData.append('school_year_id', this.selectedSchoolYearId);

                const response = await fetch('/api/import', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                const data = await response.json();

                const rowsParsed = data.data?.rows_parsed ?? 0;
                const studentsImported = data.data?.students_imported ?? 0;
                const panelsImported = data.data?.panel_members_imported ?? 0;
                const groupsImported = data.data?.groups_imported ?? 0;
                const errors = Array.isArray(data.data?.errors) ? data.data.errors : (data.data?.errors ? [data.data.errors] : []);

                this.importResult = {
                    success: response.ok,
                    message: data.message || (response.ok ? 'Import completed successfully' : 'Import failed'),
                    stats: {
                        total: rowsParsed,
                        imported: studentsImported + panelsImported + groupsImported,
                        failed: errors.length,
                        raw: data.data ?? null
                    },
                    error: data.error ?? null
                };

                if (response.ok) {
                    this.importHistory.unshift({
                        id: Date.now(),
                        filename: this.selectedFile.name,
                        type: this.importType.replace('_', ' '),
                        records: studentsImported || panelsImported || groupsImported || 0,
                        success: true,
                        date: new Date().toLocaleDateString()
                    });
                    this.removeFile();
                }
            } catch (error) {
                this.importResult = {
                    success: false,
                    message: 'An error occurred during import: ' + error.message
                };
            } finally {
                this.importing = false;
            }
        }
    }
}
</script>
@endsection
