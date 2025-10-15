@extends('layouts.provider')

@section('title', __('messages.subscription'))

@section('styles')
<style>
    .subscription-card {
        transition: all 0.3s ease;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    .subscription-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    .subscription-header {
        background: linear-gradient(135deg, #5865F2 0%, #4752c4 100%);
        padding: 1.5rem;
        color: white;
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .status-badge.active {
        background-color: #d1fae5;
        color: #065f46;
    }
    .status-badge.inactive {
        background-color: #fef3c7;
        color: #92400e;
    }
    .status-badge.cancelled {
        background-color: #fee2e2;
        color: #991b1b;
    }
    .status-badge.expired {
        background-color: #fee2e2;
        color: #991b1b;
    }
    .info-box {
        background: #f9fafb;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    .icon-box {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .icon-box.green {
        background-color: #d1fae5;
        color: #065f46;
    }
    .icon-box.blue {
        background-color: #dbeafe;
        color: #1e40af;
    }
    .icon-box.orange {
        background-color: #fed7aa;
        color: #9a3412;
    }
    .icon-box.indigo {
        background-color: #e0e7ff;
        color: #3730a3;
    }
    .days-remaining-box {
        background: #f9fafb;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .alert-warning {
        background-color: #fef3c7;
        border-left: 4px solid #f59e0b;
        padding: 1rem;
        border-radius: 8px;
    }
    .alert-info {
        background-color: #e0e7ff;
        border-left: 4px solid #5865F2;
        padding: 1rem;
        border-radius: 8px;
    }
    .no-subscription-box {
        background: #f9fafb;
        border: 2px dashed #d1d5db;
        border-radius: 12px;
        padding: 3rem;
        text-align: center;
    }
    .table-responsive {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header Section -->
    <div class="mb-4">
        <h2 class="h3 fw-bold mb-1">{{ __('messages.subscription') }}</h2>
        <p class="text-muted small">{{ __('messages.manage_subscription') }}</p>
    </div>

    @if(!$hasProvider)
    <!-- No Provider Alert -->
    <div class="alert-warning mb-4">
        <div class="d-flex align-items-start">
            <div class="me-3">
                <i class="fas fa-exclamation-triangle fs-4" style="color: #f59e0b;"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-2">{{ __('messages.no_provider_registered') }}</h5>
                <p class="mb-0 small">{{ __('messages.provider_registration_required') }}</p>
            </div>
        </div>
    </div>
    @else
    <!-- Current Subscription Section -->
    <div class="mb-4">
        <h4 class="fw-semibold mb-3">{{ __('messages.current_subscription') }}</h4>

        @if($currentSubscription)
        <!-- Active Subscription Card -->
        <div class="subscription-card">
            <div class="subscription-header">
                <div class="d-flex align-items-center justify-content-between flex-wrap">
                    <div class="d-flex align-items-center gap-2 mb-2 mb-md-0">
                        <i class="fas fa-crown text-warning fs-3"></i>
                        <div>
                            <h4 class="h5 fw-bold mb-0">
                                {{ $currentSubscription->subscriptionType->title ?? $currentSubscription->subscriptionType->type_label . ' ' . __('messages.plan') }}
                            </h4>
                            <p class="mb-0 small opacity-75">{{ $currentSubscription->subscriptionType->period_label }} {{ __('messages.billing') }}</p>
                        </div>
                    </div>
                    <div class="text-md-end">
                        <div class="h3 fw-bold mb-0">{{ $currentSubscription->subscriptionType->formatted_charge }}</div>
                        <div class="small opacity-75">{{ __('messages.per') }} {{ $currentSubscription->subscriptionType->period }}</div>
                    </div>
                </div>
            </div>

            <div class="p-4">
                <div class="row g-3 mb-3">
                    <!-- Status -->
                    <div class="col-12 col-md-4">
                        <div class="d-flex align-items-center gap-2">
                            <div class="icon-box {{ $currentSubscription->status_color == 'green' ? 'green' : ($currentSubscription->status_color == 'yellow' ? 'orange' : 'red') }}">
                                <i class="fas fa-circle"></i>
                            </div>
                            <div>
                                <p class="text-muted small mb-1">{{ __('messages.status') }}</p>
                                <span class="status-badge {{ $currentSubscription->status }}">
                                    {{ $currentSubscription->status_label }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Start Date -->
                    <div class="col-12 col-md-4">
                        <div class="d-flex align-items-center gap-2">
                            <div class="icon-box blue">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div>
                                <p class="text-muted small mb-1">{{ __('messages.start_date') }}</p>
                                <p class="fw-semibold mb-0">{{ $currentSubscription->formatted_start_date }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- End Date -->
                    <div class="col-12 col-md-4">
                        <div class="d-flex align-items-center gap-2">
                            <div class="icon-box orange">
                                <i class="fas fa-calendar-times"></i>
                            </div>
                            <div>
                                <p class="text-muted small mb-1">{{ __('messages.end_date') }}</p>
                                <p class="fw-semibold mb-0">{{ $currentSubscription->formatted_end_date }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Days Remaining -->
                <div class="days-remaining-box">
                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-box indigo">
                                <i class="fas fa-hourglass-half"></i>
                            </div>
                            <div>
                                <div class="h2 fw-bold mb-0">{{ $currentSubscription->days_remaining }}</div>
                                <p class="text-muted small mb-0">{{ __('messages.days_remaining') }}</p>
                            </div>
                        </div>
                        @if($currentSubscription->isExpiringSoon())
                        <div class="badge bg-warning text-dark px-3 py-2">
                            <i class="fas fa-exclamation-circle me-1"></i>
                            <span>{{ __('messages.expiring_soon') }}</span>
                        </div>
                        @elseif($currentSubscription->isExpired())
                        <div class="badge bg-danger px-3 py-2">
                            <i class="fas fa-times-circle me-1"></i>
                            <span>{{ __('messages.expired') }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Description -->
                @if($currentSubscription->subscriptionType->description)
                <div class="border-top pt-3">
                    <h6 class="fw-semibold mb-2">{{ __('messages.plan_details') }}</h6>
                    <p class="text-muted small mb-0">{{ $currentSubscription->subscriptionType->description }}</p>
                </div>
                @endif

                <!-- Alert Message -->
                @if($currentSubscription->subscriptionType->alert_message)
                <div class="alert-info mt-3">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-info-circle me-2 mt-1"></i>
                        <p class="small mb-0">{{ $currentSubscription->subscriptionType->alert_message }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @else
        <!-- No Active Subscription -->
        <div class="no-subscription-box">
            <div class="d-flex flex-column align-items-center">
                <div class="rounded-circle bg-light d-flex align-items-center justify-center mb-3" style="width: 64px; height: 64px;">
                    <i class="fas fa-credit-card text-muted fs-2"></i>
                </div>
                <h5 class="fw-semibold mb-2">{{ __('messages.no_active_subscription') }}</h5>
                <p class="text-muted small mb-3">{{ __('messages.subscribe_to_continue') }}</p>
                <button class="btn btn-primary d-inline-flex align-items-center">
                    <i class="fas fa-plus me-2"></i>
                    {{ __('messages.subscribe_now') }}
                </button>
            </div>
        </div>
        @endif
    </div>

    <!-- Subscription History Section -->
    @if($subscriptionHistory->count() > 0)
    <div class="mb-4">
        <h4 class="fw-semibold mb-3">{{ __('messages.subscription_history') }}</h4>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col" class="text-uppercase small fw-semibold">
                            {{ __('messages.plan') }}
                        </th>
                        <th scope="col" class="text-uppercase small fw-semibold">
                            {{ __('messages.period') }}
                        </th>
                        <th scope="col" class="text-uppercase small fw-semibold">
                            {{ __('messages.status') }}
                        </th>
                        <th scope="col" class="text-uppercase small fw-semibold">
                            {{ __('messages.start_date') }}
                        </th>
                        <th scope="col" class="text-uppercase small fw-semibold">
                            {{ __('messages.end_date') }}
                        </th>
                        <th scope="col" class="text-uppercase small fw-semibold">
                            {{ __('messages.charge') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subscriptionHistory as $subscription)
                    <tr>
                        <td>
                            <div class="fw-medium">
                                {{ $subscription->subscriptionType->title ?? $subscription->subscriptionType->type_label . ' ' . __('messages.plan') }}
                            </div>
                        </td>
                        <td>
                            <div class="text-muted small">
                                {{ $subscription->subscriptionType->period_label }}
                            </div>
                        </td>
                        <td>
                            <span class="status-badge {{ $subscription->status }}">
                                {{ $subscription->status_label }}
                            </span>
                        </td>
                        <td class="text-muted small">
                            {{ $subscription->formatted_start_date }}
                        </td>
                        <td class="text-muted small">
                            {{ $subscription->formatted_end_date }}
                        </td>
                        <td class="fw-medium">
                            {{ $subscription->subscriptionType->formatted_charge }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            @if($subscriptionHistory->hasPages())
            <div class="p-3 border-top">
                {{ $subscriptionHistory->links() }}
            </div>
            @endif
        </div>
    </div>
    @endif
    @endif
</div>
@endsection

