@extends('layouts.dashboard')

@section('title', 'ETL Role Values')
@section('subtitle', 'Configure ETL values for each panel member role')

@section('content')
<div class="max-w-3xl mx-auto" x-data="etlRoleValuesForm()">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('etl.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to ETL Computation
        </a>
    </div>

    <!-- Success Message -->
    <div x-show="successMessage" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <span x-text="successMessage"></span>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-purple-50">
            <h3 class="text-lg font-semibold text-gray-900">Configure ETL Values</h3>
            <p class="text-sm text-gray-500 mt-1">Set the ETL value for each panel member role. These values are used to compute the Equivalent Teaching Load.</p>
        </div>

        <form @submit.prevent="submitForm" class="p-6 space-y-6">

            @foreach($roleValues as $index => $roleValue)
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold
                        @if($roleValue->role === 'Adviser') bg-gradient-to-br from-purple-500 to-purple-600
                        @elseif($roleValue->role === 'Chair') bg-gradient-to-br from-emerald-500 to-emerald-600
                        @else bg-gradient-to-br from-orange-500 to-orange-600 @endif">
                        @if($roleValue->role === 'Adviser')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        @elseif($roleValue->role === 'Chair')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                            </svg>
                        @else
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        @endif
                    </div>
                    <div class="ml-4">
                        <h4 class="font-semibold text-gray-900">{{ $roleValue->role }}</h4>
                        <p class="text-sm text-gray-500">{{ $roleValue->description }}</p>
                    </div>
                </div>
                <div class="flex items-center">
                    <input type="number" 
                           x-model="form.values[{{ $index }}].etl_value"
                           step="0.01"
                           min="0"
                           max="99.99"
                           class="w-24 px-4 py-2 text-center text-lg font-bold rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
            </div>
            @endforeach

            <!-- Info Box -->
            <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl">
                <div class="flex">
                    <svg class="w-5 h-5 text-blue-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h5 class="font-medium text-blue-900">ETL Computation Formula</h5>
                        <p class="text-sm text-blue-700 mt-1">
                            Total ETL = (Adviser Groups × Adviser Value) + (Chair Groups × Chair Value) + (Critique Groups × Critique Value)
                        </p>
                        <p class="text-sm text-blue-600 mt-2">
                            <strong>Note:</strong> Only ongoing projects (without grades) are counted. Completed projects with grades are excluded from ETL computation.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-4">
                <button type="submit" 
                        :disabled="loading"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-xl transition-all duration-300 hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed">
                    <template x-if="loading">
                        <svg class="w-5 h-5 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </template>
                    <template x-if="!loading">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </template>
                    <span x-text="loading ? 'Saving...' : 'Save Changes'"></span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function etlRoleValuesForm() {
    return {
        form: {
            values: [
                @foreach($roleValues as $roleValue)
                { id: {{ $roleValue->id }}, etl_value: '{{ $roleValue->etl_value }}' },
                @endforeach
            ]
        },
        loading: false,
        successMessage: '',

        async submitForm() {
            this.loading = true;
            this.successMessage = '';

            try {
                const response = await fetch('/api/etl/role-values', {
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
                    alert(data.message || 'An error occurred');
                } else {
                    this.successMessage = 'ETL role values updated successfully!';
                    setTimeout(() => {
                        this.successMessage = '';
                    }, 3000);
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
