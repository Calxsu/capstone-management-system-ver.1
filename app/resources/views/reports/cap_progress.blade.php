@extends('layouts.dashboard')

@section('title', 'CAPSTONE Progress Report')
@section('subtitle', 'Track group progress through Capstone stages')

@section('content')
<div x-data="capProgressData()">
    <!-- Filters Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6 animate-slide-up">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Report Filters</h3>
            <button @click="resetFilters()" class="text-sm text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Reset Filters
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- School Year Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">School Year</label>
                <select x-model="filters.schoolYear" @change="applyFilters()" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white">
                    <option value="">All School Years</option>
                    <template x-for="sy in schoolYears" :key="sy.id">
                        <option :value="sy.id" x-text="sy.year"></option>
                    </template>
                </select>
            </div>

            <!-- CAPSTONE Stage Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">CAPSTONE Stage</label>
                <select x-model="filters.capStage" @change="applyFilters()" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white">
                    <option value="">All Stages</option>
                    <option value="1">CAPSTONE 1</option>
                    <option value="2">CAPSTONE 2 (Completed)</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select x-model="filters.status" @change="applyFilters()" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="active">Active</option>
                    <option value="completed">Completed</option>
                </select>
            </div>

            <!-- Adviser Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Adviser</label>
                <select x-model="filters.adviser" @change="applyFilters()" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white">
                    <option value="">All Advisers</option>
                    <template x-for="adv in advisers" :key="adv.id">
                        <option :value="adv.id" x-text="adv.name"></option>
                    </template>
                </select>
            </div>

            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search Group</label>
                <input type="text" x-model="filters.search" @input.debounce.300ms="applyFilters()" placeholder="Group ID or title..." class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white">
            </div>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up" style="animation-delay: 0.1s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Groups</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1" x-text="summary.totalGroups">0</p>
                </div>
                <div class="p-4 bg-blue-100 rounded-2xl">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up" style="animation-delay: 0.15s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">CAPSTONE 1</p>
                    <p class="text-3xl font-bold text-yellow-600 mt-1" x-text="summary.cap1">0</p>
                </div>
                <div class="p-4 bg-yellow-100 rounded-2xl">
                    <span class="text-2xl font-bold text-yellow-600">1</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up" style="animation-delay: 0.2s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">CAPSTONE 2 (Completed)</p>
                    <p class="text-3xl font-bold text-emerald-600 mt-1" x-text="summary.cap2">0</p>
                </div>
                <div class="p-4 bg-emerald-100 rounded-2xl">
                    <span class="text-2xl font-bold text-emerald-600">2</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up" style="animation-delay: 0.25s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Completion Rate</p>
                    <p class="text-3xl font-bold text-purple-600 mt-1" x-text="summary.completionRate + '%'">0%</p>
                </div>
                <div class="p-4 bg-purple-100 rounded-2xl">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Chart -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6 animate-slide-up" style="animation-delay: 0.35s">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">CAPSTONE Stage Distribution</h3>
        <div class="h-8 bg-gray-100 rounded-full overflow-hidden flex">
            <div class="bg-yellow-500 h-full transition-all duration-500" :style="'width: ' + (summary.cap1 / summary.totalGroups * 100 || 0) + '%'"></div>
            <div class="bg-emerald-500 h-full transition-all duration-500" :style="'width: ' + (summary.cap2 / summary.totalGroups * 100 || 0) + '%'"></div>
        </div>
        <div class="flex justify-center space-x-8 mt-4 text-sm">
            <div class="flex items-center"><div class="w-4 h-4 bg-yellow-500 rounded mr-2"></div> CAPSTONE 1</div>
            <div class="flex items-center"><div class="w-4 h-4 bg-emerald-500 rounded mr-2"></div> CAPSTONE 2 (Completed)</div>
        </div>
    </div>

    <!-- Export Actions -->
    <div class="flex items-center justify-between mb-6 animate-slide-up" style="animation-delay: 0.4s">
        <p class="text-sm text-gray-500">
            Showing <span class="font-semibold" x-text="filteredData.length">0</span> of <span class="font-semibold" x-text="allData.length">0</span> groups
        </p>
        <div class="flex items-center space-x-3">
            <button @click="exportToPDF()" class="inline-flex items-center px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white font-medium rounded-xl transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                Export PDF
            </button>
            <button @click="exportToCSV()" class="inline-flex items-center px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-medium rounded-xl transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Export CSV
            </button>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up" style="animation-delay: 0.45s">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" @click="sortBy('id')">
                            <div class="flex items-center">
                                Group ID
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Project Title</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Adviser</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" @click="sortBy('cap_stage')">CAPSTONE Stage</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" @click="sortBy('students_count')">Students</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Progress</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-for="group in filteredData" :key="group.id">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-semibold mr-3" :class="{
                                        'bg-yellow-500': group.cap_stage == 1,
                                        'bg-emerald-500': group.cap_stage == 2
                                    }" x-text="'C' + group.cap_stage"></div>
                                    <span class="font-medium text-gray-900" x-text="'Group #' + group.id"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-500" x-text="group.project_title || 'No title yet'"></td>
                            <td class="px-6 py-4 text-gray-500" x-text="group.adviser_name || 'Unassigned'"></td>
                            <td class="px-6 py-4">
                                <span :class="{
                                    'bg-yellow-100 text-yellow-700': group.cap_stage == 1,
                                    'bg-emerald-100 text-emerald-700': group.cap_stage == 2
                                }" class="px-3 py-1 text-xs font-semibold rounded-full" x-text="group.cap_stage == 2 ? 'CAPSTONE 2 (Completed)' : 'CAPSTONE ' + group.cap_stage"></span>
                            </td>
                            <td class="px-6 py-4">
                                <span :class="{
                                    'bg-gray-100 text-gray-700': group.status === 'pending',
                                    'bg-blue-100 text-blue-700': group.status === 'active',
                                    'bg-green-100 text-green-700': group.status === 'completed'
                                }" class="px-3 py-1 text-xs font-semibold rounded-full capitalize" x-text="group.status"></span>
                            </td>
                            <td class="px-6 py-4 text-gray-900 font-medium" x-text="group.students_count || 0"></td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-full max-w-24 h-2 bg-gray-200 rounded-full mr-3">
                                        <div class="h-full rounded-full transition-all duration-300" :class="{
                                            'bg-yellow-500': group.cap_stage == 1,
                                            'bg-emerald-500': group.cap_stage == 2
                                        }" :style="'width: ' + (group.cap_stage == 2 ? 100 : 50) + '%'"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-600" x-text="(group.cap_stage == 2 ? 100 : 50) + '%'"></span>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredData.length === 0">
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p>No groups match your filters</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function capProgressData() {
    return {
        allData: [],
        filteredData: [],
        schoolYears: [],
        advisers: [],
        filters: {
            schoolYear: '',
            capStage: '',
            status: '',
            adviser: '',
            search: ''
        },
        sortField: 'cap_stage',
        sortDirection: 'desc',
        summary: {
            totalGroups: 0,
            cap1: 0,
            cap2: 0,
            completionRate: 0
        },

        async init() {
            await Promise.all([this.loadSchoolYears(), this.loadAdvisers(), this.loadData()]);
        },

        async loadSchoolYears() {
            try {
                const response = await fetch('/api/school-years');
                this.schoolYears = await response.json();
            } catch (error) {
                console.error('Error loading school years:', error);
            }
        },

        async loadAdvisers() {
            try {
                const response = await fetch('/api/panel-members');
                const members = await response.json();
                this.advisers = members.filter(m => m.role === 'Adviser');
            } catch (error) {
                console.error('Error loading advisers:', error);
            }
        },

        async loadData() {
            try {
                const response = await fetch('/api/groups');
                this.allData = await response.json();
                this.applyFilters();
            } catch (error) {
                console.error('Error loading data:', error);
            }
        },

        applyFilters() {
            let data = [...this.allData];

            if (this.filters.schoolYear) data = data.filter(g => g.school_year_id == this.filters.schoolYear);
            if (this.filters.capStage) data = data.filter(g => g.cap_stage == this.filters.capStage);
            if (this.filters.status) data = data.filter(g => g.status === this.filters.status);
            if (this.filters.adviser) data = data.filter(g => g.adviser_id == this.filters.adviser);
            if (this.filters.search) {
                const search = this.filters.search.toLowerCase();
                data = data.filter(g => (g.name && g.name.toLowerCase().includes(search)) || (g.project_title && g.project_title.toLowerCase().includes(search)));
            }

            data.sort((a, b) => {
                let aVal = a[this.sortField], bVal = b[this.sortField];
                if (typeof aVal === 'string') { aVal = aVal.toLowerCase(); bVal = bVal.toLowerCase(); }
                return this.sortDirection === 'asc' ? (aVal > bVal ? 1 : -1) : (aVal < bVal ? 1 : -1);
            });

            this.filteredData = data;
            this.updateSummary();
        },

        updateSummary() {
            this.summary.totalGroups = this.filteredData.length;
            this.summary.cap1 = this.filteredData.filter(g => g.cap_stage == 1).length;
            this.summary.cap2 = this.filteredData.filter(g => g.cap_stage == 2).length;
            this.summary.completionRate = this.summary.totalGroups > 0 ? Math.round(this.summary.cap2 / this.summary.totalGroups * 100) : 0;
        },

        sortBy(field) {
            if (this.sortField === field) this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            else { this.sortField = field; this.sortDirection = 'desc'; }
            this.applyFilters();
        },

        resetFilters() {
            this.filters = { schoolYear: '', capStage: '', status: '', adviser: '', search: '' };
            this.applyFilters();
        },

        exportToPDF() {
            window.open('/api/reports/cap-progress/export?format=pdf', '_blank');
        },

        exportToCSV() {
            const headers = ['Group ID', 'Project Title', 'Adviser', 'CAPSTONE Stage', 'Status', 'Students', 'Progress %'];
            const rows = this.filteredData.map(g => ['#' + g.id, g.project_title || '', g.adviser_name || '', g.cap_stage == 2 ? 'CAPSTONE 2 (Completed)' : 'CAPSTONE ' + g.cap_stage, g.status, g.students_count || 0, (g.cap_stage == 2 ? 100 : 50) + '%']);
            let csv = headers.join(',') + '\n';
            rows.forEach(row => { csv += row.map(cell => '"' + String(cell).replace(/"/g, '""') + '"').join(',') + '\n'; });
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'capstone_progress_' + new Date().toISOString().split('T')[0] + '.csv';
            a.click();
        }
    }
}
</script>
@endsection
