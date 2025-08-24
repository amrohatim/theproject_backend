@extends('layouts.dashboard')

@section('title', 'Post-Rejection User Management')
@section('page-title', 'Post-Rejection User Management')

@section('styles')
<style>
    .status-card {
        transition: all 0.3s ease;
    }
    .status-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    .action-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .action-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    .success-gradient {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }
    .danger-gradient {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }
</style>
@endsection

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
                <div class="flex items-center px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                    <i class="fas fa-check-circle mr-2"></i>
                    Rejection Complete
                </div>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                <div class="text-green-800">
                    <p class="font-medium">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Status Alert -->
    <div class="mb-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-500 text-xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-blue-900">License Rejection Successful</h3>
                <p class="mt-2 text-blue-800">The vendor license has been rejected and an email notification has been sent to the user.</p>
            </div>
        </div>
    </div>

    <!-- Information Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- User Information Card -->
        <div class="status-card bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="bg-black/70 px-6 py-4">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-user mr-3"></i>
                    User Information
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="font-medium text-gray-600 dark:text-gray-400">Name:</span>
                        <span class="text-gray-900 dark:text-white font-semibold">{{ $user->name }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="font-medium text-gray-600 dark:text-gray-400">Email:</span>
                        <span class="text-gray-900 dark:text-white">{{ $user->email }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="font-medium text-gray-600 dark:text-gray-400">Role:</span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                            {{ ucfirst($user->role) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="font-medium text-gray-600 dark:text-gray-400">Registration Date:</span>
                        <span class="text-gray-900 dark:text-white">{{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="font-medium text-gray-600 dark:text-gray-400">Status:</span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            {{ ucfirst($user->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- License Information Card -->
        <div class="status-card bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="bg-black/70 px-6 py-4">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-certificate mr-3"></i>
                    License Information
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="font-medium text-gray-600 dark:text-gray-400">License ID:</span>
                        <span class="text-gray-900 dark:text-white font-semibold">#{{ $license->id }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="font-medium text-gray-600 dark:text-gray-400">Status:</span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            {{ ucfirst($license->status) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="font-medium text-gray-600 dark:text-gray-400">Submitted:</span>
                        <span class="text-gray-900 dark:text-white">{{ $license->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="font-medium text-gray-600 dark:text-gray-400">Rejected:</span>
                        <span class="text-gray-900 dark:text-white">{{ $license->updated_at->format('M d, Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Decision Section -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-8">
        <div class="bg-black/70 px-6 py-4">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-exclamation-triangle mr-3"></i>
                What would you like to do with this user?
            </h3>
        </div>
        <div class="p-6">
            <div class="mb-6">
                <p class="text-gray-600 dark:text-gray-400 text-center">
                    Since the license has been rejected, you can choose to either keep the user account
                    (allowing them to resubmit their license) or permanently remove the user and all associated data.
                </p>
            </div>

            <form action="{{ route('admin.vendor-licenses.handle-post-rejection-choice') }}" method="POST" id="userActionForm">
                @csrf
                <input type="hidden" name="user_id" value="{{ $user->id }}">
                <input type="hidden" name="license_id" value="{{ $license->id }}">
                <input type="hidden" name="action" id="actionInput">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Keep User Card -->
                    <div class="action-card bg-white dark:bg-gray-800 rounded-xl shadow-lg border-2 border-green-200 hover:border-green-400 overflow-hidden">
                        <div class="success-gradient p-6 text-center">
                            <div class="w-20 h-20 mx-auto mb-4 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                <i class="fas fa-user-check text-4xl text-white"></i>
                            </div>
                            <h4 class="text-xl font-bold text-white mb-2">Keep User</h4>
                        </div>
                        <div class="p-6 text-center">
                            <p class="text-gray-600 dark:text-gray-400 mb-6">
                                Maintain the user account so they can resubmit their license application.
                            </p>
                            <div class="space-y-3 mb-6">
                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    User can resubmit license
                                </div>
                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    Account data preserved
                                </div>
                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    No data loss
                                </div>
                            </div>
                            <button type="button" class="w-full inline-flex items-center justify-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-green-500 focus:ring-opacity-30" onclick="submitAction('keep')">
                                <i class="fas fa-check mr-2"></i>
                                Keep User Account
                            </button>
                        </div>
                    </div>

                    <!-- Remove User Card -->
                    <div class="action-card bg-white dark:bg-gray-800 rounded-xl shadow-lg border-2 border-red-200 hover:border-red-400 overflow-hidden">
                        <div class="danger-gradient p-6 text-center">
                            <div class="w-20 h-20 mx-auto mb-4 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                <i class="fas fa-user-times text-4xl text-white"></i>
                            </div>
                            <h4 class="text-xl font-bold text-white mb-2">Remove User</h4>
                        </div>
                        <div class="p-6 text-center">
                            <p class="text-gray-600 dark:text-gray-400 mb-6">
                                Permanently delete the user and all associated data from the system.
                            </p>
                            <div class="space-y-3 mb-6">
                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                    <i class="fas fa-times text-red-500 mr-3"></i>
                                    Complete data removal
                                </div>
                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                    <i class="fas fa-times text-red-500 mr-3"></i>
                                    Cannot be undone
                                </div>
                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                    <i class="fas fa-times text-red-500 mr-3"></i>
                                    All files deleted
                                </div>
                            </div>
                            <button type="button" class="w-full inline-flex items-center justify-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-red-500 focus:ring-opacity-30" onclick="confirmRemoval()">
                                <i class="fas fa-trash mr-2"></i>
                                Remove User Account
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Back Button -->
    <div class="flex justify-center">
        <a href="{{ route('admin.vendor-licenses.index') }}" class="inline-flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-gray-500 focus:ring-opacity-30">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Vendor Licenses
        </a>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmRemovalModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 xl:w-2/5 shadow-lg rounded-xl bg-white dark:bg-gray-800">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-red-500 to-red-600 -m-5 mb-4 px-6 py-4 rounded-t-xl">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-exclamation-triangle mr-3"></i>
                    Confirm User Removal
                </h3>
                <button type="button" class="text-white hover:text-gray-200 focus:outline-none" onclick="closeModal()">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <div class="px-2">
            <!-- Warning Alert -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-500 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-yellow-800 font-semibold">Warning: This action cannot be undone!</h4>
                    </div>
                </div>
            </div>

            <!-- Confirmation Text -->
            <div class="mb-6">
                <p class="text-gray-700 dark:text-gray-300 mb-4">
                    Are you sure you want to permanently delete the user <strong class="text-red-600">{{ $user->name }}</strong> and all associated data?
                </p>

                <p class="text-gray-700 dark:text-gray-300 mb-3 font-medium">This will remove:</p>
                <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                    <li class="flex items-center">
                        <i class="fas fa-times text-red-500 mr-3"></i>
                        User account and profile information
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-times text-red-500 mr-3"></i>
                        All license records and documents
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-times text-red-500 mr-3"></i>
                        Any uploaded files (logos, images, etc.)
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-times text-red-500 mr-3"></i>
                        All related vendor/company data
                    </li>
                </ul>

                <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-red-800 font-semibold text-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        This action is irreversible!
                    </p>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200 dark:border-gray-600">
            <button type="button" class="inline-flex items-center px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-gray-500 focus:ring-opacity-30" onclick="closeModal()">
                <i class="fas fa-times mr-2"></i>
                Cancel
            </button>
            <button type="button" class="inline-flex items-center px-6 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-red-500 focus:ring-opacity-30" onclick="submitAction('remove')">
                <i class="fas fa-trash mr-2"></i>
                Yes, Remove User
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function submitAction(action) {
    document.getElementById('actionInput').value = action;
    document.getElementById('userActionForm').submit();
}

function confirmRemoval() {
    document.getElementById('confirmRemovalModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('confirmRemovalModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('confirmRemovalModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});
</script>
@endsection
