@extends('layouts.service-provider')

@section('title', 'Services')
@section('page-title', 'Services')

@section('content')
<div class="container mx-auto">
    <!-- Filters -->
    <div class="mb-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div>
                <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Service name..."
                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-0 focus:border-[#53D2DC]" />
            </div>
            <div>
                <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Branch</label>
                <select name="branch_id" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-0 focus:border-[#53D2DC]">
                    <option value="">All</option>
                    @foreach(($branches ?? []) as $branch)
                        <option value="{{ $branch->id }}" @selected(request('branch_id') == $branch->id)>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button class="inline-flex items-center px-4 py-2 bg-[#53D2DC] text-white rounded-md hover:opacity-90 active:opacity-80">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- List -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Assigned Services</h3>
            <a href="{{ route('service-provider.services.create') }}" class="inline-flex items-center px-3 py-2 bg-[#53D2DC] text-white rounded-md hover:opacity-90 active:opacity-80 text-sm">
                <i class="fas fa-plus mr-2"></i>Add Service
            </a>
        </div>
        @if(($services ?? collect())->count())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Service</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Branch</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Price</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Duration</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($services as $service)
                            <tr>
                                <td class="px-4 py-3 text-gray-900 dark:text-white">{{ $service->name }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $service->branch->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">${{ number_format($service->price, 2) }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $service->duration }} min</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('service-provider.services.show', $service) }}" class="text-[#53D2DC] hover:underline text-sm mr-3">View</a>
                                    <a href="{{ route('service-provider.services.edit', $service) }}" class="text-[#53D2DC] hover:underline text-sm">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">{{ $services->links() }}</div>
        @else
            <div class="p-8 text-center">
                <div class="mx-auto h-14 w-14 rounded-full bg-[#53D2DC]/15 flex items-center justify-center">
                    <i class="fas fa-cog text-[#53D2DC]"></i>
                </div>
                <h4 class="mt-3 text-gray-900 dark:text-white font-medium">No services found</h4>
                <p class="text-sm text-gray-500 dark:text-gray-400">Assigned services will appear here.</p>
            </div>
        @endif
    </div>
</div>
@endsection
