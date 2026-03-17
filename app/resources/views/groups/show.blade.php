@extends('layouts.dashboard')

@section('title', 'Group Details')
@section('subtitle', 'View group information and members')

@section('content')
<div class="max-w-5xl mx-auto" x-data="groupDetails()">
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

    <!-- Group Content -->
    <div x-show="!loading" x-cloak>
        <!-- Header Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6 animate-slide-up">
            <div class="p-6 bg-gradient-to-r from-blue-500 to-indigo-600">
                <div class="flex items-start justify-between">
                    <div class="flex items-center">
                        <div class="w-16 h-16 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center text-white text-2xl font-bold mr-4">
                            <span x-text="(group?.project_title || group?.name || 'Group #' + group?.id)?.charAt(0) || 'G'"></span>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-white" x-text="group?.project_title || group?.name || 'Group #' + group?.id"></h1>
                            <p class="text-blue-100 mt-1" x-text="'School Year: ' + (group?.school_year?.year || 'N/A')"></p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <a :href="'/groups/' + group?.id + '/edit'" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-xl backdrop-blur transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Stats Row -->
            <div class="grid grid-cols-6 divide-x divide-gray-100">
                <div class="p-4 text-center">
                    <p class="text-2xl font-bold text-gray-900" x-text="group?.students?.length || 0"></p>
                    <p class="text-sm text-gray-500">Students</p>
                </div>
                <div class="p-4 text-center">
                    <p class="text-2xl font-bold text-gray-900" x-text="group?.panel_members?.length || 0"></p>
                    <p class="text-sm text-gray-500">Panel Members</p>
                </div>
                <div class="p-4 text-center">
                    <p class="text-2xl font-bold text-blue-600" x-text="getCap1Average()"></p>
                    <p class="text-sm text-gray-500">CAP 1 Avg</p>
                </div>
                <div class="p-4 text-center">
                    <p class="text-2xl font-bold text-purple-600" x-text="getCap2Average()"></p>
                    <p class="text-sm text-gray-500">CAP 2 Avg</p>
                </div>
                <div class="p-4 text-center">
                    <span class="px-3 py-1 text-lg font-bold rounded-full" 
                          :class="getCapBadgeClass(group?.cap_stage)"
                          x-text="'CAP ' + (group?.cap_stage || 1)"></span>
                    <p class="text-sm text-gray-500 mt-1">Current Stage</p>
                </div>
                <div class="p-4 text-center">
                    <span class="px-3 py-1 text-sm font-semibold rounded-full"
                          :class="group?.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'"
                          x-text="group?.status || 'Active'"></span>
                    <p class="text-sm text-gray-500 mt-1">Status</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Students Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up" style="animation-delay: 0.1s">
                <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        Students
                    </h3>
                    <span class="px-2 py-1 text-xs font-semibold bg-emerald-100 text-emerald-700 rounded-full" x-text="(group?.students?.length || 0) + ' members'"></span>
                </div>
                <div class="divide-y divide-gray-50">
                    <template x-for="student in group?.students || []" :key="student.id">
                        <div class="p-4 hover:bg-gray-50 transition-colors flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-xl flex items-center justify-center text-white font-bold mr-3">
                                <span x-text="student.name?.charAt(0)"></span>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900" x-text="student.name"></p>
                                <p class="text-sm text-gray-500" x-text="student.specialization || 'No specialization'"></p>
                            </div>
                            <span class="text-xs text-gray-400" x-text="student.course || ''"></span>
                        </div>
                    </template>
                    <div x-show="!group?.students?.length" class="p-8 text-center text-gray-500">
                        No students assigned
                    </div>
                </div>
            </div>

            <!-- Panel Members Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up" style="animation-delay: 0.2s">
                <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Panel Members
                    </h3>
                    <span class="px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-700 rounded-full" x-text="(group?.panel_members?.length || 0) + ' members'"></span>
                </div>
                <div class="divide-y divide-gray-50">
                    <template x-for="panel in group?.panel_members || []" :key="panel.id">
                        <div class="p-4 hover:bg-gray-50 transition-colors flex items-center">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold mr-3"
                                 :class="getPanelGradient(panel.pivot?.role || panel.role)">
                                <span x-text="panel.email?.charAt(0)?.toUpperCase()"></span>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900" x-text="panel.email"></p>
                                <p class="text-sm text-gray-500" x-text="panel.specialization || 'Faculty'"></p>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full"
                                  :class="getPanelBadgeClass(panel.pivot?.role || panel.role)"
                                  x-text="panel.pivot?.role || panel.role"></span>
                        </div>
                    </template>
                    <div x-show="!group?.panel_members?.length" class="p-8 text-center text-gray-500">
                        No panel members assigned
                    </div>
                </div>
            </div>
        </div>

        <!-- Evaluations History -->
        <div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up" style="animation-delay: 0.3s">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    Evaluation History
                </h3>
                <a :href="'/evaluations/create?group_id=' + group?.id" class="text-sm text-blue-600 hover:text-blue-700 font-medium">+ Add Evaluation</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full" x-show="evaluations.length > 0">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Professor</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">CAP Stage</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Grade</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="eval in evaluations" :key="eval.id">
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900 font-medium" x-text="eval.panel_member?.email || 'Panel Member'"></td>
                                <td class="px-6 py-4 text-sm text-gray-600" x-text="formatDate(eval.evaluation_date || eval.created_at)"></td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full"
                                          :class="getCapBadgeClass(eval.cap_stage)"
                                          x-text="'CAPSTONE ' + eval.cap_stage"></span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-lg font-bold" :class="getGradeClass(eval.grade)" x-text="eval.grade"></span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600" x-text="eval.remarks || '-'"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
                <div x-show="evaluations.length === 0" class="p-8 text-center text-gray-500">
                    No evaluations recorded yet
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function groupDetails() {
    return {
        group: null,
        evaluations: [],
        loading: true,

        init() {
            this.loadGroup();
        },

        async loadGroup() {
            const groupId = window.location.pathname.split('/').pop();
            try {
                const response = await fetch('/api/groups/' + groupId);
                this.group = await response.json();
                await this.loadEvaluations(groupId);
            } catch (error) {
                console.error('Error:', error);
            } finally {
                this.loading = false;
            }
        },

        async loadEvaluations(groupId) {
            try {
                const response = await fetch('/api/evaluations?group_id=' + groupId);
                const data = await response.json();
                // API returns array with group+evaluations objects, extract evaluations from first item
                if (Array.isArray(data) && data.length > 0) {
                    this.evaluations = data[0].evaluations || [];
                } else {
                    this.evaluations = [];
                }
            } catch (error) {
                console.error('Error loading evaluations:', error);
            }
        },

        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
        },

        getCapBadgeClass(stage) {
            const classes = { 1: 'bg-blue-100 text-blue-700', 2: 'bg-purple-100 text-purple-700', 3: 'bg-green-100 text-green-700' };
            return classes[stage] || 'bg-gray-100 text-gray-700';
        },

        getPanelGradient(role) {
            const gradients = {
                'Adviser': 'bg-gradient-to-br from-purple-400 to-purple-600',
                'Chair': 'bg-gradient-to-br from-emerald-400 to-emerald-600',
                'Critique': 'bg-gradient-to-br from-orange-400 to-orange-600'
            };
            return gradients[role] || 'bg-gradient-to-br from-gray-400 to-gray-600';
        },

        getPanelBadgeClass(role) {
            const classes = { 'Adviser': 'bg-purple-100 text-purple-700', 'Chair': 'bg-emerald-100 text-emerald-700', 'Critique': 'bg-orange-100 text-orange-700' };
            return classes[role] || 'bg-gray-100 text-gray-700';
        },

        getGradeClass(grade) {
            if (grade >= 90) return 'text-green-600';
            if (grade >= 80) return 'text-blue-600';
            if (grade >= 75) return 'text-yellow-600';
            return 'text-red-600';
        },

        getCap1Average() {
            const cap1Evals = this.evaluations.filter(e => e.cap_stage == 1);
            if (cap1Evals.length === 0) return 'N/A';
            const sum = cap1Evals.reduce((acc, e) => acc + parseFloat(e.grade || 0), 0);
            return (sum / cap1Evals.length).toFixed(1);
        },

        getCap2Average() {
            const cap2Evals = this.evaluations.filter(e => e.cap_stage == 2);
            if (cap2Evals.length === 0) return 'N/A';
            const sum = cap2Evals.reduce((acc, e) => acc + parseFloat(e.grade || 0), 0);
            return (sum / cap2Evals.length).toFixed(1);
        }
    }
}
</script>
@endsection
