@extends('layouts.dashboard')

@section('title', 'Edit Group')
@section('subtitle', 'Update group details and members')

@section('content')
<div class="max-w-4xl mx-auto" x-data="editGroupForm()">
    <!-- Back Button -->
    <a href="{{ route('groups.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 mb-6 animate-fade-in">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to Groups
    </a>

    <!-- Loading State -->
    <div x-show="loading" class="text-center py-16">
        <div class="w-16 h-16 border-4 border-blue-500 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
        <p class="text-gray-500">Loading group details...</p>
    </div>

    <!-- Form Card -->
    <div x-show="!loading" x-cloak class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
        <!-- Header -->
        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mr-4 text-white font-bold text-xl">
                    <span x-text="group?.name?.charAt(0) || 'G'"></span>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Edit Group</h2>
                    <p class="text-sm text-gray-500" x-text="'Group #' + group?.id"></p>
                </div>
            </div>
        </div>

        <form @submit.prevent="saveGroup()" class="p-6 space-y-6">
            <!-- Basic Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Group ID Display -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Group ID</label>
                    <div class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-600" x-text="'#' + group.id"></div>
                </div>

                <!-- Project Title -->
                <div>
                    <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">Project Title</label>
                    <input type="text" id="title" x-model="group.title"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"
                           placeholder="e.g., AI-Powered Student Portal">
                </div>
            </div>

            <!-- CAPSTONE Stage -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-3">CAPSTONE Stage</label>
                <div class="grid grid-cols-2 gap-4">
                    <label class="relative cursor-pointer">
                        <input type="radio" x-model="group.cap_stage" value="1" class="peer sr-only">
                        <div class="p-4 rounded-xl border-2 border-gray-200 text-center transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-blue-300">
                            <div class="w-10 h-10 mx-auto bg-blue-100 rounded-full flex items-center justify-center mb-2">
                                <span class="text-lg font-bold text-blue-600">1</span>
                            </div>
                            <span class="font-medium text-gray-700">CAPSTONE 1</span>
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" x-model="group.cap_stage" value="2" class="peer sr-only">
                        <div class="p-4 rounded-xl border-2 border-gray-200 text-center transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-50 hover:border-emerald-300">
                            <div class="w-10 h-10 mx-auto bg-emerald-100 rounded-full flex items-center justify-center mb-2">
                                <span class="text-lg font-bold text-emerald-600">2</span>
                            </div>
                            <span class="font-medium text-gray-700">CAPSTONE 2</span>
                            <span class="text-xs text-gray-500 block">(Completed)</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Students Section -->
            <div class="border-t border-gray-100 pt-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-900">Students</h3>
                    <button type="button" @click="showStudentSearch = !showStudentSearch" class="text-sm text-blue-600 hover:text-blue-700">
                        + Add Student
                    </button>
                </div>

                <!-- Student Search -->
                <div x-show="showStudentSearch" x-transition class="mb-4 p-4 bg-gray-50 rounded-xl">
                    <input type="text" x-model="studentQuery" @input.debounce.300ms="searchStudents()"
                           placeholder="Search available students..."
                           class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    <div class="mt-2 max-h-48 overflow-y-auto space-y-1">
                        <template x-for="student in availableStudents" :key="student.id">
                            <button type="button" @click="addStudent(student)"
                                    class="w-full flex items-center p-2 hover:bg-white rounded-lg transition-colors text-left">
                                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center text-emerald-600 font-bold mr-3" x-text="student.name.charAt(0)"></div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900" x-text="student.name"></p>
                                    <p class="text-xs text-gray-500" x-text="student.specialization || 'No specialization'"></p>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Selected Students -->
                <div class="space-y-2">
                    <template x-for="student in selectedStudents" :key="student.id">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-xl flex items-center justify-center text-white font-bold mr-3" x-text="student.name.charAt(0)"></div>
                                <div>
                                    <p class="font-medium text-gray-900" x-text="student.name"></p>
                                    <p class="text-sm text-gray-500" x-text="student.specialization || 'No specialization'"></p>
                                </div>
                            </div>
                            <button type="button" @click="removeStudent(student)" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </template>
                    <div x-show="selectedStudents.length === 0" class="text-center py-4 text-gray-500">
                        No students assigned
                    </div>
                </div>
            </div>

            <!-- Panel Members Section -->
            <div class="border-t border-gray-100 pt-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-900">Panel Members</h3>
                    <button type="button" @click="showPanelSearch = !showPanelSearch" class="text-sm text-blue-600 hover:text-blue-700">
                        + Add Panel Member
                    </button>
                </div>

                <!-- Panel Search -->
                <div x-show="showPanelSearch" x-transition class="mb-4 p-4 bg-gray-50 rounded-xl">
                    <input type="text" x-model="panelQuery" @input.debounce.300ms="searchPanels()"
                           placeholder="Search panel members by email..."
                           class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    <div class="mt-2 max-h-48 overflow-y-auto space-y-1">
                        <template x-for="panel in availablePanels" :key="panel.id">
                            <button type="button" @click="addPanel(panel)"
                                    class="w-full flex items-center p-2 hover:bg-white rounded-lg transition-colors text-left">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold mr-3"
                                     :class="getPanelGradient(panel.role)" x-text="panel.email.charAt(0).toUpperCase()"></div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900" x-text="panel.email"></p>
                                    <p class="text-xs text-gray-500" x-text="panel.specialization || panel.role"></p>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Selected Panels -->
                <div class="space-y-2">
                    <template x-for="panel in selectedPanels" :key="panel.id">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold mr-3"
                                     :class="getPanelGradient(panel.role)" x-text="panel.email.charAt(0).toUpperCase()"></div>
                                <div>
                                    <p class="font-medium text-gray-900" x-text="panel.email"></p>
                                    <p class="text-sm text-gray-500" x-text="panel.specialization || ''"></p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <select x-model="panel.role" class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                                    <option value="Adviser">Adviser</option>
                                    <option value="Chair">Chair</option>
                                    <option value="Critique">Critique</option>
                                </select>
                                <button type="button" @click="removePanel(panel)" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>
                    <div x-show="selectedPanels.length === 0" class="text-center py-4 text-gray-500">
                        No panel members assigned
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-100">
                <a href="{{ route('groups.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" :disabled="saving" class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-medium rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-xl transition-all duration-300 hover:-translate-y-0.5 disabled:opacity-50">
                    <span x-show="!saving">Save Changes</span>
                    <span x-show="saving">Saving...</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function editGroupForm() {
    return {
        loading: true,
        saving: false,
        group: {},
        selectedStudents: [],
        selectedPanels: [],
        availableStudents: [],
        availablePanels: [],
        studentQuery: '',
        panelQuery: '',
        showStudentSearch: false,
        showPanelSearch: false,

        init() {
            this.loadGroup();
        },

        async loadGroup() {
            const groupId = window.location.pathname.split('/')[2];
            try {
                const response = await fetch('/api/groups/' + groupId);
                this.group = await response.json();
                this.group.title = this.group.project_title; // Map project_title to title for form binding
                this.selectedStudents = this.group.students || [];
                // Map panel_members with pivot role
                this.selectedPanels = (this.group.panel_members || []).map(p => ({
                    ...p,
                    role: p.pivot?.role || 'Critique'
                }));
            } catch (error) {
                console.error('Error:', error);
            } finally {
                this.loading = false;
            }
        },

        async searchStudents() {
            try {
                const response = await fetch('/api/students/available?search=' + this.studentQuery);
                const students = await response.json();
                this.availableStudents = students.filter(s => !this.selectedStudents.find(sel => sel.id === s.id));
            } catch (error) {
                console.error('Error:', error);
            }
        },

        async searchPanels() {
            try {
                const response = await fetch('/api/panel-members?search=' + this.panelQuery);
                const panels = await response.json();
                this.availablePanels = panels.filter(p => !this.selectedPanels.find(sel => sel.id === p.id));
            } catch (error) {
                console.error('Error:', error);
            }
        },

        addStudent(student) {
            this.selectedStudents.push(student);
            this.availableStudents = this.availableStudents.filter(s => s.id !== student.id);
        },

        removeStudent(student) {
            this.selectedStudents = this.selectedStudents.filter(s => s.id !== student.id);
        },

        addPanel(panel) {
            this.selectedPanels.push({...panel, role: panel.role || 'Critique'});
            this.availablePanels = this.availablePanels.filter(p => p.id !== panel.id);
        },

        removePanel(panel) {
            this.selectedPanels = this.selectedPanels.filter(p => p.id !== panel.id);
        },

        getPanelGradient(role) {
            const gradients = {
                'Adviser': 'bg-gradient-to-br from-purple-400 to-purple-600 text-white',
                'Chair': 'bg-gradient-to-br from-emerald-400 to-emerald-600 text-white',
                'Critique': 'bg-gradient-to-br from-orange-400 to-orange-600 text-white'
            };
            return gradients[role] || 'bg-gradient-to-br from-gray-400 to-gray-600 text-white';
        },

        getPanelBadgeClass(role) {
            const classes = { 'Adviser': 'bg-purple-100 text-purple-700', 'Chair': 'bg-emerald-100 text-emerald-700', 'Critique': 'bg-orange-100 text-orange-700' };
            return classes[role] || 'bg-gray-100 text-gray-700';
        },

        async saveGroup() {
            this.saving = true;
            try {
                const response = await fetch('/api/groups/' + this.group.id, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        project_title: this.group.title,
                        cap_stage: parseInt(this.group.cap_stage),
                        student_ids: this.selectedStudents.map(s => s.id),
                        panel_ids: this.selectedPanels.map(p => p.id),
                        panel_roles: this.selectedPanels.map(p => p.role || 'Critique')
                    })
                });

                if (response.ok) {
                    window.location.href = '/groups';
                } else {
                    alert('Failed to save group');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error saving group');
            } finally {
                this.saving = false;
            }
        }
    }
}
</script>
@endsection
