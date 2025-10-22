@extends('layouts.service-provider')

@section('title', __('messages.booking_calendar'))
@section('page-title', __('messages.booking_calendar'))

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css">
<style>
    .fc-event {
        cursor: pointer;
    }
    .fc-daygrid-day.fc-day-today {
        background-color: rgba(96, 165, 250, 0.1);
    }
    .dark .fc-daygrid-day.fc-day-today {
        background-color: rgba(96, 165, 250, 0.2);
    }
    .fc-header-toolbar {
        margin-bottom: 1.5em !important;
    }
    .fc-toolbar-title {
        font-size: 1.5em !important;
    }
    .fc-button {
        padding: 0.4em 0.65em !important;
        font-size: 0.9em !important;
    }
    .fc-view-harness {
        background-color: white;
    }
    .dark .fc-view-harness {
        background-color: #1f2937;
    }
    .dark .fc-col-header-cell {
        background-color: #374151;
    }
    .dark .fc-scrollgrid, .dark .fc-scrollgrid-section > td, .dark .fc-col-header, .dark .fc-col-header-cell, .dark .fc-daygrid-body, .dark .fc-daygrid-day {
        border-color: #4b5563 !important;
    }
    .dark .fc-daygrid-day-number, .dark .fc-col-header-cell-cushion {
        color: #e5e7eb;
    }
</style>
@endsection

@section('content')
<div class="container mx-auto">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('messages.booking_calendar') }}</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">{{ __('messages.view_manage_bookings') }}</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('service-provider.bookings.index') }}" class="inline-flex items-center px-4 py-2 bg-[#53D2DC] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:opacity-90 active:opacity-80 focus:outline-none focus:ring ring-[#53D2DC]/30 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-list mr-2"></i> {{ __('messages.list_view') }}
            </a>
        </div>
    </div>

    <!-- Filter Controls -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 p-4 border border-gray-200 dark:border-gray-700">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="branch_filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.branch') }}</label>
                <select id="branch_filter" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-[#53D2DC] focus:border-[#53D2DC] sm:text-sm rounded-md">
                    <option value="all">{{ __('messages.all_branches') }}</option>
                    @foreach($branches ?? [] as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="status_filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.status') }}</label>
                <select id="status_filter" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-[#53D2DC] focus:border-[#53D2DC] sm:text-sm rounded-md">
                    <option value="all">{{ __('service_provider.all_status') }}</option>
                    <option value="pending">{{ __('service_provider.booking_status_pending') }}</option>
                    <option value="confirmed">{{ __('service_provider.booking_status_confirmed') }}</option>
                    <option value="in_progress">{{ __('service_provider.booking_status_in_progress') }}</option>
                    <option value="completed">{{ __('service_provider.booking_status_completed') }}</option>
                    <option value="cancelled">{{ __('service_provider.booking_status_cancelled') }}</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Calendar -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border border-gray-200 dark:border-gray-700">
        <div id="calendar"></div>
    </div>

    <!-- Booking Details Modal -->
    <div id="booking-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                {{ __('messages.booking_details') }}
                            </h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.customer') }}:</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white" id="modal-customer">-</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.service') }}:</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white" id="modal-service">-</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.date_time') }}:</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white" id="modal-datetime">-</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.status') }}:</p>
                                    <p class="text-sm font-medium" id="modal-status">-</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <a id="modal-view-link" href="#" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        {{ __('messages.view_details') }}
                    </a>
                    <button type="button" id="modal-close" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        {{ __('messages.close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
@php
    $statusLabels = [
        'pending' => __('service_provider.booking_status_pending'),
        'confirmed' => __('service_provider.booking_status_confirmed'),
        'in_progress' => __('service_provider.booking_status_in_progress'),
        'completed' => __('service_provider.booking_status_completed'),
        'cancelled' => __('service_provider.booking_status_cancelled'),
    ];
@endphp
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const modal = document.getElementById('booking-modal');
        const modalClose = document.getElementById('modal-close');
        const branchFilter = document.getElementById('branch_filter');
        const statusFilter = document.getElementById('status_filter');
        const statusLabels = @json($statusLabels);
        
        // Calendar events data
        let events = @json($calendarEvents ?? []);
        
        // Initialize FullCalendar
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: events,
            eventClick: function(info) {
                // Populate modal with event data
                const [customerName, serviceName] = typeof info.event.title === 'string'
                    ? info.event.title.split(' - ')
                    : ['', ''];
                document.getElementById('modal-customer').textContent = customerName ?? '';
                document.getElementById('modal-service').textContent = serviceName ?? '';
                document.getElementById('modal-datetime').textContent = info.event.start.toLocaleString();
                
                // Set status with appropriate color
                const statusEl = document.getElementById('modal-status');
                const statusClass = info.event.backgroundColor;
                let statusKey = info.event.extendedProps.status ?? 'pending';
                
                if (!info.event.extendedProps.status && statusClass) {
                    if (statusClass.includes('10B981')) statusKey = 'completed';
                    else if (statusClass.includes('3B82F6')) statusKey = 'confirmed';
                    else if (statusClass.includes('A855F7')) statusKey = 'in_progress';
                    else if (statusClass.includes('EF4444')) statusKey = 'cancelled';
                }
                
                statusEl.textContent = statusLabels[statusKey] ?? statusKey;
                statusEl.className = 'text-sm font-medium';
                
                if (statusKey === 'completed') statusEl.classList.add('text-emerald-600', 'dark:text-emerald-400');
                else if (statusKey === 'confirmed') statusEl.classList.add('text-blue-600', 'dark:text-blue-400');
                else if (statusKey === 'in_progress') statusEl.classList.add('text-purple-600', 'dark:text-purple-400');
                else if (statusKey === 'cancelled') statusEl.classList.add('text-red-600', 'dark:text-red-400');
                else statusEl.classList.add('text-amber-600', 'dark:text-amber-400');
                
                // Set view details link
                document.getElementById('modal-view-link').href = info.event.url;
                
                // Show modal
                modal.classList.remove('hidden');
            },
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                meridiem: 'short'
            }
        });
        
        calendar.render();
        
        // Close modal when clicking the close button
        if (modalClose) {
            modalClose.addEventListener('click', function() {
                modal.classList.add('hidden');
            });
        }
        
        // Close modal when clicking outside
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
        });
        
        // Filter events
        function filterEvents() {
            const branchId = branchFilter.value;
            const status = statusFilter.value;
            
            let filteredEvents = events;
            
            if (branchId !== 'all') {
                filteredEvents = filteredEvents.filter(event => {
                    const eventBranchId = event.branchId || (event.extendedProps && event.extendedProps.branch_id) || null;
                    return String(eventBranchId) === String(branchId);
                });
            }
            
            if (status !== 'all') {
                filteredEvents = filteredEvents.filter(event => {
                    const color = event.backgroundColor;
                    const eventStatus = event.status || (event.extendedProps && event.extendedProps.status) || null;

                    if (eventStatus) {
                        return eventStatus === status;
                    }
                    
                    if (status === 'completed' && color.includes('10B981')) return true;
                    if (status === 'confirmed' && color.includes('3B82F6')) return true;
                    if (status === 'in_progress' && color.includes('A855F7')) return true;
                    if (status === 'cancelled' && color.includes('EF4444')) return true;
                    if (status === 'pending' && color.includes('F59E0B')) return true;
                    
                    return false;
                });
            }
            
            calendar.removeAllEvents();
            calendar.addEventSource(filteredEvents);
        }
        
        // Add event listeners to filters
        branchFilter.addEventListener('change', filterEvents);
        statusFilter.addEventListener('change', filterEvents);
    });
</script>
@endsection
