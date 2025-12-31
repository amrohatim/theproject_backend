@extends('layouts.dashboard')

@section('title', __('messages.branches_management'))
@section('page-title', __('messages.branches_management'))

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('styles')
<style>
    @media (max-width: 768px) {
        .responsive-table thead {
            display: none;
        }

        .responsive-table,
        .responsive-table tbody,
        .responsive-table tr,
        .responsive-table td {
            display: block;
            width: 100%;
        }

        .responsive-table tbody tr {
            margin-bottom: 1rem;
            border: 1px solid #3b82f6 !important;
            border-radius: 0.375rem !important;
            overflow: hidden;
            background-color: #ffffff;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.06);
        }

        .dark .responsive-table tbody tr {
            background-color: #1f2937;
            border-color: #60a5fa !important;
        }

        .responsive-table td {
            position: relative;
            padding: 0.75rem 1rem 0.75rem 9.5rem;
            text-align: left;
            white-space: normal;
        }

        .responsive-table td::before {
            content: attr(data-label);
            position: absolute;
            left: 1rem;
            top: 0.75rem;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #6b7280;
        }

        .dark .responsive-table td::before {
            color: #9ca3af;
        }

        .responsive-table td:last-child {
            text-align: left;
        }
    }
</style>
@endsection

@section('content')
<div class="container mx-auto">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('messages.branches_management') }}</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">{{ __('messages.manage_your_company_branches') }}</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('vendor.branches.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i> {{ __('messages.add_branch') }}
            </a>
        </div>
    </div>

    <!-- Search and filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 p-4 border border-gray-200 dark:border-gray-700">
        <form action="{{ route('vendor.branches.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.search') }}</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" placeholder="{{ __('messages.search_branches') }}">
                    </div>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.status') }}</label>
                    <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="">{{ __('messages.all_status') }}</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>{{ __('messages.inactive') }}</option>
                    </select>
                </div>

                <div>
                    <label for="sort" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.sort_by') }}</label>
                    <select id="sort" name="sort" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="newest" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>{{ __('messages.newest_first') }}</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>{{ __('messages.oldest_first') }}</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>{{ __('messages.name_a_z') }}</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>{{ __('messages.name_z_a') }}</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-filter mr-2"></i> {{ __('messages.filter') }}
                </button>
            </div>
        </form>
    </div>

    <!-- Branches list -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 border border-gray-200 dark:border-gray-700">
        <div class="overflow-x-auto">
            <table class="responsive-table min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('messages.branch') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('messages.location') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Business Type</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('messages.contact') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('messages.products') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('messages.services') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('messages.license_status') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('messages.status') }}</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($branches ?? [] as $branch)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap" data-label="{{ __('messages.branch') }}">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @php
                                        // Use the branch model's accessor method which handles all the fallback logic
                                        $branchImage = $branch->getBranchImageAttribute();
                                    @endphp

                                    @if($branchImage)
                                        <img src="{{ $branchImage }}" alt="{{ $branch->name }}" class="h-10 w-10 rounded-md object-cover">
                                    @else
                                        <div class="h-10 w-10 rounded-md bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                                            <i class="fas fa-store text-indigo-600 dark:text-indigo-400"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $branch->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $branch->branch_code ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap" data-label="{{ __('messages.location') }}">
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $branch->address ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $branch->city ?? '' }}, {{ $branch->state ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap" data-label="Business Type">
                            <div class="text-sm text-gray-900 dark:text-white">
                                @if($branch->business_type)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        <i class="fas fa-industry mr-1"></i>
                                        {{ $branch->business_type }}
                                    </span>
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">Not Set</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap" data-label="{{ __('messages.contact') }}">
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $branch->phone ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $branch->email ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap" data-label="{{ __('messages.products') }}">
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $branch->products_count ?? 0 }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap" data-label="{{ __('messages.services') }}">
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $branch->services_count ?? 0 }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap" data-label="{{ __('messages.license_status') }}">
                            @php
                                $licenseStatus = $branch->getLicenseStatus();
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($licenseStatus == 'active') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @elseif($licenseStatus == 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @elseif($licenseStatus == 'expired') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                @elseif($licenseStatus == 'rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                                @if($licenseStatus == 'active')
                                    <i class="fas fa-check-circle mr-1"></i> {{ __('messages.active') }}
                                @elseif($licenseStatus == 'pending')
                                    <i class="fas fa-clock mr-1"></i> {{ __('messages.pending') }}
                                @elseif($licenseStatus == 'expired')
                                    <i class="fas fa-exclamation-triangle mr-1"></i> {{ __('messages.expired') }}
                                @elseif($licenseStatus == 'rejected')
                                    <i class="fas fa-times-circle mr-1"></i> {{ __('messages.rejected') }}
                                @else
                                    <i class="fas fa-question-circle mr-1"></i> {{ __('messages.no_license') }}
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap" data-label="{{ __('messages.status') }}">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($branch->status == 'active') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">
                                @if($branch->status == 'active')
                                    {{ __('messages.active') }}
                                @else
                                    {{ __('messages.inactive') }}
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium" data-label="{{ __('messages.actions') }}">
                            <a href="{{ route('vendor.branches.show', $branch->id) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 mr-3" title="{{ __('messages.view') }}">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('vendor.branches.edit', $branch->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 mr-3" title="{{ __('messages.edit') }}">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('vendor.branches.destroy', $branch->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300" title="{{ __('messages.delete') }}" onclick="return confirm('{{ __('messages.are_you_sure_delete_branch') }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center justify-center py-4">
                                <i class="fas fa-store text-gray-300 dark:text-gray-600 text-5xl mb-4"></i>
                                <p>{{ __('messages.no_branches_found') }}</p>
                                <a href="{{ route('vendor.branches.create') }}" class="mt-2 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    <i class="fas fa-plus mr-2"></i> {{ __('messages.add_branch') }}
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        @if(method_exists($branches, 'links'))
            {{ $branches->links() }}
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/vendor-autocomplete.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    if (searchInput) {
        new VendorAutoComplete(searchInput, {
            apiUrl: '{{ route('vendor.branches.search-suggestions') }}',
            placeholder: '{{ __('messages.search_branches_addresses_phone') }}'
        });
    }
});
</script>
@endpush
