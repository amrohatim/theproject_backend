@extends('layouts.merchant')

@section('title', __('messages.notifications'))
@section('header', __('messages.notifications'))
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
@endsection

@section('content')
<div class="min-h-screen bg-[var(--towhite)] p-4 sm:p-6 lg:p-8">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">{{ __('messages.notifications') }}</h2>
        <p class="mt-1 text-sm text-[var(--primary)]">{{ __('messages.latest') }}</p>
    </div>

    @if(!$hasMerchant)
        <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-700">
            {{ __('merchant.account_information_subtitle') }}
        </div>
    @elseif($notifications->isEmpty())
        <div class="rounded-lg border border-gray-200 bg-white p-8 text-center shadow-sm">
            <i class="fas fa-bell-slash text-3xl text-gray-300"></i>
            <p class="mt-3 text-sm text-gray-500">{{ __('messages.no_notifications') }}</p>
        </div>
    @else
        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="divide-y divide-gray-200">
                @foreach($notifications as $notification)
                    <div class="px-5 py-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm text-gray-800">
                                    {{ app()->getLocale() === 'ar' ? $notification->message_arabic : $notification->message }}
                                </p>
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ $notification->sender_name }} · {{ $notification->created_at?->diffForHumans() }}
                                </p>
                            </div>
                            <span class="inline-flex rounded-full bg-[var(--primary-light)] px-2 py-1 text-xs font-medium capitalize text-[var(--primary)]">
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
