@extends('layouts.dashboard')

@section('title', 'Edit Student')
@section('subtitle', 'Update student information')

@section('content')
<div class="max-w-4xl mx-auto animate-fade-in" x-data="editStudentForm()">
    <!-- Back Button -->
    <div class="mb-8">
        <a href="{{ route('students.index') }}" 
           class="inline-flex items-center text-gray-500 hover:text-gray-700 transition-colors group">
            <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Students
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
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-8 py-6">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center text-white text-2xl font-bold">
                    {{ strtoupper(substr($student->name, 0, 2)) }}
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white">Edit Student</h1>
                    <p class="text-green-100">Updating: {{ $student->name }}</p>
                </div>
            </div>
        </div>

        <form @submit.prevent="submitForm" class="p-8">
            <!-- Name -->
            <div class="mb-6">
                <div class="group">
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Full Name
                        </span>
                    </label>
                    <input type="text"
                           id="name"
                           x-model="form.name"
                           class="w-full px-4 py-3 border-2 rounded-xl focus:outline-none focus:border-green-500 focus:ring-4 focus:ring-green-500/10 transition-all"
                           :class="errors.name ? 'border-red-500' : 'border-gray-200'"
                           placeholder="Enter full name"
                           required>
                    <p x-show="errors.name" class="mt-2 text-sm text-red-600 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        <span x-text="errors.name"></span>
                    </p>
                </div>
            </div>

            <!-- Specialization Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="group">
                    <label for="specialization" class="block text-sm font-semibold text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            Specialization
                        </span>
                    </label>
                    <select id="specialization"
                            x-model="form.specialization"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-500 focus:ring-4 focus:ring-green-500/10 transition-all bg-white">
                        <option value="">Select Specialization</option>
                        <option value="Networking">Networking</option>
                        <option value="Systems Development">Systems Development</option>
                    </select>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-100">
                <a href="{{ route('students.index') }}" 
                   class="px-6 py-3 text-gray-600 hover:text-gray-800 font-medium rounded-xl hover:bg-gray-100 transition-all">
                    Cancel
                </a>
                <button type="submit" 
                        :disabled="loading"
                        class="px-8 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-xl shadow-lg shadow-green-500/30 hover:shadow-xl hover:shadow-green-500/40 hover:-translate-y-0.5 transition-all duration-300 flex items-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed">
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
                    <span x-text="loading ? 'Updating...' : 'Update Student'"></span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function editStudentForm() {
    return {
        form: {
            name: '{{ $student->name }}',
            specialization: '{{ $student->specialization ?? '' }}'
        },
        errors: {},
        loading: false,
        successMessage: '',
        
        async submitForm() {
            this.loading = true;
            this.errors = {};
            this.successMessage = '';
            
            try {
                const response = await fetch('/api/students/{{ $student->id }}', {
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
                        this.errors.general = data.message || 'An error occurred';
                    }
                } else {
                    this.successMessage = 'Student updated successfully!';
                    setTimeout(() => {
                        window.location.href = '{{ route("students.index") }}';
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