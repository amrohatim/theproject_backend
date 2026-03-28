@extends('layouts.dashboard')

@section('title', __('messages.notifications'))
@section('page-title', __('messages.notifications'))
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
@endsection

@section('content')
<div class="container mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('messages.notifications') }}</h2>
        <p class="mt-1 text-sm text-[var(--primary)] dark:text-[var(--primary-light)]">{{ __('messages.latest') }}</p>
    </div>

    @if(!$hasProvider)
        <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-700 dark:border-yellow-700 dark:bg-yellow-900/20 dark:text-yellow-200">
            {{ __('messages.no_items_found') }}
        </div>
    @elseif($notifications->isEmpty())
        <div class="rounded-lg border border-gray-200 bg-white p-8 text-center shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <i class="fas fa-bell-slash text-3xl text-gray-300 dark:text-gray-600"></i>
            <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">{{ __('messages.no_notifications') }}</p>
        </div>
    @else
        <div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($notifications as $notification)
                    <div class="px-5 py-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm text-gray-800 dark:text-gray-200">
                                    {{ app()->getLocale() === 'ar' ? $notification->message_arabic : $notification->message }}
                                </p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ $notification->sender_name }} · {{ $notification->created_at?->diffForHumans() }}
                                </p>
                            </div>
                            <span class="inline-flex rounded-full bg-[var(--primary-light)] px-2 py-1 text-xs font-medium capitalize text-[var(--primary)] dark:bg-[var(--primary)]/20 dark:text-[var(--primary-light)]">
                                {{ $notification->notification_type }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
