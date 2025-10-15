@extends('layouts.dashboard')

@section('title', 'Subscription')
@section('page-title', 'Subscription')

@section('styles')
<style>
    .subscription-card {
        transition: all 0.3s ease;
    }
    .subscription-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
</style>
@endsection

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center space-x-4">
                
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('messages.subscription') }}</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('messages.manage_subscription') }}</p>
                </div>
            </div>
        </div>
    </div>

    @if(!$hasCompany)
    <!-- No Company Alert -->
    <div class="bg-yellow-50 dark:bg-yellow-900/30 border-l-4 border-yellow-400 rounded-lg p-6 shadow-sm">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400 text-2xl"></i>
            </div>
            <div class="ml-4 flex-1">
                <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-200">{{ __('messages.no_company_registered') }}</h3>
                <p class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                    {{ __('messages.company_registration_required') }}
                </p>
                <div class="mt-4">
                    <a href="{{ route('vendor.company.create') }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-building mr-2"></i>
                        {{ __('messages.register_company') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- Current Subscription Section -->
    <div class="mb-8">
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">{{ __('messages.current_subscription') }}</h3>
        
        @if($currentSubscription)
        <!-- Active Subscription Card -->
        <div class="subscription-card bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="bg-indigo-500 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-crown text-yellow-300 text-2xl"></i>
                        <div>
                            <h4 class="text-xl font-bold text-white">
                                {{ $currentSubscription->subscriptionType->title ?? $currentSubscription->subscriptionType->type_label . ' ' . __('messages.plan') }}
                            </h4>
                            <p class="text-indigo-100 text-sm">{{ $currentSubscription->subscriptionType->period_label }} {{ __('messages.billing') }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold text-white">{{ $currentSubscription->subscriptionType->formatted_charge }}</div>
                        <div class="text-indigo-100 text-sm">{{ __('messages.per') }} {{ $currentSubscription->subscriptionType->period }}</div>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- Status -->
                    <div class="flex items-center gap-1">
                        <div class="flex-shrink-0 w-10 h-10 bg-{{ $currentSubscription->status_color }}-100 dark:bg-{{ $currentSubscription->status_color }}-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-circle text-{{ $currentSubscription->status_color }}-600 dark:text-{{ $currentSubscription->status_color }}-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('messages.status') }}</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                <span class="inline-flex items-center px-2 py-0.5  rounded-md mr-1 text-xs font-medium bg-{{ $currentSubscription->status_color }}-100 text-{{ $currentSubscription->status_color }}-800 dark:bg-{{ $currentSubscription->status_color }}-900/30 dark:text-{{ $currentSubscription->status_color }}-300">
                                    {{ $currentSubscription->status_label }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- Start Date -->
                    <div class="flex items-center gap-1">
                        <div class="flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-calendar-check text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('messages.start_date') }}</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $currentSubscription->formatted_start_date }}</p>
                        </div>
                    </div>

                    <!-- End Date -->
                    <div class="flex items-center gap-1">
                        <div class="flex-shrink-0 w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-calendar-times text-orange-600 dark:text-orange-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('messages.end_date') }}</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $currentSubscription->formatted_end_date }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Days Remaining -->
                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 mb-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-start space-x-3 flex-col">
                            <div class="items-center flex flex-row pl-3 gap-2">
                                 <i class="fas fa-hourglass-half text-indigo-600 dark:text-indigo-400 text-l"></i>
                            <p class="text-2xl font-bold  text-gray-900 dark:text-white">{{ $currentSubscription->days_remaining }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('messages.days_remaining') }}</p>

                            </div>
                        </div>
                        @if($currentSubscription->isExpiringSoon())
                        <div class="bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300 px-4 py-2 rounded-lg">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <span class="text-sm font-medium">{{ __('messages.expiring_soon') }}</span>
                        </div>
                        @elseif($currentSubscription->isExpired())
                        <div class="bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 px-4 py-2 rounded-lg">
                            <i class="fas fa-times-circle mr-2"></i>
                            <span class="text-sm font-medium">{{ __('messages.expired') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Description -->
                @if($currentSubscription->subscriptionType->description)
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <h5 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">{{ __('messages.plan_details') }}</h5>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $currentSubscription->subscriptionType->description }}</p>
                </div>
                @endif
                
                <!-- Alert Message -->
                @if($currentSubscription->subscriptionType->alert_message)
                <div class="mt-4 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-indigo-600 dark:text-indigo-400 mt-0.5 mr-3"></i>
                        <p class="text-sm text-indigo-800 dark:text-indigo-300">{{ $currentSubscription->subscriptionType->alert_message }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @else
        <!-- No Active Subscription -->
        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-700 p-8 text-center">
            <div class="flex flex-col items-center">
                <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-credit-card text-gray-400 dark:text-gray-500 text-2xl"></i>
                </div>
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ __('messages.no_active_subscription') }}</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ __('messages.subscribe_to_continue') }}</p>
                <button class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    {{ __('messages.subscribe_now') }}
                </button>
            </div>
        </div>
        @endif
    </div>

    <!-- Subscription History Section -->
    @if($subscriptionHistory->count() > 0)
    <div class="mb-8">
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">{{ __('messages.subscription_history') }}</h3>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('messages.plan') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('messages.period') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('messages.status') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('messages.start_date') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('messages.end_date') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('messages.charge') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($subscriptionHistory as $subscription)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $subscription->subscriptionType->title ?? $subscription->subscriptionType->type_label . ' ' . __('messages.plan') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $subscription->subscriptionType->period_label }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $subscription->status_color }}-100 text-{{ $subscription->status_color }}-800 dark:bg-{{ $subscription->status_color }}-900/30 dark:text-{{ $subscription->status_color }}-300">
                                    {{ $subscription->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                {{ $subscription->formatted_start_date }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                {{ $subscription->formatted_end_date }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ $subscription->subscriptionType->formatted_charge }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($subscriptionHistory->hasPages())
            <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $subscriptionHistory->links() }}
            </div>
            @endif
        </div>
    </div>
    @endif
    @endif
</div>
@endsection

