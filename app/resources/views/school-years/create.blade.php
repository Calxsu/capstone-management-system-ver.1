@extends('layouts.dashboard')

@section('title', 'Create School Year')
@section('subtitle', 'Add a new academic year to the system')

@section('content')
<div class="max-w-xl mx-auto" x-data="createSchoolYearForm()">
    <!-- Back Button -->
    <a href="{{ route('school-years.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 mb-6 animate-fade-in">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to School Years
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

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
        <!-- Header -->
        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-blue-50">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-blue-500 rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">New School Year</h2>
                    <p class="text-sm text-gray-500">Define the academic year period</p>
                </div>
            </div>
        </div>

        <form @submit.prevent="submitForm" class="p-6 space-y-6">
            <!-- Academic Year -->
            <div class="animate-slide-up" style="animation-delay: 0.1s">
                <label for="year" class="block text-sm font-semibold text-gray-700 mb-2">
                    Academic Year (A.Y.)
                    <span class="text-gray-400 font-normal ml-1">- The school year period</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <input type="text" id="year" x-model="form.year"
                           class="w-full pl-12 pr-4 py-3 rounded-xl border focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
                           :class="errors.year ? 'border-red-500' : 'border-gray-200'"
                           placeholder="e.g., 2024-2025" required>
                </div>
                <p class="mt-2 text-xs text-gray-500">Enter in format: YYYY-YYYY (e.g., 2024-2025 for Academic Year 2024-2025)</p>
                <p x-show="errors.year" class="mt-2 text-sm text-red-600 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    <span x-text="errors.year"></span>
                </p>
            </div>

            <!-- Semester -->
            <div class="animate-slide-up" style="animation-delay: 0.2s">
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    Semester
                    <span class="text-gray-400 font-normal ml-1">- First or Second semester of the academic year</span>
                </label>
                <div class="grid grid-cols-2 gap-4">
                    <label class="relative cursor-pointer group">
                        <input type="radio" x-model="form.semester" value="1st Semester" class="peer sr-only">
                        <div class="p-4 rounded-xl border-2 border-gray-200 text-center transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-blue-300 group-hover:scale-[1.02]">
                            <div class="w-10 h-10 mx-auto bg-blue-100 rounded-full flex items-center justify-center mb-2 peer-checked:bg-blue-500">
                                <span class="text-lg font-bold text-blue-600 peer-checked:text-white">1</span>
                            </div>
                            <span class="font-medium text-gray-700">1st Semester</span>
                            <p class="text-xs text-gray-400 mt-1">First half of A.Y.</p>
                        </div>
                    </label>
                    <label class="relative cursor-pointer group">
                        <input type="radio" x-model="form.semester" value="2nd Semester" class="peer sr-only">
                        <div class="p-4 rounded-xl border-2 border-gray-200 text-center transition-all peer-checked:border-purple-500 peer-checked:bg-purple-50 hover:border-purple-300 group-hover:scale-[1.02]">
                            <div class="w-10 h-10 mx-auto bg-purple-100 rounded-full flex items-center justify-center mb-2">
                                <span class="text-lg font-bold text-purple-600">2</span>
                            </div>
                            <span class="font-medium text-gray-700">2nd Semester</span>
                            <p class="text-xs text-gray-400 mt-1">Second half of A.Y.</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Active Status -->
            <div class="animate-slide-up" style="animation-delay: 0.3s">
                <label class="flex items-center cursor-pointer">
                    <div class="relative">
                        <input type="checkbox" x-model="form.is_active" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-indigo-500 transition-colors"></div>
                        <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
                    </div>
                    <span class="ml-3 text-sm font-medium text-gray-700">Set as active school year</span>
                </label>
                <p class="mt-2 text-xs text-gray-500 ml-14">This will deactivate the current active school year</p>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-100 animate-slide-up" style="animation-delay: 0.4s">
                <a href="{{ route('school-years.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        :disabled="loading"
                        class="px-6 py-2.5 bg-gradient-to-r from-indigo-500 to-blue-500 hover:from-indigo-600 hover:to-blue-600 text-white font-medium rounded-xl shadow-lg shadow-indigo-500/30 hover:shadow-xl transition-all duration-300 hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2">
                    <template x-if="loading">
                        <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </template>
                    <span x-text="loading ? 'Creating...' : 'Create School Year'"></span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function createSchoolYearForm() {
    return {
        form: {
            year: '',
            semester: '1st Semester',
            is_active: true
        },
        errors: {},
        loading: false,
        successMessage: '',
        
        async submitForm() {
            this.loading = true;
            this.errors = {};
            this.successMessage = '';
            
            try {
                const response = await fetch('/api/school-years', {
                    method: 'POST',
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
                    this.successMessage = 'School year created successfully!';
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