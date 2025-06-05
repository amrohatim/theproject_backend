@extends('layouts.dashboard')

@section('title', 'Booking Calendar')
@section('page-title', 'Booking Calendar')

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
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Booking Calendar</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">View and manage your service bookings</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('vendor.bookings.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-list mr-2"></i> List View
            </a>
        </div>
    </div>

    <!-- Filter Controls -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 p-4 border border-gray-200 dark:border-gray-700">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="branch_filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Branch</label>
                <select id="branch_filter" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="all">All Branches</option>
                    @foreach($branches ?? [] as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="status_filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                <select id="status_filter" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="all">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="no_show">No Show</option>
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
                                Booking Details
                            </h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Customer:</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white" id="modal-customer">-</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Service:</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white" id="modal-service">-</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Date & Time:</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white" id="modal-datetime">-</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Status:</p>
                                    <p class="text-sm font-medium" id="modal-status">-</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <a id="modal-view-link" href="#" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        View Details
                    </a>
                    <button type="button" id="modal-close" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const modal = document.getElementById('booking-modal');
        const modalClose = document.getElementById('modal-close');
        const branchFilter = document.getElementById('branch_filter');
        const statusFilter = document.getElementById('status_filter');
        
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
                document.getElementById('modal-customer').textContent = info.event.title.split(' - ')[0];
                document.getElementById('modal-service').textContent = info.event.title.split(' - ')[1];
                document.getElementById('modal-datetime').textContent = info.event.start.toLocaleString();
                
                // Set status with appropriate color
                const statusEl = document.getElementById('modal-status');
                const statusClass = info.event.backgroundColor;
                let status = 'Pending';
                
                if (statusClass.includes('10B981')) status = 'Completed';
                else if (statusClass.includes('3B82F6')) status = 'Confirmed';
                else if (statusClass.includes('EF4444')) status = 'Cancelled';
                else if (statusClass.includes('6B7280')) status = 'No Show';
                
                statusEl.textContent = status;
                statusEl.className = 'text-sm font-medium';
                
                if (status === 'Completed') statusEl.classList.add('text-green-600', 'dark:text-green-400');
                else if (status === 'Confirmed') statusEl.classList.add('text-blue-600', 'dark:text-blue-400');
                else if (status === 'Cancelled') statusEl.classList.add('text-red-600', 'dark:text-red-400');
                else if (status === 'No Show') statusEl.classList.add('text-gray-600', 'dark:text-gray-400');
                else statusEl.classList.add('text-yellow-600', 'dark:text-yellow-400');
                
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
        modalClose.addEventListener('click', function() {
            modal.classList.add('hidden');
        });
        
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
                // This is a simplified example. In a real app, you would need to have branch IDs in your event data
                // filteredEvents = filteredEvents.filter(event => event.branchId === branchId);
            }
            
            if (status !== 'all') {
                filteredEvents = filteredEvents.filter(event => {
                    const color = event.backgroundColor;
                    
                    if (status === 'completed' && color.includes('10B981')) return true;
                    if (status === 'confirmed' && color.includes('3B82F6')) return true;
                    if (status === 'cancelled' && color.includes('EF4444')) return true;
                    if (status === 'no_show' && color.includes('6B7280')) return true;
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
