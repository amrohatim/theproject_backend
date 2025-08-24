@extends('layouts.dashboard')

@section('title', 'Tax Settings')
@section('page-title', 'Tax Settings')

@section('content')
<div class="container mx-auto">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Tax Settings</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">Configure tax rates and tax-related information</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('vendor.settings') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i> Back to Settings
            </a>
        </div>
    </div>

    <!-- Tax Information -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700 mb-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Tax Information</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Provide your business tax information.</p>
        
        <form action="#" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tax ID -->
                <div>
                    <label for="tax_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tax ID / VAT Number</label>
                    <input type="text" name="tax_id" id="tax_id" value="TAX123456" class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                    @error('tax_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Business Type -->
                <div>
                    <label for="business_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Business Type</label>
                    <select id="business_type" name="business_type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm rounded-md">
                        <option value="sole_proprietorship">Sole Proprietorship</option>
                        <option value="partnership">Partnership</option>
                        <option value="llc" selected>Limited Liability Company (LLC)</option>
                        <option value="corporation">Corporation</option>
                        <option value="non_profit">Non-Profit Organization</option>
                    </select>
                    @error('business_type')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Tax Office -->
                <div>
                    <label for="tax_office" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tax Office</label>
                    <input type="text" name="tax_office" id="tax_office" value="Central Tax Office" class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                    @error('tax_office')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Fiscal Year End -->
                <div>
                    <label for="fiscal_year_end" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fiscal Year End</label>
                    <select id="fiscal_year_end" name="fiscal_year_end" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm rounded-md">
                        <option value="january">January 31</option>
                        <option value="february">February 28/29</option>
                        <option value="march">March 31</option>
                        <option value="april">April 30</option>
                        <option value="may">May 31</option>
                        <option value="june">June 30</option>
                        <option value="july">July 31</option>
                        <option value="august">August 31</option>
                        <option value="september">September 30</option>
                        <option value="october">October 31</option>
                        <option value="november">November 30</option>
                        <option value="december" selected>December 31</option>
                    </select>
                    @error('fiscal_year_end')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-save mr-2"></i> Save Tax Information
                </button>
            </div>
        </form>
    </div>

    <!-- Tax Rates -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700 mb-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Tax Rates</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Configure tax rates for your products and services.</p>
        
        <div class="flex justify-end mb-4">
            <button type="button" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i> Add Tax Rate
            </button>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rate</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">Standard VAT</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">20%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">Percentage</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                Active
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button type="button" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">Edit</button>
                            <button type="button" class="ml-2 text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">Reduced VAT</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">5%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">Percentage</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                Active
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button type="button" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">Edit</button>
                            <button type="button" class="ml-2 text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">Zero Rate</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">0%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">Percentage</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                Active
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button type="button" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">Edit</button>
                            <button type="button" class="ml-2 text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tax Categories -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700 mb-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Tax Categories</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Assign tax rates to different categories of products and services.</p>
        
        <div class="flex justify-end mb-4">
            <button type="button" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i> Add Tax Category
            </button>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Category</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tax Rate</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Description</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">Hair Products</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">Standard VAT (20%)</td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">All hair care products</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button type="button" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">Edit</button>
                            <button type="button" class="ml-2 text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">Salon Services</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">Reduced VAT (5%)</td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">All salon services including haircuts, styling, and coloring</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button type="button" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">Edit</button>
                            <button type="button" class="ml-2 text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">Books & Educational</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">Zero Rate (0%)</td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">Educational materials and books</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button type="button" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">Edit</button>
                            <button type="button" class="ml-2 text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tax Reports -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Tax Reports</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Generate and download tax reports for your business.</p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="report_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Report Type</label>
                <select id="report_type" name="report_type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm rounded-md">
                    <option value="sales_tax">Sales Tax Report</option>
                    <option value="vat">VAT Report</option>
                    <option value="income_tax">Income Tax Report</option>
                </select>
            </div>
            
            <div>
                <label for="report_period" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Report Period</label>
                <select id="report_period" name="report_period" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm rounded-md">
                    <option value="current_month">Current Month</option>
                    <option value="last_month">Last Month</option>
                    <option value="current_quarter">Current Quarter</option>
                    <option value="last_quarter">Last Quarter</option>
                    <option value="current_year">Current Year</option>
                    <option value="last_year">Last Year</option>
                    <option value="custom">Custom Date Range</option>
                </select>
            </div>
            
            <div class="md:col-span-2 flex justify-end">
                <button type="button" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-download mr-2"></i> Generate Report
                </button>
            </div>
        </div>
        
        <div class="mt-6">
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Recent Reports</h4>
            <div class="bg-gray-50 dark:bg-gray-700 rounded-md p-4">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-file-pdf text-red-600 mr-2"></i>
                            <span class="text-sm text-gray-900 dark:text-white">Sales Tax Report - June 2023</span>
                        </div>
                        <button type="button" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm">
                            Download
                        </button>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-file-pdf text-red-600 mr-2"></i>
                            <span class="text-sm text-gray-900 dark:text-white">VAT Report - Q2 2023</span>
                        </div>
                        <button type="button" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm">
                            Download
                        </button>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-file-pdf text-red-600 mr-2"></i>
                            <span class="text-sm text-gray-900 dark:text-white">Income Tax Report - 2022</span>
                        </div>
                        <button type="button" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm">
                            Download
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
