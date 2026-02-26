@extends('layouts.dashboard')

@section('title', 'ETL Report')
@section('subtitle', 'Extract-Transform-Load analysis for panel members')

@section('content')
    <div x-data="{ allData: [], filteredData: [], schoolYears: [], filters: { schoolYear: '', role: '', status: '', minEtl: '', maxEtl: '', search: '' }, summary: { totalMembers: 0, totalEtl: 0, averageEtl: 0, totalGroups: 0 } }" x-init="async () => {
        await fetch('/api/school-years').then(r => r.json()).then(data => schoolYears = data);
        await fetch('/api/reports/etl').then(r => r.json()).then(data => {
            allData = data.data.panel_members.map(m => ({
                ...m,
                name: m.email,
                role: m.roles.length > 0 ? m.roles[0] : 'Critique',
                roles: m.roles || [m.role || 'Critique'],
                groups_count: m.groups_count,
                etl_total: m.etl_total
            }));
            filteredData = [...allData];
            summary.totalMembers = filteredData.length;
            summary.totalEtl = filteredData.reduce((sum, m) => sum + m.etl_total, 0);
            summary.averageEtl = summary.totalMembers > 0 ? summary.totalEtl / summary.totalMembers : 0;
            summary.totalGroups = filteredData.reduce((sum, m) => sum + (m.groups_count || 0), 0);
        });
    }">
        <!-- Filters Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6 animate-slide-up">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Report Filters</h3>
                <button @click="async () => {
                    filters = { schoolYear: '', role: '', status: '', minEtl: '', maxEtl: '', search: '' };
                    await fetch('/api/reports/etl').then(r => r.json()).then(data => {
                        allData = data.data.panel_members.map(m => ({
                            ...m,
                            name: m.email,
                            role: m.roles.length > 0 ? m.roles[0] : 'Critique',
                            roles: m.roles || [m.role || 'Critique'],
                            groups_count: m.groups_count,
                            etl_total: m.etl_total
                        }));
                        filteredData = [...allData];
                        summary.totalMembers = filteredData.length;
                        summary.totalEtl = filteredData.reduce((sum, m) => sum + m.etl_total, 0);
                        summary.averageEtl = summary.totalMembers > 0 ? summary.totalEtl / summary.totalMembers : 0;
                        summary.totalGroups = filteredData.reduce((sum, m) => sum + (m.groups_count || 0), 0);
                    });
                }" class="text-sm text-gray-500 hover:text-gray-700 transition-colors">
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
                    <select x-model="filters.schoolYear" @change="async () => {
                        const params = new URLSearchParams();
                        if (filters.schoolYear) params.append('school_year_id', filters.schoolYear);
                        const response = await fetch(`/api/reports/etl?${params.toString()}`);
                        const data = await response.json();
                        allData = data.data.panel_members.map(m => ({
                            ...m,
                            name: m.email,
                            role: m.roles.length > 0 ? m.roles[0] : 'Critique',
                            roles: m.roles || [m.role || 'Critique'],
                            groups_count: m.groups_count,
                            etl_total: m.etl_total
                        }));
                        filteredData = [...allData];
                        summary.totalMembers = filteredData.length;
                        summary.totalEtl = filteredData.reduce((sum, m) => sum + m.etl_total, 0);
                        summary.averageEtl = summary.totalMembers > 0 ? summary.totalEtl / summary.totalMembers : 0;
                        summary.totalGroups = filteredData.reduce((sum, m) => sum + (m.groups_count || 0), 0);
                    }" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white">
                        <option value="">All School Years</option>
                        <template x-for="sy in schoolYears" :key="sy.id">
                            <option :value="sy.id" x-text="sy.year + ' - ' + sy.semester"></option>
                        </template>
                    </select>
                </div>

                <!-- Role Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                    <select x-model="filters.role" @change="() => {
                        filteredData = allData.filter(m => {
                            if (filters.role && !m.roles.includes(filters.role)) return false;
                            if (filters.status && m.status !== filters.status) return false;
                            if (filters.minEtl && m.etl_total < parseFloat(filters.minEtl)) return false;
                            if (filters.maxEtl && m.etl_total > parseFloat(filters.maxEtl)) return false;
                            if (filters.search && !m.name.toLowerCase().includes(filters.search.toLowerCase())) return false;
                            return true;
                        });
                        summary.totalMembers = filteredData.length;
                        summary.totalEtl = filteredData.reduce((sum, m) => sum + m.etl_total, 0);
                        summary.averageEtl = summary.totalMembers > 0 ? summary.totalEtl / summary.totalMembers : 0;
                        summary.totalGroups = filteredData.reduce((sum, m) => sum + (m.groups_count || 0), 0);
                    }" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white">
                        <option value="">All Roles</option>
                        <option value="Adviser">Adviser</option>
                        <option value="Chair">Chair</option>
                        <option value="Critique">Critique</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select x-model="filters.status" @change="() => {
                        filteredData = allData.filter(m => {
                            if (filters.role && !m.roles.includes(filters.role)) return false;
                            if (filters.status && m.status !== filters.status) return false;
                            if (filters.minEtl && m.etl_total < parseFloat(filters.minEtl)) return false;
                            if (filters.maxEtl && m.etl_total > parseFloat(filters.maxEtl)) return false;
                            if (filters.search && !m.name.toLowerCase().includes(filters.search.toLowerCase())) return false;
                            return true;
                        });
                        summary.totalMembers = filteredData.length;
                        summary.totalEtl = filteredData.reduce((sum, m) => sum + m.etl_total, 0);
                        summary.averageEtl = summary.totalMembers > 0 ? summary.totalEtl / summary.totalMembers : 0;
                        summary.totalGroups = filteredData.reduce((sum, m) => sum + (m.groups_count || 0), 0);
                    }" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white">
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
                    <input type="number" x-model="filters.minEtl" @input.debounce.300ms="() => {
                        filteredData = allData.filter(m => {
                            if (filters.role && !m.roles.includes(filters.role)) return false;
                            if (filters.status && m.status !== filters.status) return false;
                            if (filters.minEtl && m.etl_total < parseFloat(filters.minEtl)) return false;
                            if (filters.maxEtl && m.etl_total > parseFloat(filters.maxEtl)) return false;
                            if (filters.search && !m.name.toLowerCase().includes(filters.search.toLowerCase())) return false;
                            return true;
                        });
                        summary.totalMembers = filteredData.length;
                        summary.totalEtl = filteredData.reduce((sum, m) => sum + m.etl_total, 0);
                        summary.averageEtl = summary.totalMembers > 0 ? summary.totalEtl / summary.totalMembers : 0;
                        summary.totalGroups = filteredData.reduce((sum, m) => sum + (m.groups_count || 0), 0);
                    }" step="0.1" min="0" placeholder="0.0" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Max ETL</label>
                    <input type="number" x-model="filters.maxEtl" @input.debounce.300ms="() => {
                        filteredData = allData.filter(m => {
                            if (filters.role && !m.roles.includes(filters.role)) return false;
                            if (filters.status && m.status !== filters.status) return false;
                            if (filters.minEtl && m.etl_total < parseFloat(filters.minEtl)) return false;
                            if (filters.maxEtl && m.etl_total > parseFloat(filters.maxEtl)) return false;
                            if (filters.search && !m.name.toLowerCase().includes(filters.search.toLowerCase())) return false;
                            return true;
                        });
                        summary.totalMembers = filteredData.length;
                        summary.totalEtl = filteredData.reduce((sum, m) => sum + m.etl_total, 0);
                        summary.averageEtl = summary.totalMembers > 0 ? summary.totalEtl / summary.totalMembers : 0;
                        summary.totalGroups = filteredData.reduce((sum, m) => sum + (m.groups_count || 0), 0);
                    }" step="0.1" min="0" placeholder="10.0" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search Email</label>
                    <input type="text" x-model="filters.search" @input.debounce.300ms="() => {
                        filteredData = allData.filter(m => {
                            if (filters.role && !m.roles.includes(filters.role)) return false;
                            if (filters.status && m.status !== filters.status) return false;
                            if (filters.minEtl && m.etl_total < parseFloat(filters.minEtl)) return false;
                            if (filters.maxEtl && m.etl_total > parseFloat(filters.maxEtl)) return false;
                            if (filters.search && !m.name.toLowerCase().includes(filters.search.toLowerCase())) return false;
                            return true;
                        });
                        summary.totalMembers = filteredData.length;
                        summary.totalEtl = filteredData.reduce((sum, m) => sum + m.etl_total, 0);
                        summary.averageEtl = summary.totalMembers > 0 ? summary.totalEtl / summary.totalMembers : 0;
                        summary.totalGroups = filteredData.reduce((sum, m) => sum + (m.groups_count || 0), 0);
                    }" placeholder="Search..." class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white">
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

        <!-- Data Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up" style="animation-delay: 0.35s">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    Email
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Groups</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">ETL Total</th>
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
                                    <div class="flex flex-wrap gap-1">
                                        <template x-for="role in member.roles" :key="role">
                                            <span :class="{
                                                'bg-purple-100 text-purple-700': role === 'Adviser',
                                                'bg-emerald-100 text-emerald-700': role === 'Chair',
                                                'bg-orange-100 text-orange-700': role === 'Critique'
                                            }" class="px-3 py-1 text-xs font-semibold rounded-full" x-text="role"></span>
                                        </template>
                                    </div>
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
@endsection
