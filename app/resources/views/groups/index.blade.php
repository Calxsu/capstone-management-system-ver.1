@extends('layouts.dashboard')

@section('title', 'Groups')
@section('subtitle', 'Manage groups and team assignments')

@section('content')
<div x-data="groupsData()">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 animate-slide-up">
        <div class="flex items-center space-x-4">
            <div class="relative">
                <input type="text" 
                       x-model="searchQuery" 
                       @input.debounce.300ms="search()"
                       placeholder="Search by ID, title, or student..." 
                       class="w-64 pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white transition-all">
                <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <select x-model="statusFilter" @change="filter()" class="px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white">
                <option value="">All Status</option>
                <option value="1">CAPSTONE 1</option>
                <option value="2">CAPSTONE 2</option>
            </select>
        </div>
        <a href="{{ route('groups.create') }}" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-xl transition-all duration-300 hover:-translate-y-0.5">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Create Group
        </a>
    </div>

    <!-- Groups Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <template x-for="(group, index) in paginatedGroups" :key="group.id">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover-lift animate-slide-up" :style="'animation-delay: ' + (index * 0.05) + 's'">
                <!-- Card Header -->
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-semibold text-gray-900" x-text="'Group #' + group.id"></h3>
                            <p class="text-sm text-gray-500 mt-1 line-clamp-2" x-text="group.project_title || 'No project assigned'"></p>
                        </div>
                        <span :class="getStatusClass(group.cap_stage)" class="px-3 py-1 text-xs font-semibold rounded-full whitespace-nowrap flex-shrink-0" x-text="'CAPSTONE ' + (group.cap_stage || 1)"></span>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="p-6">
                    <!-- Members List -->
                    <div class="mb-4">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Team Members</p>
                        <ul class="space-y-1.5">
                            <template x-for="(student, i) in (group.students || [])" :key="student.id">
                                <li class="flex items-center text-sm text-gray-700">
                                    <span class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-2"></span>
                                    <span x-text="student.name"></span>
                                </li>
                            </template>
                            <li x-show="!group.students || group.students.length === 0" class="text-sm text-gray-400 italic">No members assigned</li>
                        </ul>
                    </div>

                    <!-- Panelists -->
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Panel Members</p>
                        <div class="space-y-1.5">
                            <template x-for="(panel, i) in (group.panel_members || [])" :key="panel.id">
                                <div class="flex items-center justify-between text-sm">
                                    <div class="flex items-center text-gray-700">
                                        <span class="w-1.5 h-1.5 rounded-full mr-2" :class="panel.pivot?.role === 'Adviser' ? 'bg-purple-500' : panel.pivot?.role === 'Chair' ? 'bg-emerald-500' : 'bg-orange-500'"></span>
                                        <span x-text="panel.email"></span>
                                    </div>
                                    <span class="text-xs px-2 py-0.5 rounded-full" 
                                          :class="panel.pivot?.role === 'Adviser' ? 'bg-purple-100 text-purple-700' : panel.pivot?.role === 'Chair' ? 'bg-emerald-100 text-emerald-700' : 'bg-orange-100 text-orange-700'"
                                          x-text="panel.pivot?.role || 'Member'"></span>
                                </div>
                            </template>
                            <p x-show="!group.panel_members || group.panel_members.length === 0" class="text-sm text-gray-400 italic">No panelists assigned</p>
                        </div>
                    </div>
                </div>

                <!-- Card Footer -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end">
                    <div class="flex items-center space-x-2">
                        <a :href="'/groups/' + group.id + '/edit'" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>
                        <button @click="confirmDelete(group)" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Empty State -->
    <div x-show="filteredGroups.length === 0 && !loading" class="text-center py-16 animate-fade-in">
        <div class="w-24 h-24 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-6">
            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No groups found</h3>
        <p class="text-gray-500 mb-6">Get started by creating your first group</p>
        <a href="{{ route('groups.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-medium rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-xl transition-all duration-300">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Create Group
        </a>
    </div>

    <!-- Pagination -->
    <div x-show="filteredGroups.length > 0" class="mt-6 flex items-center justify-between bg-white rounded-xl p-4 border border-gray-100">
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

    <!-- Loading State -->
    <div x-show="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <template x-for="i in 6" :key="i">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="animate-pulse">
                    <div class="h-6 bg-gray-200 rounded w-3/4 mb-4"></div>
                    <div class="h-4 bg-gray-200 rounded w-1/2 mb-6"></div>
                    <div class="flex space-x-2 mb-4">
                        <div class="w-8 h-8 bg-gray-200 rounded-full"></div>
                        <div class="w-8 h-8 bg-gray-200 rounded-full"></div>
                        <div class="w-8 h-8 bg-gray-200 rounded-full"></div>
                    </div>
                    <div class="h-2 bg-gray-200 rounded-full"></div>
                </div>
            </div>
        </template>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showDeleteModal = false"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 animate-scale-in">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto bg-red-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Delete Group?</h3>
                    <p class="text-gray-500 mb-6">Are you sure you want to delete "<span x-text="groupToDelete?.name" class="font-medium"></span>"? This action cannot be undone.</p>
                    <div class="flex space-x-3">
                        <button @click="showDeleteModal = false" class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button @click="deleteGroup()" class="flex-1 px-4 py-2.5 bg-red-600 text-white font-medium rounded-xl hover:bg-red-700 transition-colors">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function groupsData() {
    return {
        groups: [],
        filteredGroups: [],
        searchQuery: '',
        statusFilter: '',
        loading: true,
        showDeleteModal: false,
        groupToDelete: null,
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
                this.loading = true;
                const response = await fetch('/api/groups');
                this.groups = await response.json();
                this.filteredGroups = this.groups;
            } catch (error) {
                console.error('Error loading groups:', error);
            } finally {
                this.loading = false;
            }
        },

        search() {
            this.filter();
        },

        filter() {
            let result = this.groups;

            if (this.searchQuery) {
                const query = this.searchQuery.toLowerCase();
                result = result.filter(g => 
                    g.id.toString().includes(query) ||
                    (g.project_title && g.project_title.toLowerCase().includes(query)) ||
                    (g.students && g.students.some(s => s.name.toLowerCase().includes(query)))
                );
            }

            if (this.statusFilter) {
                result = result.filter(g => g.cap_stage == this.statusFilter || g.cap_status === 'CAP' + this.statusFilter);
            }

            this.filteredGroups = result;
            this.currentPage = 1;
        },

        getStatusClass(status) {
            const stage = parseInt(status) || status;
            if (stage == 1 || status === 'CAP1') return 'bg-blue-100 text-blue-700';
            if (stage == 2 || status === 'CAP2') return 'bg-emerald-100 text-emerald-700';
            return 'bg-gray-100 text-gray-700';
        },

        confirmDelete(group) {
            this.groupToDelete = group;
            this.showDeleteModal = true;
        },

        async deleteGroup() {
            if (!this.groupToDelete) return;
            
            try {
                const response = await fetch('/api/groups/' + this.groupToDelete.id, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    this.groups = this.groups.filter(g => g.id !== this.groupToDelete.id);
                    this.filter();
                }
            } catch (error) {
                console.error('Error deleting group:', error);
            } finally {
                this.showDeleteModal = false;
                this.groupToDelete = null;
            }
        }
    }
}
</script>
@endsection
