@extends('layouts.dashboard')

@section('title', 'Add Evaluation')
@section('subtitle', 'Record grade for a group')

@section('content')
<div class="max-w-3xl mx-auto" x-data="evaluationForm()">
    <!-- Back Button -->
    <a href="{{ route('evaluations.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 mb-6 animate-fade-in">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to Evaluations
    </a>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
        <!-- Header -->
        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-amber-50 to-orange-50">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Add Grade</h2>
                    <p class="text-sm text-gray-500">Record panelist grade for a group's CAPSTONE stage</p>
                </div>
            </div>
        </div>

        <form @submit.prevent="submitForm" class="p-6 space-y-6">
            <!-- Success Message -->
            <div x-show="successMessage" x-cloak
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 class="p-4 bg-green-50 border border-green-200 rounded-xl flex items-center text-green-800">
                <svg class="w-5 h-5 mr-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span x-text="successMessage"></span>
            </div>

            <!-- Group Selection -->
            <div class="animate-slide-up" style="animation-delay: 0.1s">
                <label class="block text-sm font-semibold text-gray-700 mb-3">Select Group</label>
                <div class="relative">
                    <select name="group_id" x-model="selectedGroup" @change="loadGroupInfo()" 
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all @error('group_id') border-red-500 @enderror" required>
                        <option value="">Choose a group...</option>
                        <template x-for="group in groups" :key="group.id">
                            <option :value="group.id" x-text="(group.project_title || 'Untitled Project') + ' (Group #' + group.id + ')'"></option>
                        </template>
                    </select>
                </div>
                @error('group_id')
                    <p class="mt-2 text-sm text-red-600 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Group Info Preview -->
            <div x-show="groupInfo" x-transition class="p-4 bg-gray-50 rounded-xl animate-fade-in">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg flex items-center justify-center text-white font-bold mr-3">
                            <span x-text="'#' + selectedGroup"></span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900" x-text="groupInfo?.project_title || 'Untitled Project'"></p>
                            <p class="text-sm text-gray-500"><span x-text="groupInfo?.students_count || groupInfo?.students?.length || 0"></span> students</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 text-xs font-semibold rounded-full" 
                          :class="getCapBadgeClass(groupInfo?.cap_stage)"
                          x-text="'CAPSTONE ' + (groupInfo?.cap_stage || 1)"></span>
                </div>
            </div>

            <!-- Panel Member Selection -->
            <div class="animate-slide-up" style="animation-delay: 0.15s">
                <label class="block text-sm font-semibold text-gray-700 mb-3">Select Panel Member</label>
                <div class="relative">
                    <select name="panel_member_id" x-model="selectedPanelMember"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all @error('panel_member_id') border-red-500 @enderror" required>
                        <option value="">Choose a panel member...</option>
                        <template x-for="pm in filteredPanelMembers" :key="pm.id">
                            <option :value="pm.id" x-text="pm.email + (pm.pivot?.role ? ' (' + pm.pivot.role + ')' : '')"></option>
                        </template>
                    </select>
                </div>
                <p x-show="selectedGroup && filteredPanelMembers.length === 0" class="mt-2 text-sm text-amber-600 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    No panel members assigned to this group. Please assign panel members first.
                </p>
                @error('panel_member_id')
                    <p class="mt-2 text-sm text-red-600 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- CAPSTONE Stage Selection -->
            <div class="animate-slide-up" style="animation-delay: 0.2s">
                <label class="block text-sm font-semibold text-gray-700 mb-3">CAPSTONE Stage</label>
                <div class="grid grid-cols-2 gap-4">
                    <label class="relative cursor-pointer">
                        <input type="radio" name="cap_stage" value="1" x-model="capStage" class="peer sr-only" {{ old('cap_stage', 1) == 1 ? 'checked' : '' }}>
                        <div class="p-4 rounded-xl border-2 border-gray-200 text-center transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-blue-300">
                            <div class="w-12 h-12 mx-auto bg-blue-100 rounded-full flex items-center justify-center mb-2">
                                <span class="text-xl font-bold text-blue-600">1</span>
                            </div>
                            <span class="font-medium text-gray-700">CAPSTONE 1</span>
                            <p class="text-xs text-gray-500 mt-1">Concept Stage</p>
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" name="cap_stage" value="2" x-model="capStage" class="peer sr-only" {{ old('cap_stage') == 2 ? 'checked' : '' }}>
                        <div class="p-4 rounded-xl border-2 border-gray-200 text-center transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-50 hover:border-emerald-300">
                            <div class="w-12 h-12 mx-auto bg-emerald-100 rounded-full flex items-center justify-center mb-2">
                                <span class="text-xl font-bold text-emerald-600">2</span>
                            </div>
                            <span class="font-medium text-gray-700">CAPSTONE 2</span>
                            <p class="text-xs text-gray-500 mt-1">Application & Publication</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Grade Input -->
            <div class="animate-slide-up" style="animation-delay: 0.3s">
                <label for="grade" class="block text-sm font-semibold text-gray-700 mb-3">Grade</label>
                <div class="flex items-center space-x-4">
                    <input type="number" id="grade" name="grade" x-model="grade"
                           min="0" max="100" step="0.01"
                           class="w-32 px-4 py-3 text-center text-2xl font-bold rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all @error('grade') border-red-500 @enderror"
                           placeholder="85" required>
                    <div class="flex-1">
                        <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full transition-all duration-300 rounded-full"
                                 :class="getGradeBarClass(grade)"
                                 :style="'width: ' + (grade || 0) + '%'"></div>
                        </div>
                        <p class="text-sm mt-2" :class="getGradeTextClass(grade)" x-text="getGradeLabel(grade)"></p>
                    </div>
                </div>
                @error('grade')
                    <p class="mt-2 text-sm text-red-600 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Evaluation Date -->
            <div class="animate-slide-up" style="animation-delay: 0.35s">
                <label for="evaluation_date" class="block text-sm font-semibold text-gray-700 mb-2">Evaluation Date</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <input type="date" id="evaluation_date" x-model="evaluationDate"
                           class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all">
                </div>
            </div>

            <!-- Remarks -->
            <div class="animate-slide-up" style="animation-delay: 0.4s">
                <label for="remarks" class="block text-sm font-semibold text-gray-700 mb-2">Remarks <span class="text-gray-400 font-normal">(Optional)</span></label>
                <textarea id="remarks" x-model="remarks" rows="4"
                          class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all resize-none"
                          placeholder="Add any notes or feedback about this evaluation..."></textarea>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-100 animate-slide-up" style="animation-delay: 0.5s">
                <a href="{{ route('evaluations.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" :disabled="submitting" class="px-6 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-medium rounded-xl shadow-lg shadow-amber-500/30 hover:shadow-xl transition-all duration-300 hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2">
                    <template x-if="submitting">
                        <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </template>
                    <span x-text="submitting ? 'Saving...' : 'Save Evaluation'"></span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function evaluationForm() {
    return {
        groups: [],
        selectedGroup: '',
        selectedPanelMember: '',
        groupInfo: null,
        filteredPanelMembers: [],
        capStage: '1',
        grade: '',
        evaluationDate: new Date().toISOString().split('T')[0],
        remarks: '',
        submitting: false,
        successMessage: '',
        errors: {},

        init() {
            this.loadGroups();
        },

        async loadGroups() {
            try {
                const response = await fetch('/api/groups');
                this.groups = await response.json();
            } catch (error) {
                console.error('Error loading groups:', error);
            }
        },

        async loadGroupInfo() {
            if (!this.selectedGroup) {
                this.groupInfo = null;
                this.filteredPanelMembers = [];
                this.selectedPanelMember = '';
                return;
            }
            
            try {
                const response = await fetch('/api/groups/' + this.selectedGroup);
                this.groupInfo = await response.json();
                this.filteredPanelMembers = this.groupInfo.panel_members || [];
                this.selectedPanelMember = '';
            } catch (error) {
                console.error('Error loading group info:', error);
                const group = this.groups.find(g => g.id == this.selectedGroup);
                this.groupInfo = group;
                this.filteredPanelMembers = [];
            }
        },

        async submitForm() {
            this.submitting = true;
            this.errors = {};
            this.successMessage = '';

            try {
                const response = await fetch('/api/evaluations', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        group_id: this.selectedGroup,
                        panel_member_id: this.selectedPanelMember,
                        cap_stage: this.capStage,
                        grade: this.grade,
                        evaluation_date: this.evaluationDate,
                        remarks: this.remarks
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    if (data.errors) {
                        this.errors = data.errors;
                    } else {
                        alert(data.message || 'An error occurred');
                    }
                } else {
                    this.successMessage = 'Evaluation saved successfully!';
                    setTimeout(() => {
                        window.location.href = '{{ route("evaluations.index") }}';
                    }, 1000);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Network error. Please try again.');
            } finally {
                this.submitting = false;
            }
        },

        getCapBadgeClass(stage) {
            const classes = {
                1: 'bg-blue-100 text-blue-700',
                2: 'bg-emerald-100 text-emerald-700'
            };
            return classes[stage] || 'bg-gray-100 text-gray-700';
        },

        getGradeBarClass(grade) {
            if (grade >= 90) return 'bg-gradient-to-r from-green-400 to-green-500';
            if (grade >= 80) return 'bg-gradient-to-r from-blue-400 to-blue-500';
            if (grade >= 75) return 'bg-gradient-to-r from-yellow-400 to-yellow-500';
            return 'bg-gradient-to-r from-red-400 to-red-500';
        },

        getGradeTextClass(grade) {
            if (grade >= 90) return 'text-green-600 font-medium';
            if (grade >= 80) return 'text-blue-600 font-medium';
            if (grade >= 75) return 'text-yellow-600 font-medium';
            if (grade) return 'text-red-600 font-medium';
            return 'text-gray-400';
        },

        getGradeLabel(grade) {
            if (!grade) return 'Enter a grade (0-100)';
            if (grade >= 90) return 'Excellent';
            if (grade >= 80) return 'Very Good';
            if (grade >= 75) return 'Good';
            if (grade >= 70) return 'Satisfactory';
            return 'Needs Improvement';
        }
    }
}
</script>
@endsection
