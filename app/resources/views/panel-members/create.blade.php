@extends('layouts.dashboard')

@section('title', 'Create Panel Member')
@section('subtitle', 'Add a new panel member')

@section('content')
<div class="max-w-2xl mx-auto" x-data="createPanelMemberForm()">
    <!-- Back Button -->
    <a href="{{ route('panel-members.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 mb-6 animate-fade-in">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to Panel Members
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
        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">New Panel Member</h2>
                    <p class="text-sm text-gray-500">Fill in the details below</p>
                </div>
            </div>
        </div>

        <!-- Info Note -->
        <div class="mx-6 mt-6 p-4 bg-blue-50 border border-blue-200 rounded-xl">
            <div class="flex">
                <svg class="w-5 h-5 text-blue-500 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="text-sm text-blue-700 font-medium">About Roles</p>
                    <p class="text-sm text-blue-600 mt-1">Roles (Adviser, Chair, Critique) are assigned when you add this panel member to a capstone group. A panel member can have different roles in different groups.</p>
                </div>
            </div>
        </div>

        <form @submit.prevent="submitForm" class="p-6 space-y-6">
            <!-- Email -->
            <div class="animate-slide-up" style="animation-delay: 0.1s">
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <input type="email" id="email" x-model="form.email"
                           class="w-full pl-12 pr-4 py-3 rounded-xl border focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"
                           :class="errors.email ? 'border-red-500' : 'border-gray-200'"
                           placeholder="Enter email address" required>
                </div>
                <p x-show="errors.email" class="mt-2 text-sm text-red-600 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    <span x-text="errors.email"></span>
                </p>
            </div>

            <!-- Specialization -->
            <div class="animate-slide-up" style="animation-delay: 0.15s">
                <label for="specialization" class="block text-sm font-semibold text-gray-700 mb-2">Specialization</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <select id="specialization" x-model="form.specialization"
                           class="w-full pl-12 pr-4 py-3 rounded-xl border focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        <option value="">Select Specialization</option>
                        <option value="Networking">Networking</option>
                        <option value="Systems Development">Systems Development</option>
                    </select>
                </div>
            </div>

            <!-- Status -->
            <div class="animate-slide-up" style="animation-delay: 0.2s">
                <label class="block text-sm font-semibold text-gray-700 mb-3">Status</label>
                <div class="flex items-center space-x-6">
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" x-model="form.status" value="active" class="w-4 h-4 text-green-600 border-gray-300 focus:ring-green-500">
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </label>
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" x-model="form.status" value="inactive" class="w-4 h-4 text-gray-600 border-gray-300 focus:ring-gray-500">
                        <span class="ml-2 text-sm text-gray-700">Inactive</span>
                    </label>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-100 animate-slide-up" style="animation-delay: 0.5s">
                <a href="{{ route('panel-members.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        :disabled="loading"
                        class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-xl transition-all duration-300 hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2">
                    <template x-if="loading">
                        <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </template>
                    <span x-text="loading ? 'Creating...' : 'Create Panel Member'"></span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function createPanelMemberForm() {
    return {
        form: {
            email: '',
            specialization: '',
            status: 'active'
        },
        errors: {},
        loading: false,
        successMessage: '',
        
        async submitForm() {
            this.loading = true;
            this.errors = {};
            this.successMessage = '';
            
            try {
                const response = await fetch('/api/panel-members', {
                    method: 'POST',
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
                    this.successMessage = 'Panel member created successfully!';
                    setTimeout(() => {
                        window.location.href = '{{ route("panel-members.index") }}';
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