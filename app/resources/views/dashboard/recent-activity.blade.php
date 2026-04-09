@extends('layouts.dashboard')

@section('title', 'Recent Activity')
@section('subtitle', 'Detailed timeline of dashboard activity')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Activity Feed</h3>
            <p class="text-sm text-gray-500">Showing updates from evaluations, group creations, and group-level changes.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
            Back to Dashboard
        </a>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <a href="{{ route('dashboard.recent-activity', ['type' => 'all']) }}" class="rounded-xl border p-4 transition-all {{ $selectedType === 'all' ? 'border-blue-300 bg-blue-50' : 'border-gray-200 bg-white hover:border-gray-300' }}">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">All</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $summary['all'] }}</p>
        </a>

        <a href="{{ route('dashboard.recent-activity', ['type' => 'evaluation']) }}" class="rounded-xl border p-4 transition-all {{ $selectedType === 'evaluation' ? 'border-amber-300 bg-amber-50' : 'border-gray-200 bg-white hover:border-gray-300' }}">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Evaluations</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $summary['evaluation'] }}</p>
        </a>

        <a href="{{ route('dashboard.recent-activity', ['type' => 'group']) }}" class="rounded-xl border p-4 transition-all {{ $selectedType === 'group' ? 'border-emerald-300 bg-emerald-50' : 'border-gray-200 bg-white hover:border-gray-300' }}">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">New Groups</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $summary['group'] }}</p>
        </a>

        <a href="{{ route('dashboard.recent-activity', ['type' => 'change']) }}" class="rounded-xl border p-4 transition-all {{ $selectedType === 'change' ? 'border-indigo-300 bg-indigo-50' : 'border-gray-200 bg-white hover:border-gray-300' }}">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Group Changes</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $summary['change'] }}</p>
        </a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Type</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Activity</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Detail</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($activities as $activity)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-6 py-4 align-top">
                                @php
                                    $typeClass = match($activity['type']) {
                                        'evaluation' => 'bg-amber-100 text-amber-700',
                                        'group' => 'bg-emerald-100 text-emerald-700',
                                        default => 'bg-indigo-100 text-indigo-700',
                                    };
                                @endphp
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $typeClass }}">
                                    {{ ucfirst($activity['type']) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 align-top text-sm font-medium text-gray-900">{{ $activity['title'] }}</td>
                            <td class="px-6 py-4 align-top text-sm text-gray-600">{{ $activity['detail'] }}</td>
                            <td class="px-6 py-4 align-top text-sm text-gray-500">
                                <span>{{ \Illuminate\Support\Carbon::parse($activity['timestamp'])->format('M d, Y h:i A') }}</span>
                                <p class="mt-1 text-xs text-gray-400">{{ \Illuminate\Support\Carbon::parse($activity['timestamp'])->diffForHumans() }}</p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500">No activity found for this filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-100 px-6 py-4">
            {{ $activities->links() }}
        </div>
    </div>
</div>
@endsection
