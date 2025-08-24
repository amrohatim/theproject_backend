@extends('layouts.dashboard')

@section('title', 'Pending Registrations')
@section('page-title', 'Pending Registrations')

@section('styles')
<style>
    .registration-card {
        transition: all 0.3s ease;
    }
    .registration-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    .status-badge {
        @apply px-3 py-1 rounded-full text-xs font-semibold;
    }
    .status-pending {
        @apply bg-yellow-100 text-yellow-800;
    }
    .status-approved {
        @apply bg-green-100 text-green-800;
    }
    .status-rejected {
        @apply bg-red-100 text-red-800;
    }
    .vendor-badge {
        @apply bg-purple-100 text-purple-800;
    }
    .provider-badge {
        @apply bg-pink-100 text-pink-800;
    }
</style>
@endsection

@section('content')
<div class="container mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Pending Registrations</h2>
                <p class="text-gray-600 dark:text-gray-400">Review and approve vendor and provider registrations</p>
            </div>
            <div class="flex space-x-2">
                <button onclick="filterRegistrations('all')" class="filter-btn active" data-filter="all">
                    All ({{ $totalCount }})
                </button>
                <button onclick="filterRegistrations('vendor')" class="filter-btn" data-filter="vendor">
                    Vendors ({{ $vendorCount }})
                </button>
                <button onclick="filterRegistrations('provider')" class="filter-btn" data-filter="provider">
                    Providers ({{ $providerCount }})
                </button>
                <button onclick="filterRegistrations('pending')" class="filter-btn" data-filter="pending">
                    Pending ({{ $pendingCount }})
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Total Registrations -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Registrations</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalCount }}</p>
                </div>
                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                    <i class="fas fa-users text-blue-500 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Pending Approvals -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pending Approval</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $pendingCount }}</p>
                </div>
                <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900">
                    <i class="fas fa-clock text-yellow-500 dark:text-yellow-400 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Vendors -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Vendor Registrations</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $vendorCount }}</p>
                </div>
                <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900">
                    <i class="fas fa-store text-purple-500 dark:text-purple-400 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Providers -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Provider Registrations</p>
                    <p class="text-2xl font-bold text-pink-600">{{ $providerCount }}</p>
                </div>
                <div class="p-3 rounded-full bg-pink-100 dark:bg-pink-900">
                    <i class="fas fa-hands-helping text-pink-500 dark:text-pink-400 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <div class="flex-1 max-w-md">
                <div class="relative">
                    <input type="text" id="search-input" placeholder="Search by name, email, or company..."
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
            </div>
            <div class="flex space-x-2">
                <select id="status-filter" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
                <select id="type-filter" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    <option value="">All Types</option>
                    <option value="vendor">Vendors</option>
                    <option value="provider">Providers</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Registrations List -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" id="registrations-container">
        @forelse($registrations as $registration)
            <div class="registration-card bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6"
                 data-type="{{ $registration->role }}"
                 data-status="{{ $registration->registration_status }}"
                 data-search="{{ strtolower($registration->name . ' ' . $registration->email . ' ' . ($registration->company->name ?? '') . ' ' . ($registration->provider->business_name ?? '')) }}">

                <!-- Header -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-r {{ $registration->role === 'vendor' ? 'from-purple-500 to-indigo-500' : 'from-pink-500 to-rose-500' }} flex items-center justify-center text-white">
                            <i class="fas {{ $registration->role === 'vendor' ? 'fa-store' : 'fa-hands-helping' }} text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $registration->name }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $registration->email }}</p>
                        </div>
                    </div>
                    <div class="flex flex-col items-end space-y-2">
                        <span class="status-badge {{ $registration->registration_status === 'pending' ? 'status-pending' : ($registration->registration_status === 'approved' ? 'status-approved' : 'status-rejected') }}">
                            {{ ucfirst($registration->registration_status) }}
                        </span>
                        <span class="status-badge {{ $registration->role === 'vendor' ? 'vendor-badge' : 'provider-badge' }}">
                            {{ ucfirst($registration->role) }}
                        </span>
                    </div>
                </div>

                <!-- Registration Details -->
                <div class="space-y-3 mb-4">
                    @if($registration->role === 'vendor' && $registration->company)
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <i class="fas fa-building mr-2 w-4"></i>
                            <span>{{ $registration->company->name }}</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <i class="fas fa-map-marker-alt mr-2 w-4"></i>
                            <span>{{ $registration->company->city }}, {{ $registration->company->emirate }}</span>
                        </div>
                        @if($registration->company->delivery_capability)
                            <div class="flex items-center text-sm text-green-600">
                                <i class="fas fa-truck mr-2 w-4"></i>
                                <span>Offers delivery services</span>
                            </div>
                        @endif
                    @elseif($registration->role === 'provider' && $registration->provider)
                        @if($registration->provider->business_name)
                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <i class="fas fa-briefcase mr-2 w-4"></i>
                                <span>{{ $registration->provider->business_name }}</span>
                            </div>
                        @endif
                        @if($registration->provider->deliver_to_vendor_capability)
                            <div class="flex items-center text-sm text-green-600">
                                <i class="fas fa-truck mr-2 w-4"></i>
                                <span>Can deliver to vendor locations</span>
                            </div>
                        @endif
                    @endif

                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                        <i class="fas fa-phone mr-2 w-4"></i>
                        <span>{{ $registration->phone }}</span>
                    </div>

                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                        <i class="fas fa-calendar mr-2 w-4"></i>
                        <span>Registered {{ $registration->created_at->diffForHumans() }}</span>
                    </div>
                </div>

                <!-- License Information -->
                @if($registration->licenses->count() > 0)
                    <div class="mb-4">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">License Documents</h4>
                        <div class="space-y-2">
                            @foreach($registration->licenses as $license)
                                <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                    <div class="flex items-center">
                                        <i class="fas fa-file-pdf text-red-500 mr-2"></i>
                                        <span class="text-sm text-gray-700 dark:text-gray-300">License Document</span>
                                    </div>
                                    <a href="{{ route('admin.registrations.download-license', $license->id) }}"
                                       class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
                                        Download
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Email & Phone Verification Status -->
                <div class="mb-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Email Verified:</span>
                        <span class="flex items-center {{ $registration->email_verified_at ? 'text-green-600' : 'text-red-600' }}">
                            <i class="fas {{ $registration->email_verified_at ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                            {{ $registration->email_verified_at ? 'Yes' : 'No' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-sm mt-1">
                        <span class="text-gray-600 dark:text-gray-400">Phone Verified:</span>
                        <span class="flex items-center {{ $registration->phone_verified_at ? 'text-green-600' : 'text-red-600' }}">
                            <i class="fas {{ $registration->phone_verified_at ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                            {{ $registration->phone_verified_at ? 'Yes' : 'No' }}
                        </span>
                    </div>
                </div>

                <!-- Action Buttons -->
                @if($registration->registration_status === 'pending')
                    <div class="flex space-x-2">
                        <button onclick="viewRegistration({{ $registration->id }})"
                                class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-eye mr-2"></i>View Details
                        </button>
                        <button onclick="approveRegistration({{ $registration->id }})"
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-check mr-2"></i>Approve
                        </button>
                        <button onclick="rejectRegistration({{ $registration->id }})"
                                class="flex-1 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-times mr-2"></i>Reject
                        </button>
                    </div>
                @else
                    <div class="flex space-x-2">
                        <button onclick="viewRegistration({{ $registration->id }})"
                                class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-eye mr-2"></i>View Details
                        </button>
                        @if($registration->registration_status === 'approved')
                            <button onclick="rejectRegistration({{ $registration->id }})"
                                    class="flex-1 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                                <i class="fas fa-times mr-2"></i>Revoke
                            </button>
                        @else
                            <button onclick="approveRegistration({{ $registration->id }})"
                                    class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                                <i class="fas fa-check mr-2"></i>Approve
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No registrations found</h3>
                <p class="text-gray-600 dark:text-gray-400">There are no pending registrations at the moment.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($registrations->hasPages())
        <div class="mt-8">
            {{ $registrations->links() }}
        </div>
    @endif
</div>

<!-- Approval/Rejection Modal -->
<div id="actionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white" id="modalTitle">Confirm Action</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 dark:text-gray-400" id="modalMessage">
                    Are you sure you want to perform this action?
                </p>
                <div class="mt-4" id="rejectionReasonContainer" style="display: none;">
                    <label for="rejectionReason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Reason for rejection (optional):
                    </label>
                    <textarea id="rejectionReason" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                              placeholder="Provide a reason for rejection..."></textarea>
                </div>
            </div>
            <div class="flex items-center justify-end px-4 py-3 space-x-2">
                <button onclick="closeModal()"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors duration-200">
                    Cancel
                </button>
                <button id="confirmButton" onclick="confirmAction()"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors duration-200">
                    Confirm
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let currentAction = null;
    let currentRegistrationId = null;

    // Filter functionality
    function filterRegistrations(type) {
        const buttons = document.querySelectorAll('.filter-btn');
        buttons.forEach(btn => btn.classList.remove('active'));
        document.querySelector(`[data-filter="${type}"]`).classList.add('active');

        const cards = document.querySelectorAll('.registration-card');
        cards.forEach(card => {
            const cardType = card.dataset.type;
            const cardStatus = card.dataset.status;

            let show = false;
            if (type === 'all') {
                show = true;
            } else if (type === 'pending') {
                show = cardStatus === 'pending';
            } else {
                show = cardType === type;
            }

            card.style.display = show ? 'block' : 'none';
        });
    }

    // Search functionality
    document.getElementById('search-input').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const cards = document.querySelectorAll('.registration-card');

        cards.forEach(card => {
            const searchData = card.dataset.search;
            const matches = searchData.includes(searchTerm);
            card.style.display = matches ? 'block' : 'none';
        });
    });

    // Status filter
    document.getElementById('status-filter').addEventListener('change', function(e) {
        const status = e.target.value;
        const cards = document.querySelectorAll('.registration-card');

        cards.forEach(card => {
            const cardStatus = card.dataset.status;
            const matches = !status || cardStatus === status;
            card.style.display = matches ? 'block' : 'none';
        });
    });

    // Type filter
    document.getElementById('type-filter').addEventListener('change', function(e) {
        const type = e.target.value;
        const cards = document.querySelectorAll('.registration-card');

        cards.forEach(card => {
            const cardType = card.dataset.type;
            const matches = !type || cardType === type;
            card.style.display = matches ? 'block' : 'none';
        });
    });

    // Modal functions
    function viewRegistration(id) {
        window.location.href = `/admin/registrations/${id}`;
    }

    function approveRegistration(id) {
        currentAction = 'approve';
        currentRegistrationId = id;
        document.getElementById('modalTitle').textContent = 'Approve Registration';
        document.getElementById('modalMessage').textContent = 'Are you sure you want to approve this registration? The user will be notified via email.';
        document.getElementById('rejectionReasonContainer').style.display = 'none';
        document.getElementById('confirmButton').className = 'px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors duration-200';
        document.getElementById('confirmButton').textContent = 'Approve';
        document.getElementById('actionModal').classList.remove('hidden');
    }

    function rejectRegistration(id) {
        currentAction = 'reject';
        currentRegistrationId = id;
        document.getElementById('modalTitle').textContent = 'Reject Registration';
        document.getElementById('modalMessage').textContent = 'Are you sure you want to reject this registration? The user will be notified via email.';
        document.getElementById('rejectionReasonContainer').style.display = 'block';
        document.getElementById('confirmButton').className = 'px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors duration-200';
        document.getElementById('confirmButton').textContent = 'Reject';
        document.getElementById('actionModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('actionModal').classList.add('hidden');
        document.getElementById('rejectionReason').value = '';
        currentAction = null;
        currentRegistrationId = null;
    }

    function confirmAction() {
        if (!currentAction || !currentRegistrationId) return;

        const reason = document.getElementById('rejectionReason').value;
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        formData.append('_method', 'PATCH');

        if (currentAction === 'reject' && reason) {
            formData.append('reason', reason);
        }

        const url = `/admin/registrations/${currentRegistrationId}/${currentAction}`;

        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Something went wrong'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });

        closeModal();
    }

    // Close modal when clicking outside
    document.getElementById('actionModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>
@endsection