@extends('layouts.dashboard')

@section('title', 'Evaluation Details')
@section('subtitle', 'View evaluation record')

@section('content')
<div>
    <!-- Header -->
    <div class="mb-6 animate-slide-up">
        <div class="flex items-center justify-between">
            <a href="{{ route('evaluations.index') }}" class="inline-flex items-center text-amber-600 hover:text-amber-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Evaluations
            </a>
            <a href="{{ route('evaluations.edit', $evaluation) }}" 
               class="inline-flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-medium rounded-xl transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
        </div>
    </div>

    <!-- Evaluation Card -->
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden animate-slide-up">
        
        <!-- Grade Header -->
        <div class="p-6 bg-gradient-to-r from-amber-500 to-orange-500 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-amber-100 text-sm font-medium">Evaluation Grade</p>
                    <p class="text-5xl font-bold mt-1">{{ number_format($evaluation->grade, 2) }}</p>
                </div>
                <div class="text-right">
                    <span class="inline-block px-4 py-2 bg-white/20 rounded-xl text-sm font-semibold backdrop-blur-sm">
                        CAPSTONE {{ $evaluation->cap_stage }}
                    </span>
                    <p class="text-amber-100 text-sm mt-2">{{ $evaluation->date?->format('F d, Y') ?? 'No date' }}</p>
                </div>
            </div>
        </div>

        <!-- Evaluation Info -->
        <div class="p-6 space-y-6">
            
            <!-- Group Information -->
            <div class="bg-gray-50 rounded-xl p-5">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Group Information</h3>
                <div class="flex items-center">
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl flex items-center justify-center text-white font-bold text-xl mr-4">
                        #{{ $evaluation->group_id }}
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-900 text-lg">{{ $evaluation->group->project_title ?? 'Untitled Project' }}</p>
                        <p class="text-gray-500 text-sm">
                            {{ $evaluation->group->students->count() }} students • 
                            CAPSTONE {{ $evaluation->group->cap_stage }}
                        </p>
                    </div>
                    <a href="{{ route('groups.show', $evaluation->group) }}" 
                       class="px-4 py-2 text-blue-600 hover:bg-blue-50 rounded-xl transition-colors text-sm font-medium">
                        View Group →
                    </a>
                </div>
            </div>

            <!-- Panel Member Information -->
            <div class="bg-gray-50 rounded-xl p-5">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Evaluated By</h3>
                @if($evaluation->panelMember)
                    <div class="flex items-center">
                        <div class="w-14 h-14 bg-gradient-to-br from-purple-400 to-purple-600 rounded-xl flex items-center justify-center text-white font-bold text-xl mr-4">
                            {{ strtoupper(substr($evaluation->panelMember->email, 0, 1)) }}
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900 text-lg">{{ $evaluation->panelMember->email }}</p>
                            <p class="text-gray-500 text-sm">Faculty</p>
                            @php
                                $role = $evaluation->group->panelMembers->where('id', $evaluation->panel_member_id)->first()?->pivot?->role;
                            @endphp
                            @if($role)
                                <span class="inline-block mt-2 px-3 py-1 text-xs font-semibold rounded-full 
                                    {{ $role === 'Adviser' ? 'bg-purple-100 text-purple-700' : 
                                       ($role === 'Chair' ? 'bg-emerald-100 text-emerald-700' : 'bg-orange-100 text-orange-700') }}">
                                    {{ $role }}
                                </span>
                            @endif
                        </div>
                    </div>
                @else
                    <p class="text-gray-500 italic">Panel member information not available</p>
                @endif
            </div>

            <!-- Remarks -->
            @if($evaluation->remarks)
            <div class="bg-gray-50 rounded-xl p-5">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Remarks</h3>
                <p class="text-gray-700 leading-relaxed">{{ $evaluation->remarks }}</p>
            </div>
            @endif

            <!-- Meta Information -->
            <div class="border-t border-gray-100 pt-5">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Created</p>
                        <p class="text-gray-900 font-medium">{{ $evaluation->created_at->format('M d, Y g:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Last Updated</p>
                        <p class="text-gray-900 font-medium">{{ $evaluation->updated_at->format('M d, Y g:i A') }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Delete Action -->
    <div class="mt-6 flex justify-end" x-data="{ showConfirm: false, deleting: false }">
        <button type="button" @click="showConfirm = true" 
                class="inline-flex items-center px-4 py-2 text-red-600 hover:bg-red-50 rounded-xl transition-colors text-sm font-medium">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            Delete Evaluation
        </button>

        <!-- Delete Confirmation Modal -->
        <div x-show="showConfirm" 
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center z-50"
             @click.self="showConfirm = false">
            <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4 shadow-2xl" 
                 x-show="showConfirm"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">
                <div class="text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Delete Evaluation?</h3>
                    <p class="text-gray-600 mb-6">This action cannot be undone. This will permanently delete this evaluation record.</p>
                    <div class="flex space-x-3 justify-center">
                        <button type="button" @click="showConfirm = false" 
                                :disabled="deleting"
                                class="px-6 py-2 border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors font-medium disabled:opacity-50">
                            Cancel
                        </button>
                        <button type="button" 
                                @click="async () => {
                                    deleting = true;
                                    try {
                                        const response = await fetch('/api/evaluations/{{ $evaluation->id }}', {
                                            method: 'DELETE',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').content,
                                                'Accept': 'application/json'
                                            }
                                        });
                                        if (response.ok) {
                                            window.location.href = '{{ route('evaluations.index') }}';
                                        } else {
                                            alert('Failed to delete evaluation');
                                            deleting = false;
                                        }
                                    } catch (error) {
                                        alert('Network error. Please try again.');
                                        deleting = false;
                                    }
                                }"
                                :disabled="deleting"
                                class="px-6 py-2 bg-red-500 text-white rounded-xl hover:bg-red-600 transition-colors font-medium disabled:opacity-50">
                            <span x-show="!deleting">Delete</span>
                            <span x-show="deleting" class="flex items-center">
                                <svg class="w-4 h-4 mr-1 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Deleting...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
