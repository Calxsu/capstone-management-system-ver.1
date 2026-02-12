@extends('layouts.dashboard')

@section('title', 'Group Checklist')
@section('subtitle', 'CAPSTONE 2 completion checklist for Group #' . $group->id)

@section('content')
<div x-data="groupChecklistData()">
    <!-- Header -->
    <div class="mb-6 animate-slide-up">
        <a href="{{ route('checklists.index') }}" class="inline-flex items-center text-emerald-600 hover:text-emerald-700 mb-4 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Checklists
        </a>
        
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-14 h-14 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-xl flex items-center justify-center text-white font-bold text-xl mr-4">
                    #{{ $group->id }}
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $group->project_title ?? 'Untitled Project' }}</h1>
                    <p class="text-gray-500">{{ $group->students->count() }} students • CAPSTONE {{ $group->cap_stage }}</p>
                </div>
            </div>
            <a href="{{ route('groups.show', $group) }}" class="px-4 py-2 text-emerald-600 hover:bg-emerald-50 rounded-xl transition-colors font-medium">
                View Group Details →
            </a>
        </div>
    </div>

    <!-- Progress Card -->
    <div class="bg-white rounded-xl border border-gray-100 p-6 mb-6 animate-slide-up" style="animation-delay: 0.1s">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Completion Progress</h2>
                <p class="text-gray-500 text-sm">Track your CAPSTONE 2 requirements</p>
            </div>
            <div class="text-right">
                <p class="text-3xl font-bold" 
                   :class="progress.percentage >= 100 ? 'text-emerald-600' : 'text-blue-600'"
                   x-text="progress.percentage + '%'">0%</p>
                <p class="text-sm text-gray-500" x-text="progress.required_completed + ' of ' + progress.required_total + ' required items'"></p>
            </div>
        </div>
        
        <div class="w-full bg-gray-200 rounded-full h-4">
            <div class="h-4 rounded-full transition-all duration-500"
                 :class="progress.percentage >= 100 ? 'bg-gradient-to-r from-emerald-400 to-emerald-600' : 'bg-gradient-to-r from-blue-400 to-blue-600'"
                 :style="'width: ' + progress.percentage + '%'"></div>
        </div>
        
        <div class="flex justify-between mt-2 text-sm text-gray-500">
            <span x-text="progress.completed + ' of ' + progress.total + ' total items completed'"></span>
            <span x-show="progress.percentage >= 100" class="text-emerald-600 font-medium flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                All requirements complete!
            </span>
        </div>
    </div>

    <!-- Loading State -->
    <div x-show="loading" class="text-center py-16">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-emerald-500 mx-auto"></div>
        <p class="mt-4 text-gray-500">Loading checklist...</p>
    </div>

    <!-- Checklist Items -->
    <div x-show="!loading" class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="divide-y divide-gray-100">
            <template x-for="item in items" :key="item.id">
                <div class="p-5 hover:bg-gray-50 transition-colors"
                     :class="item.is_completed ? 'bg-emerald-50/50' : ''">
                    <div class="flex items-start">
                        <!-- Checkbox -->
                        <button @click="toggleItem(item)" 
                                class="flex-shrink-0 w-7 h-7 rounded-lg border-2 flex items-center justify-center mr-4 transition-all"
                                :class="item.is_completed ? 'bg-emerald-500 border-emerald-500 text-white' : 'border-gray-300 hover:border-emerald-400'">
                            <svg x-show="item.is_completed" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </button>
                        
                        <div class="flex-1">
                            <div class="flex items-center mb-1">
                                <p class="font-medium" 
                                   :class="item.is_completed ? 'text-emerald-800' : 'text-gray-900'"
                                   x-text="item.name"></p>
                                <span x-show="item.is_required" class="ml-2 px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-700">Required</span>
                            </div>
                            <p class="text-sm text-gray-500" x-text="item.description || ''"></p>
                            
                            <!-- Completion Details -->
                            <div x-show="item.is_completed" class="mt-2 text-xs text-emerald-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Completed</span>
                                <span x-show="item.completed_at" class="ml-1" x-text="item.completed_at ? ' on ' + formatDate(item.completed_at) : ''"></span>
                                <span x-show="item.completed_by" class="ml-1" x-text="item.completed_by ? ' by ' + item.completed_by : ''"></span>
                            </div>

                            <!-- Notes Section -->
                            <div class="mt-3" x-show="item.showNotes || item.notes">
                                <textarea x-model="item.tempNotes" 
                                          @blur="saveNotes(item)"
                                          class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 resize-none"
                                          rows="2"
                                          placeholder="Add notes..."></textarea>
                            </div>
                        </div>

                        <!-- Notes Toggle -->
                        <button @click="item.showNotes = !item.showNotes" 
                                class="ml-3 p-2 text-gray-400 hover:text-gray-600 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Empty State -->
    <div x-show="!loading && items.length === 0" class="text-center py-16 bg-white rounded-xl border border-gray-100">
        <div class="w-20 h-20 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No checklist items configured</h3>
        <p class="text-gray-500 mb-6">Configure checklist items in the admin section to track completion requirements.</p>
        <a href="{{ route('checklists.items') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-medium rounded-xl">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            Configure Checklist Items
        </a>
    </div>
</div>

<script>
function groupChecklistData() {
    return {
        items: [],
        progress: {
            total: 0,
            completed: 0,
            required_total: 0,
            required_completed: 0,
            percentage: 0
        },
        loading: true,

        init() {
            this.loadChecklist();
        },

        async loadChecklist() {
            try {
                const response = await fetch('/api/groups/{{ $group->id }}/checklist');
                const data = await response.json();
                
                this.items = data.items.map(item => ({
                    ...item,
                    showNotes: false,
                    tempNotes: item.notes || ''
                }));
                this.progress = data.progress;
            } catch (error) {
                console.error('Error loading checklist:', error);
            } finally {
                this.loading = false;
            }
        },

        async toggleItem(item) {
            const newStatus = !item.is_completed;
            
            try {
                const response = await fetch('/api/groups/{{ $group->id }}/checklist/' + item.id, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        is_completed: newStatus,
                        notes: item.tempNotes || null
                    })
                });

                if (response.ok) {
                    const data = await response.json();
                    item.is_completed = newStatus;
                    item.completed_at = newStatus ? new Date().toISOString() : null;
                    item.completed_by = newStatus ? 'You' : null;
                    
                    // Recalculate progress
                    this.updateProgress();
                }
            } catch (error) {
                console.error('Error toggling item:', error);
            }
        },

        async saveNotes(item) {
            if (item.tempNotes === item.notes) return;

            try {
                await fetch('/api/groups/{{ $group->id }}/checklist/' + item.id, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        is_completed: item.is_completed,
                        notes: item.tempNotes || null
                    })
                });
                
                item.notes = item.tempNotes;
            } catch (error) {
                console.error('Error saving notes:', error);
            }
        },

        updateProgress() {
            const completed = this.items.filter(i => i.is_completed).length;
            const requiredCompleted = this.items.filter(i => i.is_required && i.is_completed).length;
            const requiredTotal = this.items.filter(i => i.is_required).length;
            
            this.progress.completed = completed;
            this.progress.total = this.items.length;
            this.progress.required_completed = requiredCompleted;
            this.progress.required_total = requiredTotal;
            this.progress.percentage = requiredTotal > 0 ? Math.round((requiredCompleted / requiredTotal) * 100) : 100;
        },

        formatDate(dateString) {
            if (!dateString) return '';
            return new Date(dateString).toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric' 
            });
        }
    }
}
</script>
@endsection
