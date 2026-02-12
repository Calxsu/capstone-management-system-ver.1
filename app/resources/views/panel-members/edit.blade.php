@extends('layouts.dashboard')

@section('title', 'Edit Panel Member')
@section('subtitle', 'Update panel member information')

@section('content')
<div class="max-w-4xl mx-auto animate-fade-in" x-data="editPanelMemberForm()">
    <!-- Back Button & Header -->
    <div class="mb-8">
        <a href="{{ route('panel-members.index') }}" 
           class="inline-flex items-center text-gray-500 hover:text-gray-700 transition-colors mb-4 group">
            <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Panel Members
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
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-8 py-6">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center text-white text-2xl font-bold">
                    {{ strtoupper(substr($panelMember->email, 0, 2)) }}
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white">Edit Panel Member</h1>
                    <p class="text-purple-100">Updating: {{ $panelMember->email }}</p>
                </div>
            </div>
        </div>

        <!-- Info Note about Roles -->
        <div class="mx-8 mt-6 p-4 bg-blue-50 border border-blue-200 rounded-xl">
            <div class="flex">
                <svg class="w-5 h-5 text-blue-500 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="text-sm text-blue-700 font-medium">About Roles</p>
                    <p class="text-sm text-blue-600 mt-1">Roles (Adviser, Chair, Critique) are assigned when you add this panel member to a capstone group. To change roles, manage the group's panel assignments.</p>
                </div>
            </div>
        </div>

        <!-- Current Group Assignments -->
        @if($panelMember->groups && $panelMember->groups->count() > 0)
        <div class="mx-8 mt-4 p-4 bg-gray-50 border border-gray-200 rounded-xl">
            <p class="text-sm font-semibold text-gray-700 mb-3">Current Group Assignments</p>
            <div class="space-y-2">
                @foreach($panelMember->groups as $group)
                    @php
                        $role = $group->pivot->role ?? 'Unknown';
                        $roleColors = [
                            'Adviser' => 'bg-purple-100 text-purple-700',
                            'Chair' => 'bg-emerald-100 text-emerald-700',
                            'Critique' => 'bg-orange-100 text-orange-700',
                        ];
                        $roleColor = $roleColors[$role] ?? 'bg-gray-100 text-gray-700';
                    @endphp
                    <div class="flex items-center justify-between py-2 px-3 bg-white rounded-lg border border-gray-100">
                        <span class="text-sm text-gray-700">{{ $group->name ?? 'Group #' . $group->id }}</span>
                        <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $roleColor }}">{{ $role }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <form @submit.prevent="submitForm" class="p-8">
            <!-- Email -->
            <div class="mb-6">
                <div class="group">
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            Email Address
                        </span>
                    </label>
                    <input type="email"
                           id="email"
                           x-model="form.email"
                           class="w-full px-4 py-3 border-2 rounded-xl focus:outline-none focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 transition-all"
                           :class="errors.email ? 'border-red-500' : 'border-gray-200'"
                           placeholder="Enter email address"
                           required>
                    <p x-show="errors.email" class="mt-2 text-sm text-red-600 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        <span x-text="errors.email"></span>
                    </p>
                </div>
            </div>

            <!-- Specialization -->
            <div class="mb-6">
                <label for="specialization" class="block text-sm font-semibold text-gray-700 mb-2">Specialization</label>
                <select id="specialization" x-model="form.specialization" class="w-full px-4 py-3 border-2 rounded-xl focus:outline-none focus:border-purple-500 transition-all">
                    <option value="">(none)</option>
                    <option value="Networking" {{ $panelMember->specialization === 'Networking' ? 'selected' : '' }}>Networking</option>
                    <option value="Systems Development" {{ $panelMember->specialization === 'Systems Development' ? 'selected' : '' }}>Systems Development</option>
                </select>
            </div>

            <!-- Status Toggle -->
            <div class="mb-8 p-4 bg-gray-50 rounded-xl">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Status</label>
                        <p class="text-sm text-gray-500">Toggle to set panel member as active or inactive</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" 
                               x-model="form.status"
                               :true-value="'active'"
                               :false-value="'inactive'"
                               class="sr-only peer">
                        <div class="w-14 h-7 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-500/20 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-green-500"></div>
                        <span class="ms-3 text-sm font-medium" :class="form.status === 'active' ? 'text-green-600' : 'text-gray-700'" x-text="form.status === 'active' ? 'Active' : 'Inactive'"></span>
                    </label>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-100">
                <a href="{{ route('panel-members.index') }}" 
                   class="px-6 py-3 text-gray-600 hover:text-gray-800 font-medium rounded-xl hover:bg-gray-100 transition-all">
                    Cancel
                </a>
                <button type="submit" 
                        :disabled="loading"
                        class="px-8 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-xl shadow-lg shadow-purple-500/30 hover:shadow-xl hover:shadow-purple-500/40 hover:-translate-y-0.5 transition-all duration-300 flex items-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed">
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
                    <span x-text="loading ? 'Updating...' : 'Update Panel Member'"></span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function editPanelMemberForm() {
    return {
        form: {
            email: '{{ $panelMember->email }}',
            specialization: '{{ $panelMember->specialization }}',
            status: '{{ $panelMember->status ?? "active" }}'
        },
        errors: {},
        loading: false,
        successMessage: '',
        
        async submitForm() {
            this.loading = true;
            this.errors = {};
            this.successMessage = '';
            
            try {
                const response = await fetch('/api/panel-members/{{ $panelMember->id }}', {
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
                    this.successMessage = 'Panel member updated successfully!';
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
