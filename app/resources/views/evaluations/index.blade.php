@extends('layouts.dashboard')

@section('title', 'Evaluations')
@section('subtitle', 'Manage group evaluations and grades')

@section('content')
<div x-data="evaluationsData()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 animate-slide-up">
        <div class="flex items-center space-x-4">
            <div class="relative">
                <input type="text" x-model="searchQuery" @input.debounce.300ms="filter()"
                       placeholder="Search by group ID, title, or panelist..." 
                       class="w-64 pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white transition-all">
                <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <select x-model="capFilter" @change="filter()" class="px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white">
                <option value="">All CAPSTONE Stages</option>
                <option value="1">CAPSTONE 1</option>
                <option value="2">CAPSTONE 2</option>
            </select>
        </div>
        <a href="{{ route('evaluations.create') }}" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white font-medium rounded-xl shadow-lg shadow-purple-500/30 hover:shadow-xl transition-all duration-300 hover:-translate-y-0.5">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            New Evaluation
        </a>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 border border-gray-100 animate-slide-up" style="animation-delay: 0.1s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Evaluations</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="evaluations.length">0</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-xl">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100 animate-slide-up" style="animation-delay: 0.15s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Average Grade</p>
                    <p class="text-2xl font-bold text-emerald-600" x-text="averageGrade.toFixed(1)">0.0</p>
                </div>
                <div class="p-3 bg-emerald-100 rounded-xl">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100 animate-slide-up" style="animation-delay: 0.2s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">CAPSTONE 1 Evals</p>
                    <p class="text-2xl font-bold text-blue-600" x-text="cap1Count">0</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-xl">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100 animate-slide-up" style="animation-delay: 0.25s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">CAPSTONE 2 Evals</p>
                    <p class="text-2xl font-bold text-emerald-600" x-text="cap2Count">0</p>
                </div>
                <div class="p-3 bg-emerald-100 rounded-xl">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Evaluations Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-slide-up" style="animation-delay: 0.3s">
        <template x-for="(group, index) in paginatedEvaluations" :key="group.group_id + '-' + group.cap_stage">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover-lift" :style="'animation-delay: ' + (index * 0.05) + 's'">
                <!-- Card Header -->
                <div class="p-5 border-b border-gray-100">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center min-w-0 flex-1">
                            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center text-white font-semibold mr-3 flex-shrink-0"
                                 x-text="'#' + group.group_id"></div>
                            <div class="min-w-0">
                                <h3 class="font-semibold text-gray-900 truncate" x-text="group.project_title || 'Untitled Project'"></h3>
                                <p class="text-sm text-gray-500" x-text="(group.students_count || 0) + ' students'"></p>
                            </div>
                        </div>
                        <span :class="getCapClass(group.cap_stage)" class="px-3 py-1 text-xs font-semibold rounded-full whitespace-nowrap flex-shrink-0" x-text="'CAPSTONE ' + group.cap_stage"></span>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="p-5">
                    <!-- Average Grade Display -->
                    <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-100">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Average Grade</p>
                            <div class="flex items-baseline mt-1">
                                <span class="text-2xl font-bold" :class="getGradeColor(group.average_grade)" 
                                      x-text="group.average_grade !== null ? group.average_grade.toFixed(1) : '-'"></span>
                                <span class="text-gray-400 text-sm ml-1">/ 100</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <template x-if="group.is_complete">
                                <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Complete
                                </span>
                            </template>
                            <template x-if="!group.is_complete">
                                <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-700">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                    Pending
                                </span>
                            </template>
                            <p class="text-xs text-gray-500 mt-1" x-text="group.graded_count + '/' + group.panelists.length + ' graded'"></p>
                        </div>
                    </div>

                    <!-- Panelists with Grades -->
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-3">Panelists & Grades</p>
                        <div class="space-y-2.5">
                            <template x-for="pm in group.panelists" :key="pm.id">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center min-w-0 flex-1">
                                        <span class="w-1.5 h-1.5 rounded-full mr-2 flex-shrink-0" 
                                              :class="pm.role === 'Adviser' ? 'bg-purple-500' : pm.role === 'Chair' ? 'bg-emerald-500' : 'bg-orange-500'"></span>
                                        <span class="text-sm text-gray-700 truncate" x-text="pm.email || 'Unknown'"></span>
                                        <span class="ml-2 text-xs px-2 py-0.5 rounded-full flex-shrink-0"
                                              :class="pm.role === 'Adviser' ? 'bg-purple-100 text-purple-700' : pm.role === 'Chair' ? 'bg-emerald-100 text-emerald-700' : 'bg-orange-100 text-orange-700'"
                                              x-text="pm.role || 'Panel'"></span>
                                    </div>
                                    <span class="text-xs font-semibold px-2 py-1 rounded-lg ml-2 flex-shrink-0"
                                          :class="pm.grade !== null ? getGradeBadgeClass(pm.grade) : 'bg-gray-100 text-gray-500'"
                                          x-text="pm.grade !== null ? parseFloat(pm.grade).toFixed(1) : 'N/A'"></span>
                                </div>
                            </template>
                            <template x-if="!group.panelists?.length">
                                <p class="text-gray-400 text-sm italic">No panelists assigned</p>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Card Footer -->
                <div class="px-5 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end">
                    <div class="flex items-center space-x-2">
                        <a :href="'/groups/' + group.group_id" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="View Group">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </a>
                        <a :href="'/evaluations/group/' + group.group_id + '/edit?cap_stage=' + group.cap_stage" 
                           class="p-2 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" 
                           title="Edit Grades">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>
                        <button type="button" @click="confirmDeleteGroup(group)" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete All Evaluations" x-show="group.graded_count > 0">
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
    <div x-show="groupedEvaluations.length === 0 && !loading" class="text-center py-16 animate-fade-in">
        <div class="w-24 h-24 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-6">
            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No evaluations found</h3>
        <p class="text-gray-500 mb-6">Start by creating your first evaluation</p>
        <a href="{{ route('evaluations.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-500 to-purple-600 text-white font-medium rounded-xl shadow-lg shadow-purple-500/30 hover:shadow-xl transition-all duration-300">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            New Evaluation
        </a>
    </div>

    <!-- Pagination -->
    <div x-show="groupedEvaluations.length > 0" class="mt-6 flex items-center justify-between">
        <div class="text-sm text-gray-500">
            Showing <span class="font-medium" x-text="((currentPage - 1) * perPage) + 1"></span> to 
            <span class="font-medium" x-text="Math.min(currentPage * perPage, groupedEvaluations.length)"></span> of 
            <span class="font-medium" x-text="groupedEvaluations.length"></span> results
        </div>
        <div class="flex items-center space-x-2">
            <button @click="prevPage()" :disabled="currentPage === 1" 
                    :class="currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'"
                    class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg transition-colors">
                Previous
            </button>
            <template x-for="page in totalPages" :key="page">
                <button @click="goToPage(page)" 
                        :class="page === currentPage ? 'bg-purple-500 text-white border-purple-500' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'"
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
    <div x-show="showDeleteModal" 
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showDeleteModal = false"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 animate-scale-in">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto bg-red-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Delete All Evaluations?</h3>
                    <p class="text-gray-500 mb-6">This will delete all evaluations for this group's current CAPSTONE stage. This action cannot be undone.</p>
                    <div class="flex space-x-3">
                        <button type="button" @click="showDeleteModal = false" class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button type="button" @click="deleteEvaluation()" class="flex-1 px-4 py-2.5 bg-red-600 text-white font-medium rounded-xl hover:bg-red-700 transition-colors">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function evaluationsData() {
    return {
        evaluations: [],
        rawData: [],
        groupedEvaluations: [],
        searchQuery: '',
        capFilter: '',
        loading: true,
        showDeleteModal: false,
        groupToDelete: null,
        averageGrade: 0,
        cap1Count: 0,
        cap2Count: 0,
        // Pagination
        currentPage: 1,
        perPage: 10,

        get paginatedEvaluations() {
            const start = (this.currentPage - 1) * this.perPage;
            return this.groupedEvaluations.slice(start, start + this.perPage);
        },

        get totalPages() {
            return Math.ceil(this.groupedEvaluations.length / this.perPage);
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
            // Reset modal state on page load (handles browser back/forward cache)
            this.showDeleteModal = false;
            this.groupToDelete = null;
            
            // Also listen for pageshow event to reset state when using browser back button
            window.addEventListener('pageshow', (event) => {
                if (event.persisted) {
                    this.showDeleteModal = false;
                    this.groupToDelete = null;
                }
            });
            
            this.loadEvaluations();
        },

        async loadEvaluations() {
            try {
                const response = await fetch('/api/evaluations');
                const data = await response.json();
                
                // New API returns groups with their evaluations
                this.rawData = data;
                this.groupEvaluations();
                this.calculateStats();
            } catch (error) {
                console.error('Error loading evaluations:', error);
            } finally {
                this.loading = false;
            }
        },

        groupEvaluations() {
            // Data is now already grouped by group from the API
            let result = [];
            
            this.rawData.forEach(item => {
                const group = item.group;
                const evaluations = item.evaluations || [];
                const panelMembers = group.panel_members || [];
                
                // Separate evaluations by cap_stage
                const cap1Evals = evaluations.filter(e => parseInt(e.cap_stage) === 1);
                const cap2Evals = evaluations.filter(e => parseInt(e.cap_stage) === 2);
                
                // Check if CAPSTONE 1 is complete (all panelists have graded)
                const cap1Complete = panelMembers.length > 0 && cap1Evals.length >= panelMembers.length;
                
                // Determine which cap stage to display
                let displayCapStage = 1;
                if (cap1Complete) {
                    displayCapStage = 2; // Show CAPSTONE 2 if CAPSTONE 1 is complete
                }
                
                const currentEvals = displayCapStage === 1 ? cap1Evals : cap2Evals;
                
                // Build panelists array with their grades for the current stage
                const panelists = panelMembers.map(pm => {
                    const evalForPanelist = currentEvals.find(e => e.panel_member_id === pm.id);
                    return {
                        id: pm.id,
                        email: pm.email,
                        role: pm.pivot?.role || 'Panel',
                        grade: evalForPanelist ? parseFloat(evalForPanelist.grade) : null,
                        evaluation_id: evalForPanelist?.id || null
                    };
                });
                
                // Calculate average grade
                const gradedPanelists = panelists.filter(p => p.grade !== null);
                const averageGrade = gradedPanelists.length > 0 
                    ? gradedPanelists.reduce((sum, p) => sum + p.grade, 0) / gradedPanelists.length 
                    : null;
                
                result.push({
                    group_id: group.id,
                    project_title: group.project_title,
                    students_count: group.students_count || 0,
                    cap_stage: displayCapStage,
                    panelists: panelists,
                    average_grade: averageGrade,
                    graded_count: gradedPanelists.length,
                    is_complete: panelists.length > 0 && gradedPanelists.length >= panelists.length,
                    evaluation_ids: currentEvals.map(e => e.id),
                    all_evaluations: evaluations
                });
            });
            
            // Store all evaluations for stats calculation
            this.evaluations = this.rawData.flatMap(item => item.evaluations || []);
            
            // Apply filters
            if (this.searchQuery) {
                const query = this.searchQuery.toLowerCase();
                result = result.filter(g => 
                    g.group_id.toString().includes(query) ||
                    (g.project_title || '').toLowerCase().includes(query) ||
                    g.panelists.some(p => (p.email || '').toLowerCase().includes(query))
                );
            }
            
            if (this.capFilter) {
                result = result.filter(g => g.cap_stage == this.capFilter);
            }
            
            // Sort by group_id
            result.sort((a, b) => a.group_id - b.group_id);
            
            this.groupedEvaluations = result;
        },

        filter() {
            this.groupEvaluations();
            this.currentPage = 1;
        },

        calculateStats() {
            if (this.evaluations.length > 0) {
                const validGrades = this.evaluations.filter(e => e.grade !== null && e.grade !== undefined);
                if (validGrades.length > 0) {
                    const total = validGrades.reduce((sum, e) => sum + parseFloat(e.grade), 0);
                    this.averageGrade = total / validGrades.length;
                }
            }
            // Count unique groups per cap stage
            const cap1Groups = new Set(this.evaluations.filter(e => e.cap_stage == 1).map(e => e.group_id));
            const cap2Groups = new Set(this.evaluations.filter(e => e.cap_stage == 2).map(e => e.group_id));
            this.cap1Count = cap1Groups.size;
            this.cap2Count = cap2Groups.size;
        },

        getGradeColor(grade) {
            const g = parseFloat(grade);
            if (isNaN(g)) return 'text-gray-400';
            if (g >= 90) return 'text-emerald-600';
            if (g >= 80) return 'text-blue-600';
            if (g >= 70) return 'text-yellow-600';
            return 'text-red-600';
        },

        getGradeBadgeClass(grade) {
            const g = parseFloat(grade);
            if (isNaN(g)) return 'bg-gray-100 text-gray-500';
            if (g >= 90) return 'bg-emerald-100 text-emerald-700';
            if (g >= 80) return 'bg-blue-100 text-blue-700';
            if (g >= 70) return 'bg-yellow-100 text-yellow-700';
            return 'bg-red-100 text-red-700';
        },

        getCapClass(capStage) {
            const stage = parseInt(capStage) || capStage;
            if (stage == 1) return 'bg-blue-100 text-blue-700';
            if (stage == 2) return 'bg-emerald-100 text-emerald-700';
            return 'bg-gray-100 text-gray-700';
        },

        formatDate(dateString) {
            if (!dateString) return '-';
            return new Date(dateString).toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric' 
            });
        },

        confirmDeleteGroup(group) {
            this.groupToDelete = group;
            this.showDeleteModal = true;
        },

        async deleteEvaluation() {
            if (!this.groupToDelete) return;
            
            try {
                // Delete all evaluations for this group and cap stage
                const deletePromises = this.groupToDelete.evaluation_ids.map(id => 
                    fetch('/api/evaluations/' + id, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                );

                await Promise.all(deletePromises);
                
                // Remove deleted evaluations from rawData
                this.rawData = this.rawData.map(item => {
                    if (item.group_id === this.groupToDelete.group_id) {
                        item.evaluations = item.evaluations.filter(e => 
                            !this.groupToDelete.evaluation_ids.includes(e.id)
                        );
                    }
                    return item;
                });
                this.groupEvaluations();
                this.calculateStats();
            } catch (error) {
                console.error('Error deleting evaluations:', error);
            } finally {
                this.showDeleteModal = false;
                this.groupToDelete = null;
            }
        }
    }
}
</script>
@endsection
