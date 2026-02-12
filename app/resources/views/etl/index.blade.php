@extends('layouts.dashboard')

@section('title', 'Equivalent Teaching Load')
@section('subtitle', 'Compute and manage ETL for all panel members')

@section('content')
<div x-data="etlData()" x-init="init()">
    <!-- Info Box about ETL Computation -->
    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-xl animate-slide-up">
        <div class="flex">
            <svg class="w-5 h-5 text-blue-500 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <p class="text-sm text-blue-700 font-medium">ETL Computation Rules</p>
                <ul class="text-sm text-blue-600 mt-1 list-disc list-inside space-y-1">
                    <li>ETL = (Number of groups × Role value) for each role</li>
                    <li>Role Values: Adviser (0.5), Chair (0.3), Critique (0.3)</li>
                    <li><strong>Completed projects (those with grades) are NOT included</strong> in the computation</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up hover-lift">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Panel Members</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1" x-text="stats.total_members">0</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up hover-lift" style="animation-delay: 0.1s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total ETL Units</p>
                    <p class="text-3xl font-bold text-purple-600 mt-1" x-text="stats.total_etl.toFixed(2)">0</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-purple-400 to-purple-500 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up hover-lift" style="animation-delay: 0.2s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Average ETL</p>
                    <p class="text-3xl font-bold text-green-600 mt-1" x-text="stats.avg_etl.toFixed(2)">0</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-500 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up hover-lift" style="animation-delay: 0.3s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Role Values</p>
                    <div class="mt-1 space-y-1">
                        <p class="text-xs text-gray-500">Adviser: <span class="font-semibold text-purple-600" x-text="roleValues['Adviser'] || 0.50"></span></p>
                        <p class="text-xs text-gray-500">Chair: <span class="font-semibold text-emerald-600" x-text="roleValues['Chair'] || 0.30"></span></p>
                        <p class="text-xs text-gray-500">Critique: <span class="font-semibold text-orange-600" x-text="roleValues['Critique'] || 0.30"></span></p>
                    </div>
                </div>
                <a href="{{ route('etl.role-values') }}" class="w-12 h-12 bg-gradient-to-br from-orange-400 to-orange-500 rounded-xl flex items-center justify-center hover:opacity-80 transition">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 animate-slide-up" style="animation-delay: 0.4s">
        <div class="flex items-center gap-4">
            <div class="relative">
                <input type="text" x-model="searchQuery" @input.debounce.300ms="filterData()"
                       placeholder="Search by name or specialization..." 
                       class="w-64 pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white transition-all">
                <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <select x-model="selectedSchoolYear" @change="loadData()" class="px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white">
                <option value="">All Semesters</option>
                @foreach($schoolYears as $sy)
                    <option value="{{ $sy->id }}">{{ $sy->year }} - Sem {{ $sy->semester }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-center gap-3">
            <button @click="exportData()" class="inline-flex items-center px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white font-medium rounded-xl shadow-lg shadow-green-500/30 transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export
            </button>
        </div>
    </div>

    <!-- ETL Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up" style="animation-delay: 0.5s">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Panel Member</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <div class="flex flex-col items-center">
                                <span>Adviser</span>
                                <span class="text-purple-500 font-normal">(<span x-text="roleValues['Adviser'] || 0.50"></span>)</span>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <div class="flex flex-col items-center">
                                <span>Chair</span>
                                <span class="text-emerald-500 font-normal">(<span x-text="roleValues['Chair'] || 0.30"></span>)</span>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <div class="flex flex-col items-center">
                                <span>Critique</span>
                                <span class="text-orange-500 font-normal">(<span x-text="roleValues['Critique'] || 0.30"></span>)</span>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Total ETL</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    <template x-for="member in paginatedData" :key="member.id">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white font-semibold">
                                        <span x-text="member.name.charAt(0)"></span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900" x-text="member.name"></div>
                                        <div class="text-sm text-gray-500" x-text="member.specialization || 'Faculty'"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex flex-col items-center">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-700" x-text="member.adviser_count + ' groups'"></span>
                                    <span class="text-sm font-medium text-gray-700 mt-1" x-text="member.adviser_etl.toFixed(2)"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex flex-col items-center">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700" x-text="member.chair_count + ' groups'"></span>
                                    <span class="text-sm font-medium text-gray-700 mt-1" x-text="member.chair_etl.toFixed(2)"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex flex-col items-center">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-700" x-text="member.critique_count + ' groups'"></span>
                                    <span class="text-sm font-medium text-gray-700 mt-1" x-text="member.critique_etl.toFixed(2)"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="px-3 py-1.5 text-sm font-bold rounded-lg bg-gradient-to-r from-blue-500 to-purple-500 text-white" x-text="member.total_etl.toFixed(2)"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <button @click="viewDetails(member)" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Empty State -->
        <div x-show="filteredData.length === 0 && !loading" class="text-center py-16">
            <div class="w-20 h-20 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No ETL data found</h3>
            <p class="text-gray-500">Panel members with group assignments will appear here</p>
        </div>

        <!-- Pagination -->
        <div x-show="filteredData.length > 0" class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
            <div class="text-sm text-gray-500">
                Showing <span class="font-medium" x-text="((currentPage - 1) * perPage) + 1"></span> to 
                <span class="font-medium" x-text="Math.min(currentPage * perPage, filteredData.length)"></span> of 
                <span class="font-medium" x-text="filteredData.length"></span> results
            </div>
            <div class="flex items-center space-x-2">
                <button @click="prevPage()" :disabled="currentPage === 1" 
                        :class="currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'"
                        class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg transition-colors">
                    Previous
                </button>
                <template x-for="page in totalPages" :key="page">
                    <button @click="goToPage(page)" 
                            :class="page === currentPage ? 'bg-blue-500 text-white border-blue-500' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'"
                            class="px-3 py-2 text-sm font-medium border rounded-lg transition-colors"
                            x-text="page"></button>
                </template>
                <button @click="nextPage()" :disabled="currentPage === totalPages" 
                        :class="currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'"
                        class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg transition-colors">
                    Next
                </button>
            </div>
        </div>

        <!-- Loading -->
        <div x-show="loading" class="text-center py-16">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
            <p class="text-gray-500 mt-4">Loading ETL data...</p>
        </div>
    </div>

    <!-- Details Modal -->
    <div x-show="showDetailsModal" x-transition class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showDetailsModal = false"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-4xl w-full p-6 animate-scale-in max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white font-bold text-xl mr-4">
                            <span x-text="selectedMember?.name?.charAt(0) || 'P'"></span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900" x-text="selectedMember?.name"></h3>
                            <p class="text-gray-500" x-text="selectedMember?.specialization || 'Faculty'"></p>
                        </div>
                    </div>
                    <button @click="showDetailsModal = false" class="p-2 hover:bg-gray-100 rounded-lg transition">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- ETL Summary -->
                <div class="grid grid-cols-4 gap-4 mb-6">
                    <div class="bg-blue-50 rounded-xl p-4 text-center">
                        <p class="text-xs text-blue-600 font-medium">Adviser ETL</p>
                        <p class="text-2xl font-bold text-blue-700" x-text="selectedMember?.adviser_etl?.toFixed(2) || '0.00'"></p>
                    </div>
                    <div class="bg-purple-50 rounded-xl p-4 text-center">
                        <p class="text-xs text-purple-600 font-medium">Chair ETL</p>
                        <p class="text-2xl font-bold text-purple-700" x-text="selectedMember?.chair_etl?.toFixed(2) || '0.00'"></p>
                    </div>
                    <div class="bg-orange-50 rounded-xl p-4 text-center">
                        <p class="text-xs text-orange-600 font-medium">Critique ETL</p>
                        <p class="text-2xl font-bold text-orange-700" x-text="selectedMember?.critique_etl?.toFixed(2) || '0.00'"></p>
                    </div>
                    <div class="bg-gradient-to-r from-blue-500 to-purple-500 rounded-xl p-4 text-center text-white">
                        <p class="text-xs font-medium opacity-80">Total ETL</p>
                        <p class="text-2xl font-bold" x-text="selectedMember?.total_etl?.toFixed(2) || '0.00'"></p>
                    </div>
                </div>

                <!-- Group Assignments -->
                <h4 class="text-lg font-semibold text-gray-900 mb-4">Group Assignments</h4>
                <p class="text-sm text-gray-500 mb-3"><span class="text-green-600 font-medium">Ongoing projects</span> are counted for ETL. <span class="text-gray-400 font-medium">Completed projects</span> (with grades) are excluded.</p>
                <div class="space-y-3" x-show="memberDetails?.assignments?.length > 0">
                    <template x-for="assignment in memberDetails?.assignments || []" :key="assignment.group_id">
                        <div class="flex items-center justify-between p-4 rounded-xl" :class="assignment.is_complete ? 'bg-gray-100 opacity-60' : 'bg-green-50 border border-green-200'">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center text-sm font-semibold" :class="assignment.is_complete ? 'bg-gray-200 border border-gray-300 text-gray-500' : 'bg-white border border-green-300 text-green-600'" x-text="'#' + assignment.group_id"></div>
                                <div class="ml-4">
                                    <p class="font-medium" :class="assignment.is_complete ? 'text-gray-500' : 'text-gray-900'" x-text="assignment.project_title"></p>
                                    <p class="text-sm text-gray-500" x-text="assignment.school_year + ' - Sem ' + assignment.semester"></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full"
                                      :class="{
                                          'bg-purple-100 text-purple-700': assignment.role === 'Adviser',
                                          'bg-emerald-100 text-emerald-700': assignment.role === 'Chair',
                                          'bg-orange-100 text-orange-700': assignment.role === 'Critique'
                                      }"
                                      x-text="assignment.role"></span>
                                <span class="px-3 py-1 text-xs font-semibold rounded-full"
                                      :class="assignment.is_complete ? 'bg-gray-200 text-gray-500' : 'bg-green-100 text-green-700'"
                                      x-text="assignment.is_complete ? 'Completed (Has Grades)' : 'Ongoing (Counted)'"></span>
                            </div>
                        </div>
                    </template>
                </div>
                <div x-show="!memberDetails?.assignments?.length" class="text-center py-8 text-gray-500">
                    No group assignments found
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function etlData() {
    return {
        data: [],
        filteredData: [],
        searchQuery: '',
        selectedSchoolYear: '{{ $schoolYearId ?? "" }}',
        loading: true,
        showDetailsModal: false,
        selectedMember: null,
        memberDetails: null,
        stats: { total_members: 0, total_etl: 0, avg_etl: 0 },
        roleValues: {},
        // Pagination
        currentPage: 1,
        perPage: 10,

        get paginatedData() {
            const start = (this.currentPage - 1) * this.perPage;
            return this.filteredData.slice(start, start + this.perPage);
        },

        get totalPages() {
            return Math.ceil(this.filteredData.length / this.perPage);
        },

        prevPage() {
            if (this.currentPage > 1) this.currentPage--;
        },

        nextPage() {
            if (this.currentPage < this.totalPages) this.currentPage++;
        },

        goToPage(page) {
            this.currentPage = page;
        },

        init() {
            this.loadData();
        },

        async loadData() {
            this.loading = true;
            try {
                const url = new URL('{{ route("etl.data") }}', window.location.origin);
                if (this.selectedSchoolYear) {
                    url.searchParams.set('school_year_id', this.selectedSchoolYear);
                }
                const response = await fetch(url);
                const result = await response.json();
                this.data = result.data;
                this.stats = result.stats;
                this.roleValues = result.role_values;
                this.filterData();
            } catch (error) {
                console.error('Error loading ETL data:', error);
            } finally {
                this.loading = false;
            }
        },

        filterData() {
            if (!this.searchQuery) {
                this.filteredData = this.data;
            } else {
                const query = this.searchQuery.toLowerCase();
                this.filteredData = this.data.filter(member => 
                    member.name.toLowerCase().includes(query) ||
                    (member.specialization || '').toLowerCase().includes(query)
                );
            }
            this.currentPage = 1;
        },

        async viewDetails(member) {
            this.selectedMember = member;
            this.showDetailsModal = true;
            
            try {
                const response = await fetch(`/etl/member/${member.id}/details`);
                this.memberDetails = await response.json();
            } catch (error) {
                console.error('Error loading member details:', error);
            }
        },

        exportData() {
            // Create CSV
            let csv = 'Panel Member,Adviser Groups,Adviser ETL,Chair Groups,Chair ETL,Critique Groups,Critique ETL,Total ETL\n';
            this.filteredData.forEach(member => {
                csv += `"${member.name}",${member.adviser_count},${member.adviser_etl.toFixed(2)},${member.chair_count},${member.chair_etl.toFixed(2)},${member.critique_count},${member.critique_etl.toFixed(2)},${member.total_etl.toFixed(2)}\n`;
            });

            // Download
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `etl-report-${new Date().toISOString().split('T')[0]}.csv`;
            a.click();
        }
    };
}
</script>
@endsection
