@extends('layouts.dashboard')

@section('title', 'Create Group')
@section('subtitle', 'Set up a new capstone group')

@section('content')
<div x-data="createGroupData()" class="max-w-4xl mx-auto">
    <!-- Back Button -->
    <a href="{{ route('groups.index') }}" class="inline-flex items-center text-gray-500 hover:text-gray-700 mb-6 transition-colors">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to Groups
    </a>

    <form @submit.prevent="submitForm" class="space-y-6">
        <!-- Basic Information -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up">
            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                <span class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </span>
                Basic Information
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">School Year *</label>
                    <select x-model="form.school_year_id" required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        <option value="">Select School Year</option>
                        <template x-for="sy in schoolYears" :key="sy.id">
                            <option :value="sy.id" x-text="formatSchoolYear(sy)"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Capstone Stage *</label>
                    <select x-model="form.cap_status" required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        <option value="CAP1">CAPSTONE 1</option>
                        <option value="CAP2">CAPSTONE 2</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Project Title</label>
                    <input type="text" x-model="form.project_title"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"
                           placeholder="Enter project title (optional)">
                </div>
            </div>
        </div>

        <!-- Add Students -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up" style="animation-delay: 0.1s">
            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                <span class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </span>
                Team Members
            </h3>

            <!-- Available Students -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Add Students</label>
                <div class="relative">
                    <input type="text" x-model="studentSearch" @input="searchStudents()"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"
                           placeholder="Search students by name or ID">
                </div>
                
                <!-- Search Results -->
                <div x-show="studentSearch && availableStudents.length > 0" class="mt-2 bg-white border border-gray-200 rounded-xl shadow-lg max-h-48 overflow-y-auto">
                    <template x-for="student in availableStudents.filter(s => !selectedStudents.find(ss => ss.id === s.id))" :key="student.id">
                        <button type="button" @click="addStudent(student)" 
                                class="w-full flex items-center justify-between px-4 py-3 hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center text-white font-medium mr-3"
                                     x-text="student.name.charAt(0).toUpperCase()"></div>
                                <div class="text-left">
                                    <p class="font-medium text-gray-900" x-text="student.name"></p>
                                    <p class="text-sm text-gray-500" x-text="student.specialization || 'No specialization'"></p>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Selected Students -->
            <div>
                <p class="text-sm text-gray-500 mb-3" x-text="'Selected: ' + selectedStudents.length + ' student(s)'"></p>
                <div class="flex flex-wrap gap-2">
                    <template x-for="student in selectedStudents" :key="student.id">
                        <div class="inline-flex items-center px-3 py-2 bg-purple-50 text-purple-700 rounded-lg">
                            <span class="font-medium" x-text="student.name"></span>
                            <button type="button" @click="removeStudent(student)" class="ml-2 text-purple-400 hover:text-purple-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </template>
                    <p x-show="selectedStudents.length === 0" class="text-gray-400 text-sm">No students selected</p>
                </div>
            </div>
        </div>

        <!-- Add Panel Members -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up" style="animation-delay: 0.2s">
            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                <span class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </span>
                Panel Members
            </h3>

            <!-- Available Panels -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Add Panelists</label>
                    <div class="relative">
                        <input type="text" x-model="panelSearch" @input="searchPanels()"
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"
                               placeholder="Search panel members by email">
                    </div>
                    <button type="button" onclick="testSearch()" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                        Test Search (Network)
                    </button>
                    
                    <!-- Search Results -->
                    <div x-show="panelSearch && availablePanels.length > 0" class="mt-2 bg-white border border-gray-200 rounded-xl shadow-lg max-h-48 overflow-y-auto">
                        <template x-for="panel in availablePanels.filter(p => !selectedPanels.find(sp => sp.id === p.id) && p.email.toLowerCase().includes(panelSearch.toLowerCase()))" :key="panel.id">
                            <button type="button" @click="addPanel(panel)" 
                                    class="w-full flex items-center justify-between px-4 py-3 hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-full flex items-center justify-center text-white font-medium mr-3"
                                         x-text="panel.email.charAt(0).toUpperCase()"></div>
                                    <div class="text-left">
                                        <p class="font-medium text-gray-900" x-text="panel.email"></p>
                                        <p class="text-sm text-gray-500" x-text="panel.specialization || 'Faculty'"></p>
                                    </div>
                                </div>
                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </button>
                        </template>
                    </div>
                </div>

            <!-- Selected Panels with Role Selection -->
            <div>
                <p class="text-sm text-gray-500 mb-3" x-text="'Selected: ' + selectedPanels.length + ' panelist(s)'"></p>
                <div class="space-y-2">
                        <template x-for="(panel, index) in selectedPanels" :key="panel.id">
                            <div class="flex items-center justify-between p-3 bg-emerald-50 rounded-xl">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-full flex items-center justify-center text-white font-medium mr-3"
                                         x-text="panel.email.charAt(0).toUpperCase()"></div>
                                    <div>
                                        <p class="font-medium text-gray-900" x-text="panel.email"></p>
                                        <p class="text-xs text-gray-500" x-text="panel.specialization || 'Faculty'"></p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <select x-model="panel.role" class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                        <option value="Adviser">Adviser</option>
                                        <option value="Chair">Chair</option>
                                        <option value="Critique">Critique</option>
                                    </select>
                                    <button type="button" @click="removePanel(panel)" class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    <p x-show="selectedPanels.length === 0" class="text-gray-400 text-sm">No panelists selected</p>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center justify-end space-x-4 animate-slide-up" style="animation-delay: 0.3s">
            <a href="{{ route('groups.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors">
                Cancel
            </a>
            <button type="submit" :disabled="submitting" 
                    class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-xl transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
                <svg x-show="submitting" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-text="submitting ? 'Creating...' : 'Create Group'"></span>
            </button>
        </div>
    </form>
</div>

<script>
function createGroupData() {
    return {
        form: {
            school_year_id: '',
            cap_status: 'CAP1',
            project_title: ''
        },
        errors: {},
        submitting: false,
        schoolYears: [],
        availableStudents: [],
        availablePanels: [],
        selectedStudents: [],
        selectedPanels: [],
        studentSearch: '',
        panelSearch: '',

        init() {
            this.loadSchoolYears();
            this.loadStudents();
            this.loadPanels();
        },

        async loadSchoolYears() {
            const response = await fetch('/api/school-years');
            this.schoolYears = await response.json();
        },

        async loadStudents() {
            const response = await fetch('/api/students/available');
            this.availableStudents = await response.json();
        },

        async loadPanels() {
            const response = await fetch('/api/panel-members/available');
            this.availablePanels = await response.json();
            console.log('Available panels loaded:', JSON.stringify(this.availablePanels, null, 2));
        },

        formatSchoolYear(sy) {
            const semester = this.getSemesterLabel(sy.semester);
            return 'A.Y. ' + sy.year + ' - ' + semester;
        },

        getSemesterLabel(semester) {
            if (!semester) return '1st Semester';
            const sem = String(semester).toLowerCase();
            if (sem === '1' || sem === '1st semester') return '1st Semester';
            if (sem === '2' || sem === '2nd semester') return '2nd Semester';
            return semester;
        },

        searchStudents() {
            // Filter handled in template
        },

        searchPanels() {
            // Load panels with search filter from API
            const searchTerm = this.panelSearch;
            console.log('searchPanels called with term:', searchTerm);
            
            // Show loading state
            this.availablePanels = [];
            
            fetch(`/api/panel-members/available?search=${encodeURIComponent(searchTerm)}`)
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error('HTTP error ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', JSON.stringify(data, null, 2));
                    this.availablePanels = data;
                })
                .catch(error => {
                    console.error('Error fetching panels:', error);
                    this.availablePanels = [];
                });
        },

        addStudent(student) {
            this.selectedStudents.push(student);
            this.studentSearch = '';
        },

        removeStudent(student) {
            this.selectedStudents = this.selectedStudents.filter(s => s.id !== student.id);
        },

        addPanel(panel) {
            this.selectedPanels.push({...panel, role: 'Critique'});
            this.panelSearch = '';
        },

        removePanel(panel) {
            this.selectedPanels = this.selectedPanels.filter(p => p.id !== panel.id);
        },

        async submitForm() {
            this.submitting = true;
            this.errors = {};

            try {
                const payload = {
                    ...this.form,
                    student_ids: this.selectedStudents.map(s => s.id),
                    panel_ids: this.selectedPanels.map(p => p.id),
                    panel_roles: this.selectedPanels.map(p => p.role || 'Critique')
                };

                const response = await fetch('/api/groups', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(payload)
                });

                if (response.ok) {
                    window.location.href = '{{ route("groups.index") }}';
                } else {
                    const data = await response.json();
                    this.errors = data.errors || {};
                }
            } catch (error) {
                console.error('Error creating group:', error);
            } finally {
                this.submitting = false;
            }
        }
    }
}
</script>
@endsection
