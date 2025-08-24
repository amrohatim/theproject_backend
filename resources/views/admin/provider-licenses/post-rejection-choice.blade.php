@extends('layouts.dashboard')

@section('title', 'Post-Rejection User Management')

@push('styles')
<style>
.success-gradient {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.danger-gradient {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
}

.action-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.action-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.info-card {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
}

.warning-gradient {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.pulse-animation {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: .8;
    }
}

.modal-backdrop-blur {
    backdrop-filter: blur(4px);
    background-color: rgba(0, 0, 0, 0.5);
}
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">License Rejection Completed</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">Manage the user account after license rejection</p>
            </div>
            <div class="flex items-center space-x-2">
                <div class="flex items-center px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-medium pulse-animation">
                    <i class="fas fa-check-circle mr-2"></i>
                    Rejection Complete
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                <p class="text-green-800 font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <!-- Success Notification -->
    <div class="mb-8 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="info-card px-6 py-4">
            <div class="flex items-center justify-center text-white">
                
                <div>
                    <h3 class="text-lg font-semibold">License Rejection Successful</h3>
                    <p class="text-blue-800 mb-5 font-medium text-lg">The provider license has been rejected and an email notification has been sent to the user.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Information Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- User Information Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-user mr-3"></i>
                    User Information
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $user->name }}</span>
                    </div>
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $user->email }}</span>
                    </div>
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Role</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ ucfirst($user->role) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Registration Date</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            <i class="fas fa-circle text-xs mr-1"></i>
                            {{ ucfirst($user->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- License Information Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-certificate mr-3"></i>
                    License Information
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">License ID</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">#{{ $license->id }}</span>
                    </div>
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $license->status === 'active' ? 'bg-green-100 text-green-800' : ($license->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                            <i class="fas fa-circle text-xs mr-1"></i>
                            {{ ucfirst($license->status) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Submitted</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $license->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between py-3 {{ $license->notes ? 'border-b border-gray-100 dark:border-gray-700' : '' }}">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Rejected</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $license->updated_at->format('M d, Y H:i') }}</span>
                    </div>
                    @if($license->notes)
                    <div class="py-3">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400 block mb-2">Rejection Reason</span>
                        <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                            <p class="text-sm text-red-800">{{ $license->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Decision Section -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-8">
        <div class="warning-gradient px-6 py-4">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-exclamation-triangle mr-3"></i>
                What would you like to do with this user?
            </h3>
        </div>
        <div class="p-6">
            <div class="mb-6">
                <p class="text-gray-600 dark:text-gray-400 text-base leading-relaxed">
                    Since the license has been rejected, you can choose to either keep the user account
                    (allowing them to resubmit their license) or permanently remove the user and all associated data.
                </p>
            </div>

            <form action="{{ route('admin.provider-licenses.handle-post-rejection-choice') }}" method="POST" id="userActionForm">
                @csrf
                <input type="hidden" name="user_id" value="{{ $user->id }}">
                <input type="hidden" name="license_id" value="{{ $license->id }}">
                <input type="hidden" name="action" id="actionInput">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Keep User Option -->
                    <div class="action-card bg-white dark:bg-gray-800 rounded-xl border-2 border-green-200 hover:border-green-300 shadow-sm p-6 text-center">
                        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-user-check text-3xl text-green-600"></i>
                        </div>
                        <h4 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Keep User</h4>
                        <p class="text-gray-600 dark:text-gray-400 mb-6 leading-relaxed">
                            Maintain the user account so they can resubmit their license application with corrected documents.
                        </p>
                        <button type="button"
                                class="w-full success-gradient text-white  bg-green-600 hover:bg-green-700 font-semibold py-3 px-6 rounded-lg hover:shadow-lg transform hover:scale-105 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-green-300"
                                onclick="submitAction('keep')">
                            <i class="fas fa-check mr-2"></i>
                            Keep User Account
                        </button>
                    </div>

                    <!-- Remove User Option -->
                    <div class="action-card bg-white dark:bg-gray-800 rounded-xl border-2 border-red-200 hover:border-red-300 shadow-sm p-6 text-center">
                        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-user-times text-3xl text-red-600"></i>
                        </div>
                        <h4 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Remove User</h4>
                        <p class="text-gray-600 dark:text-gray-400 mb-6 leading-relaxed">
                            Permanently delete the user and all associated data from the system. This action cannot be undone.
                        </p>
                        <button type="button"
                                class="w-full danger-gradient bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg hover:shadow-lg transform hover:scale-105 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-red-300"
                                onclick="confirmRemoval()">
                            <i class="fas fa-trash mr-2"></i>
                            Remove User Permanently
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Navigation -->
    <div class="flex justify-center">
        <a href="{{ route('admin.provider-licenses.index') }}"
           class="inline-flex items-center bg-white px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors duration-200 focus:outline-none focus:ring-4 focus:ring-gray-300">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Provider Licenses
        </a>
    </div>
</div>

<!-- Modern Confirmation Modal -->
<div class="fixed inset-0 z-50 hidden overflow-y-auto modal-backdrop-blur" id="confirmRemovalModal" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <!-- Header -->
            <div class="sm:flex sm:items-start">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-16 w-16 rounded-full bg-red-100 sm:mx-0 sm:h-12 sm:w-12">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white" id="modal-title">
                        Confirm User Removal
                    </h3>
                    <div class="mt-4">
                        <!-- Warning Alert -->
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                                <span class="text-red-800 font-semibold">Warning: This action cannot be undone!</span>
                            </div>
                        </div>

                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            Are you sure you want to permanently delete the user <strong class="text-gray-900 dark:text-white">{{ $user->name }}</strong> and all associated data?
                        </p>

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">This will remove:</p>
                            <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                <li class="flex items-center">
                                    <i class="fas fa-circle text-xs text-red-500 mr-2"></i>
                                    User account and profile information
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-circle text-xs text-red-500 mr-2"></i>
                                    All license records and documents
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-circle text-xs text-red-500 mr-2"></i>
                                    Any uploaded files (logos, images, etc.)
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-circle text-xs text-red-500 mr-2"></i>
                                    All related provider data
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-circle text-xs text-red-500 mr-2"></i>
                                    Services created by this provider
                                </li>
                            </ul>
                        </div>

                        <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                            <p class="text-red-800 font-semibold text-center">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                This action is irreversible!
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 sm:mt-6 sm:flex sm:flex-row-reverse sm:space-x-reverse sm:space-x-3">
                <button type="button"
                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-6 py-3 danger-gradient text-white font-semibold hover:shadow-lg focus:outline-none focus:ring-4 focus:ring-red-300 sm:ml-3 sm:w-auto sm:text-sm transition-all duration-200"
                        onclick="submitAction('remove')">
                    <i class="fas fa-trash mr-2"></i>
                    Yes, Remove User
                </button>
                <button type="button"
                        class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-6 py-3 bg-white text-gray-700 font-medium hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-gray-300 sm:mt-0 sm:w-auto sm:text-sm transition-all duration-200"
                        onclick="closeModal()">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function submitAction(action) {
    // Add loading state
    const form = document.getElementById('userActionForm');
    const actionInput = document.getElementById('actionInput');

    actionInput.value = action;

    // Show loading state for the appropriate button
    if (action === 'keep') {
        const keepButton = document.querySelector('button[onclick="submitAction(\'keep\')"]');
        keepButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
        keepButton.disabled = true;
    } else if (action === 'remove') {
        const removeButton = document.querySelector('button[onclick="submitAction(\'remove\')"]');
        removeButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Removing...';
        removeButton.disabled = true;
    }

    form.submit();
}

function confirmRemoval() {
    const modal = document.getElementById('confirmRemovalModal');
    modal.classList.remove('hidden');

    // Focus trap for accessibility
    const focusableElements = modal.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
    const firstElement = focusableElements[0];
    const lastElement = focusableElements[focusableElements.length - 1];

    firstElement.focus();

    // Handle escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
}

function closeModal() {
    const modal = document.getElementById('confirmRemovalModal');
    modal.classList.add('hidden');
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    const modal = document.getElementById('confirmRemovalModal');
    if (e.target === modal) {
        closeModal();
    }
});

// Add smooth animations and interactions
document.addEventListener('DOMContentLoaded', function() {
    // Add hover effects to action cards
    const actionCards = document.querySelectorAll('.action-card');
    actionCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-4px)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Add ripple effect to buttons
    const buttons = document.querySelectorAll('button[class*="gradient"]');
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');

            this.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
});
</script>

<style>
.ripple {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: scale(0);
    animation: ripple-animation 0.6s linear;
    pointer-events: none;
}

@keyframes ripple-animation {
    to {
        transform: scale(4);
        opacity: 0;
    }
}
</style>
@endpush
