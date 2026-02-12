@extends('layouts.dashboard')

@section('title', 'Edit Profile')
@section('subtitle', 'Update your account information')

@section('content')
<div class="max-w-2xl mx-auto" x-data="editProfileForm()">
    <!-- Back Button -->
    <a href="{{ route('profile.show') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 mb-6 animate-fade-in">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to Profile
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
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Edit Profile</h2>
                    <p class="text-sm text-gray-500">Update your personal information</p>
                </div>
            </div>
        </div>

        <form @submit.prevent="submitForm" class="p-6 space-y-6">

            <!-- Name -->
            <div class="animate-slide-up" style="animation-delay: 0.1s">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                <input type="text" x-model="form.name"
                       class="w-full px-4 py-3 rounded-xl border focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"
                       :class="errors.name ? 'border-red-500' : 'border-gray-200'"
                       placeholder="Enter your full name" required>
                <p x-show="errors.name" class="mt-2 text-sm text-red-600 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    <span x-text="errors.name"></span>
                </p>
            </div>

            <!-- Email -->
            <div class="animate-slide-up" style="animation-delay: 0.15s">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                <input type="email" x-model="form.email"
                       class="w-full px-4 py-3 rounded-xl border focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"
                       :class="errors.email ? 'border-red-500' : 'border-gray-200'"
                       placeholder="Enter your email address" required>
                <p x-show="errors.email" class="mt-2 text-sm text-red-600 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    <span x-text="errors.email"></span>
                </p>
            </div>

            <!-- Submit -->
            <div class="flex items-center justify-end space-x-4 pt-4 animate-slide-up" style="animation-delay: 0.2s">
                <a href="{{ route('profile.show') }}" class="px-6 py-3 text-gray-700 font-medium hover:bg-gray-100 rounded-xl transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        :disabled="loading"
                        class="px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-medium rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-xl transition-all hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span class="flex items-center">
                        <template x-if="loading">
                            <svg class="w-5 h-5 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </template>
                        <span x-text="loading ? 'Saving...' : 'Save Changes'"></span>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function editProfileForm() {
    return {
        form: {
            name: '{{ $user->name ?? '' }}',
            email: '{{ $user->email ?? '' }}'
        },
        errors: {},
        loading: false,
        successMessage: '',

        async submitForm() {
            this.loading = true;
            this.errors = {};
            this.successMessage = '';

            try {
                const response = await fetch('/api/profile', {
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
                    this.successMessage = 'Profile updated successfully!';
                    setTimeout(() => {
                        window.location.href = '{{ route("profile.show") }}';
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
