@extends('layouts.dashboard')

@section('title', 'Business Hours')
@section('page-title', 'Business Hours')

@section('content')
<div class="container mx-auto">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Business Hours</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">Set your business hours and availability for bookings</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('vendor.settings') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i> Back to Settings
            </a>
        </div>
    </div>

    <!-- Branch Selection -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700 mb-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Select Branch</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Choose a branch to set business hours.</p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="branch" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Branch</label>
                <select id="branch" name="branch" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="1">Main Branch</option>
                    <option value="2">Downtown Branch</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Business Hours -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700 mb-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Business Hours</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Set your regular business hours for each day of the week.</p>
        
        <form action="#" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <!-- Monday -->
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center">
                            <input id="monday_open" name="days_open[monday]" type="checkbox" checked value="1" class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300 dark:border-gray-600 rounded">
                            <label for="monday_open" class="ml-2 text-sm font-medium text-gray-900 dark:text-white">Monday</label>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Open</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="monday_start" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Opening Time</label>
                            <input type="time" name="opening_hours[monday][open]" id="monday_start" value="09:00" class="mt-1 focus:ring-yellow-500 focus:border-yellow-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div>
                        <div>
                            <label for="monday_end" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Closing Time</label>
                            <input type="time" name="opening_hours[monday][close]" id="monday_end" value="17:00" class="mt-1 focus:ring-yellow-500 focus:border-yellow-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div>
                    </div>
                </div>
                
                <!-- Tuesday -->
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center">
                            <input id="tuesday_open" name="days_open[tuesday]" type="checkbox" checked value="1" class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300 dark:border-gray-600 rounded">
                            <label for="tuesday_open" class="ml-2 text-sm font-medium text-gray-900 dark:text-white">Tuesday</label>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Open</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="tuesday_start" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Opening Time</label>
                            <input type="time" name="opening_hours[tuesday][open]" id="tuesday_start" value="09:00" class="mt-1 focus:ring-yellow-500 focus:border-yellow-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div>
                        <div>
                            <label for="tuesday_end" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Closing Time</label>
                            <input type="time" name="opening_hours[tuesday][close]" id="tuesday_end" value="17:00" class="mt-1 focus:ring-yellow-500 focus:border-yellow-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div>
                    </div>
                </div>
                
                <!-- Wednesday -->
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center">
                            <input id="wednesday_open" name="days_open[wednesday]" type="checkbox" checked value="1" class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300 dark:border-gray-600 rounded">
                            <label for="wednesday_open" class="ml-2 text-sm font-medium text-gray-900 dark:text-white">Wednesday</label>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Open</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="wednesday_start" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Opening Time</label>
                            <input type="time" name="opening_hours[wednesday][open]" id="wednesday_start" value="09:00" class="mt-1 focus:ring-yellow-500 focus:border-yellow-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div>
                        <div>
                            <label for="wednesday_end" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Closing Time</label>
                            <input type="time" name="opening_hours[wednesday][close]" id="wednesday_end" value="17:00" class="mt-1 focus:ring-yellow-500 focus:border-yellow-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div>
                    </div>
                </div>
                
                <!-- Thursday -->
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center">
                            <input id="thursday_open" name="days_open[thursday]" type="checkbox" checked value="1" class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300 dark:border-gray-600 rounded">
                            <label for="thursday_open" class="ml-2 text-sm font-medium text-gray-900 dark:text-white">Thursday</label>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Open</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="thursday_start" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Opening Time</label>
                            <input type="time" name="opening_hours[thursday][open]" id="thursday_start" value="09:00" class="mt-1 focus:ring-yellow-500 focus:border-yellow-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div>
                        <div>
                            <label for="thursday_end" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Closing Time</label>
                            <input type="time" name="opening_hours[thursday][close]" id="thursday_end" value="17:00" class="mt-1 focus:ring-yellow-500 focus:border-yellow-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div>
                    </div>
                </div>
                
                <!-- Friday -->
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center">
                            <input id="friday_open" name="days_open[friday]" type="checkbox" checked value="1" class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300 dark:border-gray-600 rounded">
                            <label for="friday_open" class="ml-2 text-sm font-medium text-gray-900 dark:text-white">Friday</label>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Open</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="friday_start" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Opening Time</label>
                            <input type="time" name="opening_hours[friday][open]" id="friday_start" value="09:00" class="mt-1 focus:ring-yellow-500 focus:border-yellow-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div>
                        <div>
                            <label for="friday_end" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Closing Time</label>
                            <input type="time" name="opening_hours[friday][close]" id="friday_end" value="17:00" class="mt-1 focus:ring-yellow-500 focus:border-yellow-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div>
                    </div>
                </div>
                
                <!-- Saturday -->
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center">
                            <input id="saturday_open" name="days_open[saturday]" type="checkbox" value="1" class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300 dark:border-gray-600 rounded">
                            <label for="saturday_open" class="ml-2 text-sm font-medium text-gray-900 dark:text-white">Saturday</label>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Closed</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="saturday_start" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Opening Time</label>
                            <input type="time" name="opening_hours[saturday][open]" id="saturday_start" value="10:00" class="mt-1 focus:ring-yellow-500 focus:border-yellow-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div>
                        <div>
                            <label for="saturday_end" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Closing Time</label>
                            <input type="time" name="opening_hours[saturday][close]" id="saturday_end" value="15:00" class="mt-1 focus:ring-yellow-500 focus:border-yellow-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div>
                    </div>
                </div>
                
                <!-- Sunday -->
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center">
                            <input id="sunday_open" name="days_open[sunday]" type="checkbox" value="1" class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300 dark:border-gray-600 rounded">
                            <label for="sunday_open" class="ml-2 text-sm font-medium text-gray-900 dark:text-white">Sunday</label>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Closed</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="sunday_start" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Opening Time</label>
                            <input type="time" name="opening_hours[sunday][open]" id="sunday_start" value="10:00" class="mt-1 focus:ring-yellow-500 focus:border-yellow-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div>
                        <div>
                            <label for="sunday_end" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Closing Time</label>
                            <input type="time" name="opening_hours[sunday][close]" id="sunday_end" value="15:00" class="mt-1 focus:ring-yellow-500 focus:border-yellow-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:border-yellow-900 focus:ring ring-yellow-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-save mr-2"></i> Save Business Hours
                </button>
            </div>
        </form>
    </div>

    <!-- Special Hours -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Special Hours</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Set special hours for holidays or other events.</p>
        
        <div class="flex justify-end mb-4">
            <button type="button" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:border-yellow-900 focus:ring ring-yellow-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i> Add Special Hours
            </button>
        </div>
        
        <div class="space-y-4">
            <!-- Christmas -->
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                <div class="flex items-center justify-between mb-2">
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">Christmas Day</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">December 25, 2023</p>
                    </div>
                    <div>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                            Closed
                        </span>
                    </div>
                </div>
                <div class="flex justify-end mt-2">
                    <button type="button" class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-md text-xs font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Edit
                    </button>
                    <button type="button" class="ml-2 inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-md text-xs font-medium text-red-700 dark:text-red-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Remove
                    </button>
                </div>
            </div>
            
            <!-- New Year's Day -->
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                <div class="flex items-center justify-between mb-2">
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">New Year's Day</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">January 1, 2024</p>
                    </div>
                    <div>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                            Closed
                        </span>
                    </div>
                </div>
                <div class="flex justify-end mt-2">
                    <button type="button" class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-md text-xs font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Edit
                    </button>
                    <button type="button" class="ml-2 inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-md text-xs font-medium text-red-700 dark:text-red-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Remove
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle day open/closed
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const day = this.id.split('_')[0];
            const statusSpan = this.closest('.flex').nextElementSibling.querySelector('span');
            const timeInputs = this.closest('.bg-gray-50, .dark\\:bg-gray-700').querySelectorAll('input[type="time"]');
            
            if (this.checked) {
                statusSpan.textContent = 'Open';
                timeInputs.forEach(input => {
                    input.disabled = false;
                });
            } else {
                statusSpan.textContent = 'Closed';
                timeInputs.forEach(input => {
                    input.disabled = true;
                });
            }
        });
        
        // Initialize on page load
        checkbox.dispatchEvent(new Event('change'));
    });
</script>
@endsection
