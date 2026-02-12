@extends('layouts.dashboard')

@section('title', 'ETL Report')
@section('subtitle', 'Extract-Transform-Load analysis for panel members')

@section('content')
<div x-data="etlReportData()">
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
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
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

            <!-- Role Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                <select x-model="filters.role" @change="applyFilters()" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white">
                    <option value="">All Roles</option>
                    <option value="Adviser">Adviser</option>
                    <option value="Chair">Chair</option>
                    <option value="Critique">Critique</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select x-model="filters.status" @change="applyFilters()" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>

        <!-- ETL Range Filter -->
        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Min ETL</label>
                <input type="number" x-model="filters.minEtl" @input.debounce.300ms="applyFilters()" step="0.1" min="0" placeholder="0.0" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Max ETL</label>
                <input type="number" x-model="filters.maxEtl" @input.debounce.300ms="applyFilters()" step="0.1" min="0" placeholder="10.0" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search Name</label>
                <input type="text" x-model="filters.search" @input.debounce.300ms="applyFilters()" placeholder="Search..." class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white">
            </div>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up" style="animation-delay: 0.1s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Panel Members</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1" x-text="summary.totalMembers">0</p>
                </div>
                <div class="p-4 bg-blue-100 rounded-2xl">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up" style="animation-delay: 0.15s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total ETL Points</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1" x-text="summary.totalEtl.toFixed(2)">0.00</p>
                </div>
                <div class="p-4 bg-emerald-100 rounded-2xl">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up" style="animation-delay: 0.2s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Average ETL</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1" x-text="summary.averageEtl.toFixed(2)">0.00</p>
                </div>
                <div class="p-4 bg-purple-100 rounded-2xl">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up" style="animation-delay: 0.25s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Groups</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1" x-text="summary.totalGroups">0</p>
                </div>
                <div class="p-4 bg-orange-100 rounded-2xl">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Actions -->
    <div class="flex items-center justify-between mb-6 animate-slide-up" style="animation-delay: 0.3s">
        <p class="text-sm text-gray-500">
            Showing <span class="font-semibold" x-text="filteredData.length">0</span> of <span class="font-semibold" x-text="allData.length">0</span> panel members
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
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up" style="animation-delay: 0.35s">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" @click="sortBy('name')">
                            <div class="flex items-center">
                                Name
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" @click="sortBy('role')">Role</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" @click="sortBy('groups_count')">Groups</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" @click="sortBy('etl_total')">ETL Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-for="member in filteredData" :key="member.id">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold mr-3" x-text="member.name.charAt(0).toUpperCase()"></div>
                                    <span class="font-medium text-gray-900" x-text="member.name"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span :class="{
                                    'bg-purple-100 text-purple-700': member.role === 'Adviser',
                                    'bg-emerald-100 text-emerald-700': member.role === 'Chair',
                                    'bg-orange-100 text-orange-700': member.role === 'Critique'
                                }" class="px-3 py-1 text-xs font-semibold rounded-full" x-text="member.role"></span>
                            </td>

                            <td class="px-6 py-4">
                                <span :class="member.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'" class="px-3 py-1 text-xs font-semibold rounded-full capitalize" x-text="member.status"></span>
                            </td>
                            <td class="px-6 py-4 text-gray-900 font-medium" x-text="member.groups_count || 0"></td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-20 h-2 bg-gray-200 rounded-full mr-3">
                                        <div class="h-full rounded-full" :class="{
                                            'bg-green-500': member.etl_total >= 3,
                                            'bg-yellow-500': member.etl_total >= 1.5 && member.etl_total < 3,
                                            'bg-red-500': member.etl_total < 1.5
                                        }" :style="'width: ' + Math.min(member.etl_total * 20, 100) + '%'"></div>
                                    </div>
                                    <span class="font-bold" :class="{
                                        'text-green-600': member.etl_total >= 3,
                                        'text-yellow-600': member.etl_total >= 1.5 && member.etl_total < 3,
                                        'text-red-600': member.etl_total < 1.5
                                    }" x-text="member.etl_total.toFixed(2)"></span>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredData.length === 0">
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p>No data matches your filters</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ETL Legend -->
    <div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up" style="animation-delay: 0.4s">
        <h4 class="text-sm font-semibold text-gray-900 mb-4">ETL Calculation Reference</h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div class="flex items-center">
                <div class="w-4 h-4 bg-blue-500 rounded mr-3"></div>
                <span><strong>Adviser:</strong> 0.5 ETL per group</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-purple-500 rounded mr-3"></div>
                <span><strong>Chair:</strong> 0.3 ETL per group</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-orange-500 rounded mr-3"></div>
                <span><strong>Critique:</strong> 0.3 ETL per group</span>
            </div>
        </div>
    </div>
</div>

<script>
function etlReportData() {
    return {
        allData: [],
        filteredData: [],
        schoolYears: [],
        filters: {
            schoolYear: '',
            role: '',
            status: '',
            minEtl: '',
            maxEtl: '',
            search: ''
        },
        sortField: 'etl_total',
        sortDirection: 'desc',
        summary: {
            totalMembers: 0,
            totalEtl: 0,
            averageEtl: 0,
            totalGroups: 0
        },

        async init() {
            await this.loadSchoolYears();
            await this.loadData();
        },

        async loadSchoolYears() {
            try {
                const response = await fetch('/api/school-years');
                this.schoolYears = await response.json();
            } catch (error) {
                console.error('Error loading school years:', error);
            }
        },

        async loadData() {
            try {
                const response = await fetch('/api/panel-members');
                const members = await response.json();
                
                this.allData = members.map(m => ({
                    ...m,
                    groups_count: m.groups_count || 0,
                    etl_total: this.calculateEtl(m)
                }));
                
                this.applyFilters();
            } catch (error) { {
                console.error('Error loading data:', error);
            }
        },

        calculateEtl(member) {
            const groupsCount = member.groups_count || 0;
            const etlRates = { 'Adviser': 0.5, 'Chair': 0.3, 'Critique': 0.3 };
            return groupsCount * (etlRates[member.role] || 0.3);
        },

        applyFilters() {
            let data = [...this.allData];

            if (this.filters.role) data = data.filter(m => m.role === this.filters.role);
            if (this.filters.status) data = data.filter(m => m.status === this.filters.status);
            if (this.filters.minEtl !== '') data = data.filter(m => m.etl_total >= parseFloat(this.filters.minEtl));
            if (this.filters.maxEtl !== '') data = data.filter(m => m.etl_total <= parseFloat(this.filters.maxEtl));
            if (this.filters.search) {
                const search = this.filters.search.toLowerCase();
                data = data.filter(m => m.name.toLowerCase().includes(search));
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
            this.summary.totalMembers = this.filteredData.length;
            this.summary.totalEtl = this.filteredData.reduce((sum, m) => sum + m.etl_total, 0);
            this.summary.averageEtl = this.summary.totalMembers > 0 ? this.summary.totalEtl / this.summary.totalMembers : 0;
            this.summary.totalGroups = this.filteredData.reduce((sum, m) => sum + (m.groups_count || 0), 0);
        },

        sortBy(field) {
            if (this.sortField === field) this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            else { this.sortField = field; this.sortDirection = 'desc'; }
            this.applyFilters();
        },

        resetFilters() {
            this.filters = { schoolYear: '', role: '', status: '', minEtl: '', maxEtl: '', search: '' };
            this.applyFilters();
        },

        exportToPDF() {
            window.open('/api/reports/etl/export?format=pdf', '_blank');
        },

        exportToCSV() {
            const headers = ['Name', 'Role', 'Status', 'Groups', 'ETL Total'];
            const rows = this.filteredData.map(m => [m.name, m.role, m.status, m.groups_count || 0, m.etl_total.toFixed(2)]);
            let csv = headers.join(',') + '\n';
            rows.forEach(row => { csv += row.map(cell => '"' + String(cell).replace(/"/g, '""') + '"').join(',') + '\n'; });
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'etl_report_' + new Date().toISOString().split('T')[0] + '.csv';
            a.click();
        }
    }
}
</script>
@endsection
