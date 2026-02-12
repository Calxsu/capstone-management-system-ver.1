@extends('layouts.dashboard')

@section('title', 'Edit Evaluation')
@section('subtitle', 'Update evaluation record')

@section('content')
<div x-data="editEvaluationForm()">
    <!-- Header -->
    <div class="mb-6 animate-slide-up">
        <a href="{{ route('evaluations.show', $evaluation) }}" class="inline-flex items-center text-amber-600 hover:text-amber-700 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Evaluation Details
        </a>
    </div>

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

    <!-- Form Card -->
    <div class="bg-white rounded-xl border border-gray-100 p-8 animate-slide-up">
        
        <!-- Current Info Display -->
        <div class="mb-8 p-5 bg-gray-50 rounded-xl">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Evaluation For</h3>
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl flex items-center justify-center text-white font-bold mr-4">
                        #{{ $evaluation->group_id }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">{{ $evaluation->group->project_title ?? 'Untitled Project' }}</p>
                        <p class="text-gray-500 text-sm">CAPSTONE {{ $evaluation->group->cap_stage }}</p>
                    </div>
                </div>
                <div class="text-right">
                    @if($evaluation->panelMember)
                        <p class="font-medium text-gray-900">{{ $evaluation->panelMember->email }}</p>
                        @php
                            $role = $evaluation->group->panelMembers->where('id', $evaluation->panel_member_id)->first()?->pivot?->role;
                        @endphp
                        @if($role)
                            <span class="inline-block mt-1 px-3 py-1 text-xs font-semibold rounded-full 
                                {{ $role === 'Adviser' ? 'bg-purple-100 text-purple-700' : 
                                   ($role === 'Chair' ? 'bg-emerald-100 text-emerald-700' : 'bg-orange-100 text-orange-700') }}">
                                {{ $role }}
                            </span>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <form @submit.prevent="submitForm">
            <div class="space-y-6">
                
                <!-- CAPSTONE Stage (Read Only) -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">CAPSTONE Stage</label>
                    <div class="flex space-x-4">
                        @foreach([1, 2, 3] as $stage)
                            <div class="relative">
                                <input type="radio" name="cap_stage" value="{{ $stage }}" 
                                       id="cap_stage_{{ $stage }}"
                                       {{ $evaluation->cap_stage == $stage ? 'checked' : '' }}
                                       disabled
                                       class="peer hidden">
                                <label for="cap_stage_{{ $stage }}" 
                                       class="flex items-center justify-center px-6 py-3 rounded-xl cursor-not-allowed
                                              {{ $evaluation->cap_stage == $stage 
                                                  ? 'bg-gradient-to-r from-amber-500 to-orange-500 text-white shadow-lg' 
                                                  : 'bg-gray-100 text-gray-400' }}">
                                    <span class="font-semibold">CAPSTONE {{ $stage }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <p class="text-sm text-gray-500 mt-2">Stage cannot be changed. Create a new evaluation for a different stage.</p>
                </div>

                <!-- Grade -->
                <div>
                    <label for="grade" class="block text-sm font-semibold text-gray-700 mb-3">Grade</label>
                    <div class="relative">
                        <input type="number" 
                               id="grade"
                               x-model="form.grade"
                               step="0.01" 
                               min="0" 
                               max="100" 
                               class="w-full px-4 py-3 rounded-xl border focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all"
                               :class="errors.grade ? 'border-red-500' : 'border-gray-200'"
                               placeholder="Enter grade (0-100)"
                               required>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <span class="text-gray-400 font-medium">/ 100</span>
                        </div>
                    </div>
                    <p x-show="errors.grade" class="mt-2 text-sm text-red-600 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        <span x-text="errors.grade"></span>
                    </p>
                </div>

                <!-- Date -->
                <div>
                    <label for="evaluation_date" class="block text-sm font-semibold text-gray-700 mb-3">Evaluation Date</label>
                    <input type="date" 
                           id="evaluation_date"
                           x-model="form.evaluation_date"
                           class="w-full px-4 py-3 rounded-xl border focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all"
                           :class="errors.evaluation_date ? 'border-red-500' : 'border-gray-200'"
                           required>
                    <p x-show="errors.evaluation_date" class="mt-2 text-sm text-red-600 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        <span x-text="errors.evaluation_date"></span>
                    </p>
                </div>

                <!-- Remarks -->
                <div>
                    <label for="remarks" class="block text-sm font-semibold text-gray-700 mb-3">Remarks (Optional)</label>
                    <textarea id="remarks"
                              x-model="form.remarks"
                              rows="4"
                              class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all resize-none"
                              placeholder="Additional notes or comments about this evaluation..."></textarea>
                </div>

            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex items-center justify-between pt-6 border-t border-gray-100">
                <a href="{{ route('evaluations.show', $evaluation) }}" 
                   class="px-6 py-3 text-gray-600 hover:text-gray-900 font-medium transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        :disabled="loading"
                        class="px-8 py-3 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-semibold rounded-xl shadow-lg shadow-amber-500/30 transition-all hover:shadow-xl hover:shadow-amber-500/40 transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span class="flex items-center">
                        <template x-if="loading">
                            <svg class="w-5 h-5 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </template>
                        <template x-if="!loading">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </template>
                        <span x-text="loading ? 'Updating...' : 'Update Evaluation'"></span>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function editEvaluationForm() {
    return {
        form: {
            grade: '{{ $evaluation->grade }}',
            evaluation_date: '{{ $evaluation->date?->format("Y-m-d") ?? "" }}',
            remarks: `{{ $evaluation->remarks ?? '' }}`
        },
        errors: {},
        loading: false,
        successMessage: '',

        async submitForm() {
            this.loading = true;
            this.errors = {};
            this.successMessage = '';

            try {
                const response = await fetch('/api/evaluations/{{ $evaluation->id }}', {
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
                    this.successMessage = 'Evaluation updated successfully!';
                    setTimeout(() => {
                        window.location.href = '{{ route("evaluations.show", $evaluation) }}';
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
