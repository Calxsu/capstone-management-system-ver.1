@extends('layouts.dashboard')

@section('title', 'My Profile')
@section('subtitle', 'View your account information')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Success Message -->
    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center animate-fade-in">
        <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <span class="text-green-700">{{ session('success') }}</span>
    </div>
    @endif

    <!-- Profile Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
        <!-- Header Banner -->
        <div class="h-32 bg-gradient-to-r from-blue-500 via-purple-500 to-indigo-600"></div>
        
        <!-- Profile Info -->
        <div class="relative px-6 pb-6">
            <!-- Avatar -->
            <div class="absolute -top-12 left-6">
                <div class="w-24 h-24 rounded-2xl bg-white p-1 shadow-xl">
                    <div class="w-full h-full rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-3xl font-bold">
                        {{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end pt-4 space-x-3">
                <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-xl transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Profile
                </a>
                <a href="{{ route('profile.password') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                    </svg>
                    Change Password
                </a>
            </div>

            <!-- Name & Role -->
            <div class="mt-8">
                <h2 class="text-2xl font-bold text-gray-900">{{ $user->name ?? 'Admin User' }}</h2>
                <p class="text-gray-500">System Administrator</p>
            </div>

            <!-- Info Grid -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="p-4 bg-gray-50 rounded-xl">
                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Email Address</label>
                    <p class="mt-1 text-gray-900 font-medium">{{ $user->email ?? 'admin@example.com' }}</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl">
                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Member Since</label>
                    <p class="mt-1 text-gray-900 font-medium">{{ $user->created_at?->format('F d, Y') ?? 'January 01, 2024' }}</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl">
                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Last Updated</label>
                    <p class="mt-1 text-gray-900 font-medium">{{ $user->updated_at?->format('F d, Y') ?? 'January 01, 2024' }}</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl">
                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Account Status</label>
                    <p class="mt-1">
                        <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 text-sm font-semibold rounded-full">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            Active
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up" style="animation-delay: 0.1s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Login Sessions</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">1</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-xl">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up" style="animation-delay: 0.15s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Actions Today</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">12</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-xl">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up" style="animation-delay: 0.2s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Security Score</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">Good</p>
                </div>
                <div class="p-3 bg-green-100 rounded-xl">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
