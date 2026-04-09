@extends('layouts.dashboard')

@section('title', 'Dashboard')
@section('subtitle', 'Overview of your Capstone Management System')

@section('content')
<div class="space-y-6" x-data="dashboardData()">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- School Years Card -->
        <div class="group bg-white rounded-2xl shadow-sm hover-lift p-6 border border-gray-100 relative overflow-hidden animate-slide-up" style="animation-delay: 0.1s">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-500/10 to-blue-600/5 rounded-full -mr-16 -mt-16 transition-transform group-hover:scale-150 duration-500"></div>
            <div class="flex items-center justify-between relative">
                <div>
                    <p class="text-sm font-medium text-gray-500">School Years</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1" x-text="stats.schoolYears">0</p>
                    <p class="text-xs text-gray-400 mt-2 flex items-center">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-1.5 animate-pulse"></span>
                        Active periods
                    </p>
                </div>
                <div class="p-4 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Panel Members Card -->
        <div class="group bg-white rounded-2xl shadow-sm hover-lift p-6 border border-gray-100 relative overflow-hidden animate-slide-up" style="animation-delay: 0.2s">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-emerald-500/10 to-emerald-600/5 rounded-full -mr-16 -mt-16 transition-transform group-hover:scale-150 duration-500"></div>
            <div class="flex items-center justify-between relative">
                <div>
                    <p class="text-sm font-medium text-gray-500">Panel Members</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1" x-text="stats.panelMembers">0</p>
                    <p class="text-xs text-gray-400 mt-2 flex items-center">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full mr-1.5"></span>
                        Registered evaluators
                    </p>
                </div>
                <div class="p-4 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl shadow-lg shadow-emerald-500/30 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Students Card -->
        <div class="group bg-white rounded-2xl shadow-sm hover-lift p-6 border border-gray-100 relative overflow-hidden animate-slide-up" style="animation-delay: 0.3s">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-purple-500/10 to-purple-600/5 rounded-full -mr-16 -mt-16 transition-transform group-hover:scale-150 duration-500"></div>
            <div class="flex items-center justify-between relative">
                <div>
                    <p class="text-sm font-medium text-gray-500">Students</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1" x-text="stats.students">0</p>
                    <p class="text-xs text-gray-400 mt-2 flex items-center">
                        <span class="w-2 h-2 bg-purple-500 rounded-full mr-1.5"></span>
                        Enrolled trainees
                    </p>
                </div>
                <div class="p-4 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-lg shadow-purple-500/30 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Groups Card -->
        <div class="group bg-white rounded-2xl shadow-sm hover-lift p-6 border border-gray-100 relative overflow-hidden animate-slide-up" style="animation-delay: 0.4s">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-orange-500/10 to-orange-600/5 rounded-full -mr-16 -mt-16 transition-transform group-hover:scale-150 duration-500"></div>
            <div class="flex items-center justify-between relative">
                <div>
                    <p class="text-sm font-medium text-gray-500">Groups</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1" x-text="stats.groups">0</p>
                    <p class="text-xs text-gray-400 mt-2 flex items-center">
                        <span class="w-2 h-2 bg-orange-500 rounded-full mr-1.5"></span>
                        Active teams
                    </p>
                </div>
                <div class="p-4 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl shadow-lg shadow-orange-500/30 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts & Quick Actions Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- CAPSTONE Progress Chart -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm p-6 border border-gray-100 animate-slide-up" style="animation-delay: 0.5s">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">CAPSTONE Progress Overview</h3>
                    <p class="text-sm text-gray-500 mt-1">Group distribution by CAPSTONE status</p>
                </div>
            </div>
            
            <div class="relative h-64">
                <canvas id="capChart"></canvas>
            </div>
            
            <!-- CAPSTONE Legend -->
            <div class="flex items-center justify-center space-x-8 mt-6 pt-6 border-t border-gray-100">
                <div class="flex items-center space-x-2">
                    <div class="w-4 h-4 rounded-full bg-gradient-to-r from-blue-500 to-blue-600"></div>
                    <span class="text-sm text-gray-600">CAPSTONE 1</span>
                    <span class="text-sm font-bold text-gray-900" x-text="capStatus.cap1">0</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-4 h-4 rounded-full bg-gradient-to-r from-emerald-500 to-emerald-600"></div>
                    <span class="text-sm text-gray-600">CAPSTONE 2</span>
                    <span class="text-sm font-bold text-gray-900" x-text="capStatus.cap2">0</span>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 animate-slide-up" style="animation-delay: 0.6s">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <a href="{{ route('import.index') }}" class="group flex items-center justify-between p-4 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-xl transition-all duration-300 shadow-lg shadow-blue-500/30 hover:shadow-xl hover:shadow-blue-500/40 hover:-translate-y-0.5">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-white/20 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                        </div>
                        <span class="font-medium">Import Data</span>
                    </div>
                    <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>

                <a href="{{ route('groups.create') }}" class="group flex items-center justify-between p-4 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white rounded-xl transition-all duration-300 shadow-lg shadow-emerald-500/30 hover:shadow-xl hover:shadow-emerald-500/40 hover:-translate-y-0.5">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-white/20 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <span class="font-medium">Create Group</span>
                    </div>
                    <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>

                <a href="{{ route('etl.index') }}" class="group flex items-center justify-between p-4 bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white rounded-xl transition-all duration-300 shadow-lg shadow-purple-500/30 hover:shadow-xl hover:shadow-purple-500/40 hover:-translate-y-0.5">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-white/20 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <span class="font-medium">ETL Report</span>
                    </div>
                    <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>

                <a href="{{ route('reports.cap-progress') }}" class="group flex items-center justify-between p-4 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-xl transition-all duration-300 shadow-lg shadow-orange-500/30 hover:shadow-xl hover:shadow-orange-500/40 hover:-translate-y-0.5">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-white/20 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                        <span class="font-medium">CAP Progress</span>
                    </div>
                    <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Activity & System Status -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Activity -->
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 animate-slide-up" style="animation-delay: 0.7s">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
                <a href="{{ route('dashboard.recent-activity') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium transition-colors">View All</a>
            </div>
            <div class="space-y-4">
                <template x-for="(activity, index) in recentActivities" :key="index">
                    <div class="flex items-start space-x-4 p-3 rounded-xl hover:bg-gray-50 transition-colors group">
                        <div :class="activity.color" class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-medium text-gray-900" x-text="activity.title"></p>
                                <span class="text-[10px] uppercase tracking-wide px-2 py-0.5 rounded-full" :class="activity.badgeClass" x-text="activity.typeLabel"></span>
                            </div>
                            <p class="text-xs text-gray-500 mt-0.5" x-text="activity.description"></p>
                        </div>
                        <span class="text-xs text-gray-400 flex-shrink-0" x-text="activity.time"></span>
                    </div>
                </template>

                <template x-if="recentActivities.length === 0">
                    <div class="rounded-xl border border-dashed border-gray-200 p-5 text-center text-sm text-gray-500">
                        No recent activity yet.
                    </div>
                </template>
            </div>
        </div>

        <!-- System Status -->
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 animate-slide-up" style="animation-delay: 0.8s">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">System Status</h3>
                <span class="flex items-center text-sm text-green-600">
                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                    All systems operational
                </span>
            </div>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Database</p>
                            <p class="text-xs text-gray-500">MySQL Connected</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">Online</span>
                </div>

                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Web Server</p>
                            <p class="text-xs text-gray-500">Laravel {{ app()->version() }}</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">Running</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 animate-slide-up" style="animation-delay: 0.9s">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Performance Metrics</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="text-center p-4 bg-gradient-to-br from-blue-50 to-blue-100/50 rounded-xl">
                <div class="text-3xl font-bold text-blue-600" x-text="stats.groups">0</div>
                <div class="text-sm text-gray-600 mt-1">Total Groups</div>
            </div>
            
            <div class="text-center p-4 bg-gradient-to-br from-emerald-50 to-emerald-100/50 rounded-xl">
                <div class="text-3xl font-bold text-emerald-600" x-text="capStatus.cap2">0</div>
                <div class="text-sm text-gray-600 mt-1">Completed (CAPSTONE 2)</div>
            </div>
            
            <div class="text-center p-4 bg-gradient-to-br from-purple-50 to-purple-100/50 rounded-xl">
                <div class="text-3xl font-bold text-purple-600" x-text="stats.evaluations">0</div>
                <div class="text-sm text-gray-600 mt-1">Evaluations</div>
            </div>
            
            <div class="text-center p-4 bg-gradient-to-br from-orange-50 to-orange-100/50 rounded-xl">
                <div class="text-3xl font-bold text-orange-600">
                    <span x-text="completionRate">0</span>%
                </div>
                <div class="text-sm text-gray-600 mt-1">Completion Rate</div>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-3">
                    <div class="bg-gradient-to-r from-orange-500 to-orange-600 h-2 rounded-full transition-all duration-1000" :style="'width: ' + completionRate + '%'"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function dashboardData() {
    return {
        stats: {
            schoolYears: 0,
            panelMembers: 0,
            students: 0,
            groups: 0,
            evaluations: 0
        },
        capStatus: {
            cap1: 0,
            cap2: 0
        },
        averageGrades: {
            cap1: 0,
            cap2: 0
        },
        completionRate: 0,
        activeGroups: 0,
        chart: null,
        recentActivities: [],
        
        async init() {
            await Promise.all([
                this.loadDashboardStats(),
                this.loadRecentActivity()
            ]);

            this.$nextTick(() => this.initChart());
        },
        
        async loadDashboardStats() {
            try {
                // Try the new dashboard stats endpoint first
                const statsResponse = await fetch('/api/dashboard/stats');
                if (statsResponse.ok) {
                    const data = await statsResponse.json();
                    this.stats.schoolYears = Number(data.schoolYears ?? 0);
                    this.stats.students = Number(data.students ?? 0);
                    this.stats.panelMembers = Number(data.panelMembers ?? 0);
                    this.stats.groups = Number(data.groups ?? 0);
                    this.stats.evaluations = Number(data.evaluations ?? 0);

                    // Keep object shape stable for Alpine reactivity and chart reads.
                    this.capStatus.cap1 = Number(data.capProgress?.cap1 ?? 0);
                    this.capStatus.cap2 = Number(data.capProgress?.cap2 ?? 0);
                    this.averageGrades.cap1 = Number(data.averageGrades?.cap1 ?? 0);
                    this.averageGrades.cap2 = Number(data.averageGrades?.cap2 ?? 0);

                    this.completionRate = Number(data.completionRate ?? 0);
                    this.activeGroups = Number(data.activeGroups ?? 0);
                    this.updateChart();
                    return;
                }
                
                // Fallback to individual API calls
                const [schoolYears, panelMembers, students, groups, evaluations] = await Promise.all([
                    fetch('/api/school-years').then(r => r.ok ? r.json() : []),
                    fetch('/api/panel-members').then(r => r.ok ? r.json() : []),
                    fetch('/api/students').then(r => r.ok ? r.json() : []),
                    fetch('/api/groups').then(r => r.ok ? r.json() : []),
                    fetch('/api/evaluations').then(r => r.ok ? r.json() : [])
                ]);

                this.stats.schoolYears = schoolYears.length;
                this.stats.panelMembers = panelMembers.length;
                this.stats.students = students.length;
                this.stats.groups = groups.length;
                this.stats.evaluations = evaluations.length;

                this.capStatus.cap1 = groups.filter(g => Number(g.cap_stage) === 1).length;
                this.capStatus.cap2 = groups.filter(g => Number(g.cap_stage) === 2).length;

                if (groups.length > 0) {
                    this.completionRate = Math.round((this.capStatus.cap2 / groups.length) * 100);
                }

                this.updateChart();
            } catch (error) {
                console.error('Error loading dashboard stats:', error);
            }
        },

        async loadRecentActivity() {
            try {
                const response = await fetch('/api/dashboard/recent-activity');
                if (response.ok) {
                    const activities = await response.json();
                    this.recentActivities = activities.map(a => ({
                        title: (a.message || 'Activity update').replace(/:\s*$/, ''),
                        description: a.detail && a.detail.trim() ? a.detail : 'No additional detail available',
                        time: this.timeAgo(a.timestamp),
                        typeLabel: a.type || 'activity',
                        badgeClass: a.type === 'evaluation'
                            ? 'bg-amber-100 text-amber-700'
                            : a.type === 'group'
                                ? 'bg-blue-100 text-blue-700'
                                : 'bg-indigo-100 text-indigo-700',
                        color: a.type === 'evaluation'
                            ? 'bg-gradient-to-br from-amber-500 to-orange-500'
                            : a.type === 'group'
                                ? 'bg-gradient-to-br from-blue-500 to-blue-600'
                                : 'bg-gradient-to-br from-indigo-500 to-indigo-600'
                    }));
                }
            } catch (error) {
                console.error('Error loading recent activity:', error);
            }
        },

        timeAgo(timestamp) {
            const date = new Date(timestamp);
            const now = new Date();
            const diff = Math.floor((now - date) / 1000);
            
            if (diff < 60) return 'Just now';
            if (diff < 3600) return Math.floor(diff / 60) + ' min ago';
            if (diff < 86400) return Math.floor(diff / 3600) + ' hours ago';
            return Math.floor(diff / 86400) + ' days ago';
        },
        
        initChart() {
            const ctx = document.getElementById('capChart');
            if (!ctx) return;
            
            this.chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['CAPSTONE 1', 'CAPSTONE 2'],
                    datasets: [{
                        label: 'Groups',
                        data: [this.capStatus.cap1, this.capStatus.cap2],
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.8)', 
                            'rgba(16, 185, 129, 0.8)'
                        ],
                        borderColor: [
                            'rgb(59, 130, 246)', 
                            'rgb(16, 185, 129)'
                        ],
                        borderWidth: 2,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                afterLabel: (context) => {
                                    const idx = context.dataIndex;
                                    const grades = [this.averageGrades.cap1, this.averageGrades.cap2];
                                    return grades[idx] ? `Avg Grade: ${grades[idx]}` : '';
                                }
                            }
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(0, 0, 0, 0.05)' } },
                        x: { grid: { display: false } }
                    }
                }
            });

            this.updateChart();
        },
        
        updateChart() {
            if (!this.chart) return;
            this.chart.data.datasets[0].data = [
                Number(this.capStatus.cap1 || 0),
                Number(this.capStatus.cap2 || 0)
            ];
            this.chart.update('active');
        }
    }
}
</script>
@endsection