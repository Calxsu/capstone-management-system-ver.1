@extends('layouts.dashboard')

@section('title', 'Reports')
@section('subtitle', 'Generate and view system reports')

@section('content')
<div x-data="reportsData()">
    <!-- Report Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- ETL Report Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover-lift animate-slide-up">
            <div class="p-6">
                <div class="flex items-start justify-between">
                    <div class="p-4 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg shadow-blue-500/30">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">Analytics</span>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mt-6 mb-2">ETL Report</h3>
                <p class="text-gray-500 text-sm mb-6">Comprehensive Extract-Transform-Load report showing data processing metrics and group progress analysis.</p>
                
                <div class="flex items-center space-x-3">
                    <a href="{{ route('reports.etl') }}" class="flex-1 inline-flex items-center justify-center px-4 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium rounded-xl transition-all duration-300">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        View Report
                    </a>
                    <button @click="exportReport('etl')" class="px-4 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- CAPSTONE Progress Report Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover-lift animate-slide-up" style="animation-delay: 0.1s">
            <div class="p-6">
                <div class="flex items-start justify-between">
                    <div class="p-4 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl shadow-lg shadow-emerald-500/30">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-full">Progress</span>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mt-6 mb-2">CAPSTONE Progress Report</h3>
                <p class="text-gray-500 text-sm mb-6">Track group progression through CAPSTONE stages with detailed status breakdown and completion rates.</p>
                
                <div class="flex items-center space-x-3">
                    <a href="{{ route('reports.cap-progress') }}" class="flex-1 inline-flex items-center justify-center px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-medium rounded-xl transition-all duration-300">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        View Report
                    </a>
                    <button @click="exportReport('capstone-progress')" class="px-4 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8 animate-slide-up" style="animation-delay: 0.2s">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Quick Overview</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="w-16 h-16 mx-auto bg-blue-100 rounded-2xl flex items-center justify-center mb-3">
                    <span class="text-2xl font-bold text-blue-600" x-text="stats.totalGroups">0</span>
                </div>
                <p class="text-sm text-gray-600">Total Groups</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 mx-auto bg-emerald-100 rounded-2xl flex items-center justify-center mb-3">
                    <span class="text-2xl font-bold text-emerald-600" x-text="stats.completedGroups">0</span>
                </div>
                <p class="text-sm text-gray-600">Completed</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 mx-auto bg-purple-100 rounded-2xl flex items-center justify-center mb-3">
                    <span class="text-2xl font-bold text-purple-600" x-text="stats.totalEvaluations">0</span>
                </div>
                <p class="text-sm text-gray-600">Evaluations</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 mx-auto bg-orange-100 rounded-2xl flex items-center justify-center mb-3">
                    <span class="text-2xl font-bold text-orange-600" x-text="stats.completionRate + '%'">0%</span>
                </div>
                <p class="text-sm text-gray-600">Completion Rate</p>
            </div>
        </div>
    </div>

    <!-- Recent Reports -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up" style="animation-delay: 0.3s">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900">Generated Reports History</h3>
        </div>
        <div class="divide-y divide-gray-100">
            <template x-for="report in recentReports" :key="report.id">
                <div class="p-4 hover:bg-gray-50 transition-colors flex items-center justify-between">
                    <div class="flex items-center">
                        <div :class="report.type === 'ETL' ? 'bg-blue-100' : 'bg-emerald-100'" class="p-3 rounded-xl mr-4">
                            <svg :class="report.type === 'ETL' ? 'text-blue-600' : 'text-emerald-600'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900" x-text="report.name"></p>
                            <p class="text-sm text-gray-500" x-text="report.date"></p>
                        </div>
                    </div>
                    <button class="text-blue-600 hover:text-blue-700 font-medium text-sm">Download</button>
                </div>
            </template>
            <div x-show="recentReports.length === 0" class="p-8 text-center text-gray-500">
                No reports generated yet
            </div>
        </div>
    </div>
</div>

<script>
function reportsData() {
    return {
        stats: {
            totalGroups: 0,
            completedGroups: 0,
            totalEvaluations: 0,
            completionRate: 0
        },
        recentReports: [],

        init() {
            this.loadStats();
        },

        async loadStats() {
            try {
                const [groups, evaluations] = await Promise.all([
                    fetch('/api/groups').then(r => r.json()),
                    fetch('/api/evaluations').then(r => r.json())
                ]);

                this.stats.totalGroups = groups.length;
                this.stats.completedGroups = groups.filter(g => g.cap_status === 'CAP2').length;
                this.stats.totalEvaluations = evaluations.length;
                this.stats.completionRate = this.stats.totalGroups > 0 
                    ? Math.round((this.stats.completedGroups / this.stats.totalGroups) * 100) 
                    : 0;
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        },

        exportReport(type) {
            window.open('/api/reports/' + type + '/export', '_blank');
        }
    }
}
</script>
@endsection
