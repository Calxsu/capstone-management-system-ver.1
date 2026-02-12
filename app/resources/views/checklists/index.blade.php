@extends('layouts.dashboard')

@section('title', 'CAPSTONE 2 Checklists')
@section('subtitle', 'Track completion requirements for CAPSTONE 2 groups')

@section('content')
<div x-data="checklistsData()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 animate-slide-up">
        <div class="flex items-center space-x-4">
            <div class="relative">
                <input type="text" x-model="searchQuery" @input.debounce.300ms="filter()"
                       placeholder="Search by group ID or title..." 
                       class="w-64 pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 bg-white transition-all">
                <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <select x-model="statusFilter" @change="filter()" class="px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 bg-white">
                <option value="">All Status</option>
                <option value="complete">Complete</option>
                <option value="incomplete">Incomplete</option>
            </select>
        </div>
        <a href="{{ route('checklists.items') }}" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-medium rounded-xl shadow-lg shadow-emerald-500/30 hover:shadow-xl transition-all duration-300 hover:-translate-y-0.5">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            Manage Checklist Items
        </a>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 border border-gray-100 animate-slide-up" style="animation-delay: 0.1s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">CAPSTONE 2 Groups</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="groups.length">0</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-xl">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100 animate-slide-up" style="animation-delay: 0.15s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Fully Complete</p>
                    <p class="text-2xl font-bold text-emerald-600" x-text="completeCount">0</p>
                </div>
                <div class="p-3 bg-emerald-100 rounded-xl">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100 animate-slide-up" style="animation-delay: 0.2s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">In Progress</p>
                    <p class="text-2xl font-bold text-yellow-600" x-text="inProgressCount">0</p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-xl">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100 animate-slide-up" style="animation-delay: 0.25s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Avg. Progress</p>
                    <p class="text-2xl font-bold text-purple-600" x-text="avgProgress + '%'">0%</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-xl">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading State -->
    <div x-show="loading" class="text-center py-16">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-emerald-500 mx-auto"></div>
        <p class="mt-4 text-gray-500">Loading groups...</p>
    </div>

    <!-- Groups Grid -->
    <div x-show="!loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <template x-for="group in paginatedGroups" :key="group.id">
            <a :href="'/groups/' + group.id + '/checklist'" 
               class="block bg-white rounded-xl border border-gray-100 p-6 hover:shadow-lg transition-all hover:-translate-y-1 cursor-pointer">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-xl flex items-center justify-center text-white font-bold mr-4"
                             x-text="'#' + group.id"></div>
                        <div>
                            <p class="font-semibold text-gray-900" x-text="group.project_title || 'Untitled Project'"></p>
                            <p class="text-sm text-gray-500" x-text="group.students_count + ' students'"></p>
                        </div>
                    </div>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full"
                          :class="group.is_complete ? 'bg-emerald-100 text-emerald-700' : 'bg-yellow-100 text-yellow-700'"
                          x-text="group.is_complete ? 'Complete' : 'In Progress'"></span>
                </div>
                
                <!-- Progress Bar -->
                <div class="mb-3">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">Completion</span>
                        <span class="font-medium" x-text="group.completed_items + '/' + group.total_items + ' items'"></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="h-2.5 rounded-full transition-all duration-500"
                             :class="group.progress_percentage >= 100 ? 'bg-emerald-500' : 
                                    (group.progress_percentage >= 50 ? 'bg-blue-500' : 'bg-yellow-500')"
                             :style="'width: ' + group.progress_percentage + '%'"></div>
                    </div>
                </div>
                
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Progress</span>
                    <span class="font-bold" 
                          :class="group.progress_percentage >= 100 ? 'text-emerald-600' : 
                                 (group.progress_percentage >= 50 ? 'text-blue-600' : 'text-yellow-600')"
                          x-text="group.progress_percentage + '%'"></span>
                </div>
            </a>
        </template>
    </div>

    <!-- Empty State -->
    <div x-show="!loading && filteredGroups.length === 0" class="text-center py-16 bg-white rounded-xl border border-gray-100">
        <div class="w-20 h-20 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
            </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No CAPSTONE 2 groups found</h3>
        <p class="text-gray-500 mb-6">There are no groups in CAPSTONE 2 stage yet.</p>
    </div>

    <!-- Pagination -->
    <div x-show="!loading && filteredGroups.length > 0" class="mt-6 flex items-center justify-between bg-white rounded-xl p-4 border border-gray-100">
        <div class="text-sm text-gray-500">
            Showing <span class="font-medium" x-text="((currentPage - 1) * perPage) + 1"></span> to 
            <span class="font-medium" x-text="Math.min(currentPage * perPage, filteredGroups.length)"></span> of 
            <span class="font-medium" x-text="filteredGroups.length"></span> results
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
</div>

<script>
function checklistsData() {
    return {
        groups: [],
        filteredGroups: [],
        searchQuery: '',
        statusFilter: '',
        loading: true,
        completeCount: 0,
        inProgressCount: 0,
        avgProgress: 0,
        // Pagination
        currentPage: 1,
        perPage: 10,

        get paginatedGroups() {
            const start = (this.currentPage - 1) * this.perPage;
            return this.filteredGroups.slice(start, start + this.perPage);
        },

        get totalPages() {
            return Math.ceil(this.filteredGroups.length / this.perPage);
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
            this.loadGroups();
        },

        async loadGroups() {
            try {
                const response = await fetch('/api/checklists/cap2-progress');
                this.groups = await response.json();
                this.filteredGroups = this.groups;
                this.calculateStats();
            } catch (error) {
                console.error('Error loading groups:', error);
            } finally {
                this.loading = false;
            }
        },

        calculateStats() {
            this.completeCount = this.groups.filter(g => g.is_complete).length;
            this.inProgressCount = this.groups.filter(g => !g.is_complete).length;
            if (this.groups.length > 0) {
                const total = this.groups.reduce((sum, g) => sum + g.progress_percentage, 0);
                this.avgProgress = Math.round(total / this.groups.length);
            }
        },

        filter() {
            let result = this.groups;

            if (this.searchQuery) {
                const query = this.searchQuery.toLowerCase();
                result = result.filter(g => 
                    (g.project_title || '').toLowerCase().includes(query) ||
                    g.id.toString().includes(query)
                );
            }

            if (this.statusFilter === 'complete') {
                result = result.filter(g => g.is_complete);
            } else if (this.statusFilter === 'incomplete') {
                result = result.filter(g => !g.is_complete);
            }

            this.filteredGroups = result;
            this.currentPage = 1;
        }
    }
}
</script>
@endsection
