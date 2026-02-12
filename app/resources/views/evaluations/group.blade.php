@extends('layouts.dashboard')

@section('title', 'Group Evaluations')
@section('subtitle', 'View evaluations for Group #' . ($group->id ?? ''))

@section('content')
<div x-data="groupEvaluationsData()">
    <!-- Back Button -->
    <a href="{{ route('groups.show', $group->id ?? 1) }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 mb-6 animate-fade-in">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to Group
    </a>

    <!-- Group Header -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6 animate-slide-up">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl flex items-center justify-center text-white text-2xl font-bold mr-4">
                    {{ substr('G', 0, 1) }}
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Group #{{ $group->id ?? '' }}</h2>
                    <p class="text-gray-500">{{ $group->project_title ?? 'No project title' }}</p>
                </div>
            </div>
            <a href="{{ route('evaluations.create-for-group', $group->id ?? 1) }}" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white font-medium rounded-xl shadow-lg shadow-purple-500/30 hover:shadow-xl transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Evaluation
            </a>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up" style="animation-delay: 0.1s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Evaluations</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1" x-text="evaluations.length">0</p>
                </div>
                <div class="p-4 bg-blue-100 rounded-2xl">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up" style="animation-delay: 0.15s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Average Grade</p>
                    <p class="text-3xl font-bold text-emerald-600 mt-1" x-text="averageGrade.toFixed(1)">0.0</p>
                </div>
                <div class="p-4 bg-emerald-100 rounded-2xl">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up" style="animation-delay: 0.2s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Current CAPSTONE Stage</p>
                    <p class="text-3xl font-bold text-purple-600 mt-1">CAPSTONE {{ $group->cap_stage ?? 1 }}</p>
                </div>
                <div class="p-4 bg-purple-100 rounded-2xl">
                    <span class="text-2xl font-bold text-purple-600">{{ $group->cap_stage ?? 1 }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up" style="animation-delay: 0.25s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Panel Members</p>
                    <p class="text-3xl font-bold text-orange-600 mt-1" x-text="panelCount">0</p>
                </div>
                <div class="p-4 bg-orange-100 rounded-2xl">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Evaluations List -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up" style="animation-delay: 0.3s">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900">Evaluation History</h3>
        </div>
        
        <div class="divide-y divide-gray-100">
            <template x-for="evaluation in evaluations" :key="evaluation.id">
                <div class="p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold mr-4" :class="{
                                'bg-yellow-500': evaluation.cap_stage == 1,
                                'bg-emerald-500': evaluation.cap_stage == 2
                            }" x-text="'C' + evaluation.cap_stage"></div>
                            <div>
                                <p class="font-semibold text-gray-900" x-text="evaluation.panel_member?.email || 'Panel Member'"></p>
                                <p class="text-sm text-gray-500" x-text="formatDate(evaluation.evaluation_date)"></p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="text-right">
                                <p class="text-2xl font-bold" :class="getGradeColor(evaluation.grade)" x-text="evaluation.grade || 'N/A'"></p>
                                <p class="text-xs text-gray-500">Grade</p>
                            </div>
                            <a :href="'/evaluations/' + evaluation.id" class="p-2 text-gray-400 hover:text-blue-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                    <div x-show="evaluation.comments" class="mt-4 p-4 bg-gray-50 rounded-xl">
                        <p class="text-sm text-gray-600" x-text="evaluation.comments"></p>
                    </div>
                </div>
            </template>
            
            <div x-show="evaluations.length === 0" class="p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
                <p class="text-gray-500 mb-4">No evaluations yet for this group</p>
                <a href="{{ route('evaluations.create-for-group', $group->id ?? 1) }}" class="inline-flex items-center px-4 py-2 bg-purple-500 text-white rounded-xl hover:bg-purple-600 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add First Evaluation
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function groupEvaluationsData() {
    return {
        evaluations: [],
        averageGrade: 0,
        panelCount: 0,
        groupId: {{ $group->id ?? 'null' }},

        async init() {
            if (this.groupId) {
                await this.loadEvaluations();
            }
        },

        async loadEvaluations() {
            try {
                const response = await fetch('/api/groups/' + this.groupId + '/evaluations/summary');
                const data = await response.json();
                this.evaluations = data.evaluations || [];
                this.averageGrade = data.average_grade || 0;
                this.panelCount = data.panel_count || 0;
            } catch (error) {
                console.error('Error loading evaluations:', error);
                // Fallback: try loading all evaluations
                try {
                    const allResponse = await fetch('/api/evaluations');
                    const allEvaluations = await allResponse.json();
                    this.evaluations = allEvaluations.filter(e => e.group_id == this.groupId);
                    if (this.evaluations.length > 0) {
                        const total = this.evaluations.reduce((sum, e) => sum + (e.grade || 0), 0);
                        this.averageGrade = total / this.evaluations.length;
                        const uniquePanels = new Set(this.evaluations.map(e => e.panel_member_id));
                        this.panelCount = uniquePanels.size;
                    }
                } catch (fallbackError) {
                    console.error('Fallback error:', fallbackError);
                }
            }
        },

        getGradeColor(grade) {
            if (grade >= 90) return 'text-emerald-600';
            if (grade >= 80) return 'text-blue-600';
            if (grade >= 70) return 'text-yellow-600';
            return 'text-red-600';
        },

        formatDate(dateString) {
            if (!dateString) return 'N/A';
            return new Date(dateString).toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric' 
            });
        }
    }
}
</script>
@endsection
