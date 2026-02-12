@extends('layouts.dashboard')

@section('title', 'Edit Grades')
@section('subtitle', 'Manage panel member grades for this group')

@section('content')
<div class="max-w-4xl mx-auto" x-data="editGroupGrades()">
    <!-- Back Button -->
    <a href="{{ route('evaluations.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 mb-6 animate-fade-in">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to Evaluations
    </a>

    <!-- Success Message -->
    <div x-show="successMessage" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center text-green-800">
        <svg class="w-5 h-5 mr-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
        </svg>
        <span x-text="successMessage"></span>
    </div>

    <!-- Group Info Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6 animate-slide-up">
        <div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center text-white font-bold text-lg mr-4">
                        #{{ $group->id }}
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $group->project_title ?? 'Untitled Project' }}</h2>
                        <p class="text-gray-500">{{ $group->students->count() }} students • {{ $group->panelMembers->count() }} panelists</p>
                    </div>
                </div>
                <span class="px-4 py-2 text-sm font-semibold rounded-full {{ $capStage == 1 ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700' }}">
                    CAPSTONE {{ $capStage }}
                </span>
            </div>
        </div>

        <!-- Students List -->
        <div class="p-4 bg-gray-50 border-b border-gray-100">
            <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Group Members</p>
            <div class="flex flex-wrap gap-2">
                @foreach($group->students as $student)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-white border border-gray-200">
                        <span class="w-2 h-2 bg-emerald-400 rounded-full mr-2"></span>
                        {{ $student->name }}
                    </span>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Grades Form -->
    <form @submit.prevent="submitForm" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up" style="animation-delay: 0.1s">

        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-amber-50 to-orange-50">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Panel Member Grades</h3>
                    <p class="text-sm text-gray-500">Enter grades for each panel member (0-100)</p>
                </div>
            </div>
        </div>

        <div class="p-6 space-y-6">
            <!-- Evaluation Date -->
            <div>
                <label for="date" class="block text-sm font-semibold text-gray-700 mb-2">Evaluation Date</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <input type="date" id="date" 
                           x-model="form.date"
                           class="w-full pl-12 pr-4 py-3 rounded-xl border focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all"
                           :class="errors.date ? 'border-red-500' : 'border-gray-200'"
                           required>
                </div>
                <p x-show="errors.date" class="mt-2 text-sm text-red-600" x-text="errors.date"></p>
            </div>

            <!-- Panel Members Grades -->
            <div class="space-y-4">
                <label class="block text-sm font-semibold text-gray-700">Grades by Panel Member</label>
                
                @foreach($group->panelMembers as $panelMember)
                    @php
                        $existingEval = $evaluations->get($panelMember->id);
                        $role = $panelMember->pivot->role ?? 'Panel';
                    @endphp
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 hover:border-gray-200 transition-colors">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-center flex-1">
                                <div class="w-10 h-10 bg-gradient-to-br from-purple-400 to-purple-600 rounded-lg flex items-center justify-center text-white font-medium mr-3">
                                    {{ strtoupper(substr($panelMember->email, 0, 1)) }}
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">{{ $panelMember->email }}</p>
                                    <span class="inline-block mt-1 px-2 py-0.5 text-xs font-semibold rounded-full 
                                        {{ $role === 'Adviser' ? 'bg-purple-100 text-purple-700' : 
                                           ($role === 'Chair' ? 'bg-emerald-100 text-emerald-700' : 'bg-orange-100 text-orange-700') }}">
                                        {{ $role }}
                                    </span>
                                </div>
                            </div>
                            <div class="w-32">
                                <input type="number" 
                                       x-model="form.grades['{{ $panelMember->id }}']"
                                       @input="calculateAverage()"
                                       min="0" max="100" step="0.01"
                                       placeholder="Grade"
                                       class="w-full px-4 py-2.5 text-center text-lg font-bold rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all">
                            </div>
                        </div>
                        <!-- Remarks -->
                        <div class="mt-3">
                            <textarea x-model="form.remarks['{{ $panelMember->id }}']"
                                      rows="2" 
                                      placeholder="Remarks (optional)"
                                      class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all resize-none"></textarea>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Grade Summary -->
            <div class="p-4 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-xl border border-emerald-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-500 uppercase">Grade Summary</p>
                        <p class="text-xs text-gray-400 mt-1">Automatically calculated from panel member grades</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Average Grade</p>
                        <p class="text-3xl font-bold text-emerald-600" x-text="averageGrade">-</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end space-x-3">
            <a href="{{ route('evaluations.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-white transition-colors">
                Cancel
            </a>
            <button type="submit" 
                    :disabled="loading"
                    class="px-6 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-medium rounded-xl shadow-lg shadow-amber-500/30 hover:shadow-xl transition-all duration-300 hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed">
                <span class="flex items-center">
                    <template x-if="loading">
                        <svg class="w-5 h-5 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </template>
                    <span x-text="loading ? 'Saving...' : 'Save Grades'"></span>
                </span>
            </button>
        </div>
    </form>
</div>

<script>
function editGroupGrades() {
    return {
        form: {
            cap_stage: {{ $capStage }},
            date: '{{ $evaluations->first()?->date?->format("Y-m-d") ?? date("Y-m-d") }}',
            grades: {
                @foreach($group->panelMembers as $panelMember)
                    '{{ $panelMember->id }}': '{{ $evaluations->get($panelMember->id)?->grade ?? '' }}',
                @endforeach
            },
            remarks: {
                @foreach($group->panelMembers as $panelMember)
                    '{{ $panelMember->id }}': `{{ $evaluations->get($panelMember->id)?->remarks ?? '' }}`,
                @endforeach
            }
        },
        errors: {},
        loading: false,
        successMessage: '',
        averageGrade: '-',

        init() {
            this.calculateAverage();
        },

        calculateAverage() {
            let total = 0;
            let count = 0;
            
            Object.values(this.form.grades).forEach(grade => {
                const value = parseFloat(grade);
                if (!isNaN(value) && value >= 0 && value <= 100) {
                    total += value;
                    count++;
                }
            });
            
            if (count > 0) {
                this.averageGrade = (total / count).toFixed(1);
            } else {
                this.averageGrade = '-';
            }
        },

        async submitForm() {
            this.loading = true;
            this.errors = {};
            this.successMessage = '';

            try {
                const response = await fetch('/api/evaluations/group/{{ $group->id }}', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.form)
                });

                const data = await response.json();

                if (!response.ok) {
                    if (data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            this.errors[key] = data.errors[key][0];
                        });
                    } else {
                        alert(data.message || 'An error occurred');
                    }
                } else {
                    this.successMessage = 'Grades saved successfully!';
                    setTimeout(() => {
                        window.location.href = '{{ route("evaluations.index") }}';
                    }, 1000);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Network error. Please try again.');
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endsection
