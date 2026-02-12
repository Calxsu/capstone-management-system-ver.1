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
                        <option :value="year.id" x-text="year.year + ' (' + year.start_date + ' - ' + year.end_date + ')'"></option>
                    </template>
                </select>
                <p x-show="schoolYears.length === 0" class="text-sm text-gray-500 mt-1">Loading available school years...</p>
            </div>

            <!-- Import Type Selection -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Select Import Type</label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <button type="button" @click="importType = 'students'" 
                            :class="importType === 'students' ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-500/20' : 'border-gray-200 hover:border-gray-300'"
                            class="p-4 rounded-xl border-2 transition-all text-left">
                        <div class="flex items-center mb-2">
                            <div :class="importType === 'students' ? 'bg-blue-500' : 'bg-gray-400'" class="p-2 rounded-lg mr-3 transition-colors">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <span class="font-semibold text-gray-900">Students</span>
                        </div>
                        <p class="text-sm text-gray-500">Import student records</p>
                    </button>

                    <button type="button" @click="importType = 'panel_members'" 
                            :class="importType === 'panel_members' ? 'border-emerald-500 bg-emerald-50 ring-2 ring-emerald-500/20' : 'border-gray-200 hover:border-gray-300'"
                            class="p-4 rounded-xl border-2 transition-all text-left">
                        <div class="flex items-center mb-2">
                            <div :class="importType === 'panel_members' ? 'bg-emerald-500' : 'bg-gray-400'" class="p-2 rounded-lg mr-3 transition-colors">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <span class="font-semibold text-gray-900">Panel Members</span>
                        </div>
                        <p class="text-sm text-gray-500">Import professor/panelist data</p>
                    </button>

                    <button type="button" @click="importType = 'groups'" 
                            :class="importType === 'groups' ? 'border-purple-500 bg-purple-50 ring-2 ring-purple-500/20' : 'border-gray-200 hover:border-gray-300'"
                            class="p-4 rounded-xl border-2 transition-all text-left">
                        <div class="flex items-center mb-2">
                            <div :class="importType === 'groups' ? 'bg-purple-500' : 'bg-gray-400'" class="p-2 rounded-lg mr-3 transition-colors">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <span class="font-semibold text-gray-900">Groups</span>
                        </div>
                        <p class="text-sm text-gray-500">Import groups with students and panels</p>
                    </button>
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

            <!-- Template Download -->
            <div class="mb-6 p-5 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border border-gray-200">
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
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <!-- Students Template -->
                    <a href="/api/import/template/students" 
                       class="flex items-center p-3 bg-white rounded-xl border border-gray-200 hover:border-blue-400 hover:shadow-md transition-all group">
                        <div class="p-2 bg-blue-100 rounded-lg mr-3 group-hover:bg-blue-500 transition-colors">
                            <svg class="w-5 h-5 text-blue-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 text-sm">Students Template</p>
                            <p class="text-xs text-gray-500">Name, Specialization...</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                    </a>

                    <!-- Panel Members Template -->
                            <a href="/api/import/template/panel-members" 
                               class="flex items-center p-3 bg-white rounded-xl border border-gray-200 hover:border-emerald-400 hover:shadow-md transition-all group">
                                <div class="p-2 bg-emerald-100 rounded-lg mr-3 group-hover:bg-emerald-500 transition-colors">
                                    <svg class="w-5 h-5 text-emerald-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900 text-sm">Panel Members (Professors)</p>
                                    <p class="text-xs text-gray-500">Email, Specialization, Status</p>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                            </a>

                    <!-- Groups Template -->
                    <a href="/api/import/template/groups" 
                       class="flex items-center p-3 bg-white rounded-xl border border-gray-200 hover:border-purple-400 hover:shadow-md transition-all group">
                        <div class="p-2 bg-purple-100 rounded-lg mr-3 group-hover:bg-purple-500 transition-colors">
                            <svg class="w-5 h-5 text-purple-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 text-sm">Groups Template</p>
                            <p class="text-xs text-gray-500">Students, Chair + Adviser, Panels</p>
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
                            <template x-for="col in ['name', 'specialization*', 'status*']">
                                <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full" x-text="col"></span>
                            </template>
                        </template>
                        <template x-if="importType === 'panel_members'">
                            <template x-for="col in ['email', 'specialization*', 'status*']">
                                <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full" x-text="col"></span>
                            </template>
                        </template>
                        <template x-if="importType === 'groups'">
                            <template x-for="col in ['name_of_students_per_group', 'adviser/chair_(clsu2_email)', 'panel_1_(clsu2_email)', 'panel_2_(clsu2_email)', 'cap_status*']">
                                <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full" x-text="col"></span>
                            </template>
                        </template>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">* Optional fields</p>
                </div>
            </div>

            <!-- Import Button -->
            <button @click="importData()"
                    :disabled="!selectedFile || !importType || !selectedSchoolYearId || importing"
                    class="w-full py-3 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-medium rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-xl transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center">
                <svg x-show="importing" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-text="importing ? 'Importing...' : 'Start Import'"></span>
            </button>
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
        importing: false,
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

        getTemplateUrl() {
            const typeMap = {
                'students': 'students',
                'panel_members': 'panel-members',
                'groups': 'groups'
            };
            return '/api/import/template/' + typeMap[this.importType];
        },

        async init() {
            try {
                const response = await fetch('/api/school-years');
                if (response.ok) {
                    this.schoolYears = await response.json();
                    // Auto-select the first school year if available
                    if (this.schoolYears.length > 0 && !this.selectedSchoolYearId) {
                        this.selectedSchoolYearId = this.schoolYears[0].id;
                    }
                }
            } catch (error) {
                console.error('Failed to load school years:', error);
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

                // Normalize API response into UI-friendly stats: total, imported, failed
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
                        records: data.data?.students_imported || data.data?.panel_members_imported || data.data?.groups_imported || 0,
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
