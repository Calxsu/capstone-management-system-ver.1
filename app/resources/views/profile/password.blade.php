@extends('layouts.dashboard')

@section('title', 'Change Password')
@section('subtitle', 'Update your account password')

@section('content')
<div class="max-w-2xl mx-auto" x-data="changePasswordForm()">
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
        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-purple-50 to-pink-50">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Change Password</h2>
                    <p class="text-sm text-gray-500">Ensure your account uses a strong password</p>
                </div>
            </div>
        </div>

        <form @submit.prevent="submitForm" class="p-6 space-y-6">

            <!-- Current Password -->
            <div class="animate-slide-up" style="animation-delay: 0.1s">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Current Password</label>
                <input type="password" x-model="form.current_password"
                       class="w-full px-4 py-3 rounded-xl border focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all"
                       :class="errors.current_password ? 'border-red-500' : 'border-gray-200'"
                       placeholder="Enter your current password" required>
                <p x-show="errors.current_password" class="mt-2 text-sm text-red-600 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    <span x-text="errors.current_password"></span>
                </p>
            </div>

            <!-- New Password -->
            <div class="animate-slide-up" style="animation-delay: 0.15s">
                <label class="block text-sm font-semibold text-gray-700 mb-2">New Password</label>
                <input type="password" x-model="form.password"
                       class="w-full px-4 py-3 rounded-xl border focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all"
                       :class="errors.password ? 'border-red-500' : 'border-gray-200'"
                       placeholder="Enter your new password" required>
                <p x-show="errors.password" class="mt-2 text-sm text-red-600 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    <span x-text="errors.password"></span>
                </p>
                <p class="mt-2 text-xs text-gray-500">Password must be at least 8 characters long</p>
            </div>

            <!-- Confirm Password -->
            <div class="animate-slide-up" style="animation-delay: 0.2s">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Confirm New Password</label>
                <input type="password" x-model="form.password_confirmation"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all"
                       placeholder="Confirm your new password" required>
            </div>

            <!-- Security Tips -->
            <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl animate-slide-up" style="animation-delay: 0.25s">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-amber-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-amber-800">Password Tips</p>
                        <ul class="mt-1 text-xs text-amber-700 list-disc list-inside space-y-1">
                            <li>Use at least 8 characters</li>
                            <li>Include uppercase and lowercase letters</li>
                            <li>Add numbers and special characters</li>
                            <li>Avoid using personal information</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex items-center justify-end space-x-4 pt-4 animate-slide-up" style="animation-delay: 0.3s">
                <a href="{{ route('profile.show') }}" class="px-6 py-3 text-gray-700 font-medium hover:bg-gray-100 rounded-xl transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        :disabled="loading"
                        class="px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-600 hover:from-purple-600 hover:to-pink-700 text-white font-medium rounded-xl shadow-lg shadow-purple-500/30 hover:shadow-xl transition-all hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span class="flex items-center">
                        <template x-if="loading">
                            <svg class="w-5 h-5 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </template>
                        <span x-text="loading ? 'Updating...' : 'Update Password'"></span>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function changePasswordForm() {
    return {
        form: {
            current_password: '',
            password: '',
            password_confirmation: ''
        },
        errors: {},
        loading: false,
        successMessage: '',

        async submitForm() {
            this.loading = true;
            this.errors = {};
            this.successMessage = '';

            try {
                const response = await fetch('/api/profile/password', {
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
                    this.successMessage = 'Password changed successfully!';
                    this.form.current_password = '';
                    this.form.password = '';
                    this.form.password_confirmation = '';
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
