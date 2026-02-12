@extends('layouts.dashboard')

@section('title', 'Delete Account')
@section('subtitle', 'Permanently delete your account')

@section('content')
<div class="max-w-2xl mx-auto" x-data="deleteAccountForm()">
    <!-- Back Button -->
    <a href="{{ route('profile.show') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 mb-6 animate-fade-in">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to Profile
    </a>

    <!-- Warning Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-red-200 overflow-hidden animate-slide-up">
        <!-- Header -->
        <div class="p-6 border-b border-red-100 bg-gradient-to-r from-red-50 to-orange-50">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-orange-500 rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-red-900">Delete Account</h2>
                    <p class="text-sm text-red-600">This action cannot be undone</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <!-- Warning Message -->
            <div class="p-4 bg-red-50 border border-red-200 rounded-xl mb-6 animate-slide-up" style="animation-delay: 0.1s">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-red-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-red-800">Warning: This action is permanent!</p>
                        <p class="mt-1 text-sm text-red-700">Once you delete your account, there is no going back. Please be certain.</p>
                    </div>
                </div>
            </div>

            <!-- What will be deleted -->
            <div class="mb-6 animate-slide-up" style="animation-delay: 0.15s">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">What will be deleted:</h3>
                <ul class="space-y-2">
                    <li class="flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 text-red-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                        Your profile information and settings
                    </li>
                    <li class="flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 text-red-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                        Your activity history and logs
                    </li>
                    <li class="flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 text-red-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                        Access to the system
                    </li>
                </ul>
            </div>

            <form @submit.prevent="submitForm" class="animate-slide-up" style="animation-delay: 0.2s">

                <!-- Password Confirmation -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Confirm your password to continue</label>
                    <input type="password" x-model="password"
                           class="w-full px-4 py-3 rounded-xl border focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all"
                           :class="error ? 'border-red-500' : 'border-gray-200'"
                           placeholder="Enter your password" required>
                    <p x-show="error" class="mt-2 text-sm text-red-600 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        <span x-text="error"></span>
                    </p>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end space-x-4">
                    <a href="{{ route('profile.show') }}" class="px-6 py-3 text-gray-700 font-medium hover:bg-gray-100 rounded-xl transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            :disabled="loading"
                            class="px-6 py-3 bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 text-white font-medium rounded-xl shadow-lg shadow-red-500/30 hover:shadow-xl transition-all hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span class="flex items-center">
                            <template x-if="loading">
                                <svg class="w-5 h-5 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </template>
                            <span x-text="loading ? 'Deleting...' : 'Delete My Account'"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function deleteAccountForm() {
    return {
        password: '',
        error: '',
        loading: false,

        async submitForm() {
            this.loading = true;
            this.error = '';

            try {
                const response = await fetch('/api/profile', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ password: this.password })
                });

                const data = await response.json();

                if (!response.ok) {
                    if (data.errors && data.errors.password) {
                        this.error = data.errors.password[0];
                    } else {
                        this.error = data.message || 'An error occurred';
                    }
                } else {
                    window.location.href = '/';
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
