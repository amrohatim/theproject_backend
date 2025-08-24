@extends('layouts.app')

@section('title', 'Account Pending Approval')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-body text-center py-5">
                    <!-- Icon -->
                    <div class="mb-4">
                        <i class="fas fa-clock text-warning" style="font-size: 4rem;"></i>
                    </div>
                    
                    <!-- Title -->
                    <h2 class="card-title text-warning mb-3">Account Pending Approval</h2>
                    
                    <!-- Message -->
                    <div class="alert alert-info mx-auto" style="max-width: 500px;">
                        <h5 class="alert-heading">
                            <i class="fas fa-info-circle"></i> Your Registration is Under Review
                        </h5>
                        <p class="mb-0">
                            Thank you for registering as a {{ Auth::user()->role ?? 'user' }} with our marketplace. 
                            Your account and documents are currently being reviewed by our team.
                        </p>
                    </div>
                    
                    <!-- Details -->
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
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-clock"></i> Pending
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- What happens next -->
                    <div class="mt-4">
                        <h5>
                            <i class="fas fa-list-check"></i> What Happens Next?
                        </h5>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="text-center">
                                    <i class="fas fa-search text-primary mb-2" style="font-size: 2rem;"></i>
                                    <h6>Document Review</h6>
                                    <p class="small text-muted">Our team reviews your submitted documents and business information</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <i class="fas fa-check-circle text-success mb-2" style="font-size: 2rem;"></i>
                                    <h6>Approval Decision</h6>
                                    <p class="small text-muted">You'll receive an email notification with our decision</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <i class="fas fa-rocket text-info mb-2" style="font-size: 2rem;"></i>
                                    <h6>Get Started</h6>
                                    <p class="small text-muted">Once approved, you can access all marketplace features</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Timeline -->
                    <div class="alert alert-light mt-4">
                        <h6>
                            <i class="fas fa-clock"></i> Expected Timeline
                        </h6>
                        <p class="mb-0">
                            Most applications are reviewed within <strong>1-3 business days</strong>. 
                            You'll receive an email notification once your account has been reviewed.
                        </p>
                    </div>
                    
                    <!-- Contact Information -->
                    <div class="mt-4">
                        <h6>
                            <i class="fas fa-question-circle"></i> Need Help?
                        </h6>
                        <p class="text-muted">
                            If you have questions about your application or need to update your information, 
                            please contact our support team:
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
                    </div>
                    
                    <!-- Actions -->
                    <div class="mt-4">
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
}
</style>
@endsection
