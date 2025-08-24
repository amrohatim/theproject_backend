@extends('layouts.app')

@section('title', 'Registration Declined')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-body text-center py-5">
                    <!-- Icon -->
                    <div class="mb-4">
                        <i class="fas fa-times-circle text-danger" style="font-size: 4rem;"></i>
                    </div>
                    
                    <!-- Title -->
                    <h2 class="card-title text-danger mb-3">Registration Declined</h2>
                    
                    <!-- Message -->
                    <div class="alert alert-danger mx-auto" style="max-width: 500px;">
                        <h5 class="alert-heading">
                            <i class="fas fa-exclamation-triangle"></i> Application Not Approved
                        </h5>
                        <p class="mb-0">
                            We regret to inform you that your {{ Auth::user()->role ?? 'user' }} registration 
                            application has been declined after review.
                        </p>
                    </div>
                    
                    <!-- Get decline reason if available -->
                    @php
                        $registration = \App\Models\RegistrationApproval::where('user_id', Auth::id())->first();
                    @endphp
                    
                    @if($registration && $registration->admin_message)
                        <div class="alert alert-warning mx-auto mt-4" style="max-width: 600px;">
                            <h6 class="alert-heading">
                                <i class="fas fa-comment"></i> Reason for Decline
                            </h6>
                            <p class="mb-0">{{ $registration->admin_message }}</p>
                        </div>
                    @endif
                    
                    <!-- Account Details -->
                    <div class="row mt-4">
                        <div class="col-md-6 mx-auto">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-user"></i> Account Details
                                    </h6>
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr>
                                            <td><strong>Name:</strong></td>
                                            <td>{{ Auth::user()->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>{{ Auth::user()->email }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Type:</strong></td>
                                            <td>
                                                <span class="badge bg-primary">
                                                    {{ ucfirst(Auth::user()->role ?? 'User') }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times"></i> Declined
                                                </span>
                                            </td>
                                        </tr>
                                        @if($registration && $registration->reviewed_at)
                                            <tr>
                                                <td><strong>Reviewed:</strong></td>
                                                <td>{{ $registration->reviewed_at->format('M j, Y') }}</td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Next Steps -->
                    <div class="mt-4">
                        <h5>
                            <i class="fas fa-lightbulb"></i> What Can You Do Next?
                        </h5>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="text-center">
                                    <i class="fas fa-phone text-primary mb-2" style="font-size: 2rem;"></i>
                                    <h6>Contact Support</h6>
                                    <p class="small text-muted">Reach out to our team for clarification on the requirements</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <i class="fas fa-edit text-warning mb-2" style="font-size: 2rem;"></i>
                                    <h6>Address Issues</h6>
                                    <p class="small text-muted">Work on addressing the concerns mentioned in the decline reason</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <i class="fas fa-redo text-success mb-2" style="font-size: 2rem;"></i>
                                    <h6>Reapply</h6>
                                    <p class="small text-muted">Submit a new application once requirements are met</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contact Information -->
                    <div class="alert alert-info mt-4">
                        <h6>
                            <i class="fas fa-headset"></i> Get Help & Support
                        </h6>
                        <p class="mb-3">
                            Our support team is here to help you understand the requirements and guide you through the process.
                        </p>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1">
                                    <i class="fas fa-envelope text-primary"></i> 
                                    <strong>Email:</strong> support@{{ parse_url(config('app.url'), PHP_URL_HOST) }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1">
                                    <i class="fas fa-phone text-primary"></i> 
                                    <strong>Phone:</strong> +971-XXX-XXXX
                                </p>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <p class="mb-0">
                                    <i class="fas fa-clock text-primary"></i> 
                                    <strong>Business Hours:</strong> Sunday - Thursday, 9:00 AM - 6:00 PM (UAE Time)
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Appeal Process -->
                    <div class="alert alert-light mt-4">
                        <h6>
                            <i class="fas fa-balance-scale"></i> Appeal Process
                        </h6>
                        <p class="mb-0">
                            If you believe this decision was made in error, you can contact our support team 
                            to discuss your application and potentially appeal the decision.
                        </p>
                    </div>
                    
                    <!-- Actions -->
                    <div class="mt-4">
                        <a href="mailto:support@{{ parse_url(config('app.url'), PHP_URL_HOST) }}" 
                           class="btn btn-primary me-2">
                            <i class="fas fa-envelope"></i> Contact Support
                        </a>
                        
                        <a href="{{ route('logout') }}" 
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                           class="btn btn-outline-secondary">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                        
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    border-radius: 15px;
}

.alert {
    border-radius: 10px;
}

.badge {
    font-size: 0.8rem;
}

.table td {
    padding: 0.25rem 0.5rem;
}

@media (max-width: 768px) {
    .col-md-4 {
        margin-bottom: 1rem;
    }
    
    .btn {
        margin-bottom: 0.5rem;
    }
}
</style>
@endsection
