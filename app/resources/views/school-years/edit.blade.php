@extends('layouts.dashboard')

@section('title', 'Edit School Year')
@section('subtitle', 'Update school year information')

@section('content')
<div class="max-w-4xl mx-auto animate-fade-in" x-data="editSchoolYearForm()">
    <!-- Back Button -->
    <div class="mb-8">
        <a href="{{ route('school-years.index') }}" 
           class="inline-flex items-center text-gray-500 hover:text-gray-700 transition-colors group">
            <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to School Years
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
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden hover-lift">
        <!-- Header with gradient -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-8 py-6">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white">Edit School Year</h1>
                    <p class="text-blue-100">Updating: {{ $schoolYear->year }}</p>
                </div>
            </div>
        </div>

        <form @submit.prevent="submitForm" class="p-8">
            <!-- Academic Year Field -->
            <div class="mb-6">
                <label for="year" class="block text-sm font-semibold text-gray-700 mb-2">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Academic Year (A.Y.)
                    </span>
                </label>
                <input type="text"
                       id="year"
                       x-model="form.year"
                       class="w-full px-4 py-3 border-2 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-lg"
                       :class="errors.year ? 'border-red-500' : 'border-gray-200'"
                       placeholder="e.g., 2024-2025"
                       required>
                <p x-show="errors.year" class="mt-2 text-sm text-red-600 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    <span x-text="errors.year"></span>
                </p>
                <p class="mt-2 text-sm text-gray-500">Enter the academic year in format: YYYY-YYYY (e.g., 2024-2025 for A.Y. 2024-2025)</p>
            </div>

            <!-- Semester Selection -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        Semester
                        <span class="text-gray-400 font-normal ml-1">- First or Second semester of the academic year</span>
                    </span>
                </label>
                <div class="grid grid-cols-2 gap-4">
                    <label class="relative cursor-pointer group">
                        <input type="radio" x-model="form.semester" value="1" class="peer hidden">
                        <div class="p-4 rounded-xl border-2 border-gray-200 text-center transition-all duration-300
                                    peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:shadow-lg
                                    hover:border-blue-300 hover:bg-blue-50/50 group-hover:scale-[1.02]">
                            <div class="w-12 h-12 mx-auto bg-blue-100 rounded-full flex items-center justify-center mb-2">
                                <span class="text-2xl font-bold text-blue-600">1</span>
                            </div>
                            <span class="font-medium text-gray-700">1st Semester</span>
                            <p class="text-xs text-gray-400 mt-1">First half of A.Y.</p>
                        </div>
                    </label>

                    <label class="relative cursor-pointer group">
                        <input type="radio" x-model="form.semester" value="2" class="peer hidden">
                        <div class="p-4 rounded-xl border-2 border-gray-200 text-center transition-all duration-300
                                    peer-checked:border-purple-500 peer-checked:bg-purple-50 peer-checked:shadow-lg
                                    hover:border-purple-300 hover:bg-purple-50/50 group-hover:scale-[1.02]">
                            <div class="w-12 h-12 mx-auto bg-purple-100 rounded-full flex items-center justify-center mb-2">
                                <span class="text-2xl font-bold text-purple-600">2</span>
                            </div>
                            <span class="font-medium text-gray-700">2nd Semester</span>
                            <p class="text-xs text-gray-400 mt-1">Second half of A.Y.</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Active Status Toggle -->
            <div class="mb-8 p-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-100">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Active Status</label>
                        <p class="text-sm text-gray-500 mt-1">Set this school year as the current active period</p>
                        <p class="text-xs text-amber-600 mt-2" x-show="form.is_active">
                            <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            Setting this as active will deactivate other school years
                        </p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" 
                               x-model="form.is_active"
                               class="sr-only peer">
                        <div class="w-14 h-7 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-500/20 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600"></div>
                        <span class="ms-3 text-sm font-medium" :class="form.is_active ? 'text-blue-600' : 'text-gray-500'" x-text="form.is_active ? 'Active' : 'Inactive'"></span>
                    </label>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-100">
                <a href="{{ route('school-years.index') }}" 
                   class="px-6 py-3 text-gray-600 hover:text-gray-800 font-medium rounded-xl hover:bg-gray-100 transition-all">
                    Cancel
                </a>
                <button type="submit" 
                        :disabled="loading"
                        class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-700 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-xl hover:shadow-blue-500/40 hover:-translate-y-0.5 transition-all duration-300 flex items-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <template x-if="loading">
                        <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </template>
                    <template x-if="!loading">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </template>
                    <span x-text="loading ? 'Updating...' : 'Update School Year'"></span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function editSchoolYearForm() {
    return {
        form: {
            year: '{{ $schoolYear->year }}',
            semester: '{{ $schoolYear->semester ?? "" }}',
            is_active: {{ $schoolYear->is_active ? 'true' : 'false' }}
        },
        errors: {},
        loading: false,
        successMessage: '',
        
        async submitForm() {
            this.loading = true;
            this.errors = {};
            this.successMessage = '';
            
            try {
                const response = await fetch('/api/school-years/{{ $schoolYear->id }}', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        ...this.form,
                        is_active: this.form.is_active ? 1 : 0
                    })
                });
                
                const data = await response.json();
                
                if (!response.ok) {
                    if (data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            this.errors[key] = data.errors[key][0];
                        });
                    } else {
                        this.errors.general = data.message || 'An error occurred';
                    }
                } else {
                    this.successMessage = 'School year updated successfully!';
                    setTimeout(() => {
                        window.location.href = '{{ route("school-years.index") }}';
                    }, 1000);
                }
            } catch (error) {
                this.errors.general = 'Network error. Please try again.';
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endsection