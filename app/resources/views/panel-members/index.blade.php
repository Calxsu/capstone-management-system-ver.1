@extends('layouts.dashboard')

@section('title', 'Panel Members')
@section('subtitle', 'Manage thesis advisers, chairs, and critiques')

@section('content')
<div x-data="panelMembersData()">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up hover-lift">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Members</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1" x-text="stats.total">0</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up hover-lift" style="animation-delay: 0.1s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Active</p>
                    <p class="text-3xl font-bold text-green-600 mt-1" x-text="stats.active">0</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-500 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up hover-lift" style="animation-delay: 0.2s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Inactive</p>
                    <p class="text-3xl font-bold text-gray-600 mt-1" x-text="stats.inactive">0</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-gray-400 to-gray-500 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up hover-lift" style="animation-delay: 0.3s">
            <a href="{{ route('etl.index') }}" class="block">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">ETL Computation</p>
                        <p class="text-lg font-bold text-purple-600 mt-1">View ETL →</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-400 to-purple-500 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 animate-slide-up" style="animation-delay: 0.4s">
        <div class="flex items-center gap-4">
            <div class="relative">
                        <input type="text" x-model="searchQuery" @input.debounce.300ms="filter()"
                               placeholder="Search by email..." 
                               class="w-64 pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white transition-all">
                <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <select x-model="statusFilter" @change="filter()" class="px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
        <a href="{{ route('panel-members.create') }}" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-xl transition-all duration-300 hover:-translate-y-0.5">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
            </svg>
            Add Panel Member
        </a>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center animate-slide-up">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    <!-- Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <template x-for="(member, index) in paginatedItems" :key="member.id">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover-lift transition-all duration-300 animate-slide-up" :style="'animation-delay: ' + (0.5 + index * 0.05) + 's'">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center">
                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-white font-bold text-xl bg-gradient-to-br from-blue-500 to-purple-600">
                                <span x-text="member.email.charAt(0).toUpperCase()"></span>
                            </div>
                            <div class="ml-4">
                                <h3 class="font-semibold text-gray-900" x-text="member.email"></h3>
                                <p class="text-sm text-gray-500" x-text="member.specialization ? member.specialization : 'Faculty'"></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1">
                            <a :href="'/panel-members/' + member.id + '/edit'" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <button @click="confirmDelete(member)" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Status Badge -->
                    <div class="mt-4 flex items-center justify-between">
                        <span :class="member.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'" class="px-3 py-1 text-xs font-semibold rounded-full" x-text="member.status === 'active' ? 'Active' : 'Inactive'"></span>
                        <span class="text-sm text-gray-500" x-text="member.groups_count + ' groups assigned'"></span>
                    </div>

                    <!-- Role Assignments -->
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-xs text-gray-500 mb-2">Current Role Assignments:</p>
                        <div class="flex flex-wrap gap-2">
                            <span x-show="member.adviser_count > 0" class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-700" x-text="'Adviser (' + member.adviser_count + ')'"></span>
                            <span x-show="member.chair_count > 0" class="px-2 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-700" x-text="'Chair (' + member.chair_count + ')'"></span>
                            <span x-show="member.critique_count > 0" class="px-2 py-1 text-xs font-medium rounded-full bg-orange-100 text-orange-700" x-text="'Critique (' + member.critique_count + ')'"></span>
                            <span x-show="member.groups_count === 0" class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-500">No assignments</span>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Empty State -->
    <div x-show="filteredItems.length === 0 && !loading" class="text-center py-16 animate-fade-in">
        <div class="w-20 h-20 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No panel members found</h3>
        <p class="text-gray-500 mb-6">Add your first panel member to get started</p>
        <a href="{{ route('panel-members.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-medium rounded-xl shadow-lg">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
            </svg>
            Add Panel Member
        </a>
    </div>

    <!-- Pagination -->
    <div x-show="filteredItems.length > 0" class="mt-6 flex items-center justify-between bg-white rounded-xl p-4 border border-gray-100">
        <div class="text-sm text-gray-500">
            Showing <span class="font-medium" x-text="((currentPage - 1) * perPage) + 1"></span> to 
            <span class="font-medium" x-text="Math.min(currentPage * perPage, filteredItems.length)"></span> of 
            <span class="font-medium" x-text="filteredItems.length"></span> results
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

    <!-- Delete Modal -->
    <div x-show="showDeleteModal" x-transition class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showDeleteModal = false"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 animate-scale-in">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto bg-red-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Delete Panel Member?</h3>
                    <p class="text-gray-500 mb-6">Are you sure you want to delete "<span x-text="itemToDelete?.email" class="font-medium"></span>"?</p>
                    <div class="flex space-x-3">
                        <button @click="showDeleteModal = false" class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors">Cancel</button>
                        <button @click="deleteItem()" class="flex-1 px-4 py-2.5 bg-red-600 text-white font-medium rounded-xl hover:bg-red-700 transition-colors">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function panelMembersData() {
    return {
        items: [],
        filteredItems: [],
        searchQuery: '',
        statusFilter: '',
        loading: true,
        showDeleteModal: false,
        itemToDelete: null,
        stats: { total: 0, active: 0, inactive: 0 },
        // Pagination
        currentPage: 1,
        perPage: 10,

        get paginatedItems() {
            const start = (this.currentPage - 1) * this.perPage;
            return this.filteredItems.slice(start, start + this.perPage);
        },

        get totalPages() {
            return Math.ceil(this.filteredItems.length / this.perPage);
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
            this.loadItems();
        },

        async loadItems() {
            try {
                const response = await fetch('{{ route("panel-members.data") }}');
                const result = await response.json();
                this.items = result.data;
                this.stats = result.stats;
                this.filteredItems = this.items;
            } catch (error) {
                console.error('Error loading panel members:', error);
            } finally {
                this.loading = false;
            }
        },

        filter() {
            let result = this.items;
            if (this.searchQuery) {
                const query = this.searchQuery.toLowerCase();
                result = result.filter(i => i.email.toLowerCase().includes(query));
            }
            if (this.statusFilter) {
                result = result.filter(i => i.status === this.statusFilter);
            }
            this.filteredItems = result;
            this.currentPage = 1;
        },

        confirmDelete(item) {
            this.itemToDelete = item;
            this.showDeleteModal = true;
        },

        async deleteItem() {
            if (!this.itemToDelete) return;
            try {
                const response = await fetch('/api/panel-members/' + this.itemToDelete.id, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                if (response.ok) {
                    this.items = this.items.filter(i => i.id !== this.itemToDelete.id);
                    this.filter();
                    // Update stats
                    this.stats.total = this.items.length;
                    this.stats.active = this.items.filter(i => i.status === 'active').length;
                    this.stats.inactive = this.items.filter(i => i.status === 'inactive').length;
                }
            } catch (error) {
                console.error('Error:', error);
            } finally {
                this.showDeleteModal = false;
                this.itemToDelete = null;
            }
        }
    }
}
</script>
@endsection
