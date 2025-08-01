@extends('layouts.dashboard')

@section('title', 'Categories Management')
@section('page-title', 'Categories Management')

@section('content')
<div class="container mx-auto">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Categories Management</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">Manage product and service categories</p>
        </div>
        <div class="mt-4 md:mt-0 space-x-2">
            <button type="button" onclick="openAddParentCategoryModal()" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-folder-plus mr-2"></i> Add Parent Category
            </button>
            <button type="button" onclick="openAddCategoryModal()" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i> Add Subcategory
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-bold">Success!</strong>
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-bold">Error!</strong>
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <!-- Search and filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 p-4 border border-gray-200 dark:border-gray-700">
        <form action="{{ route('admin.categories.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" name="search" id="search" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" placeholder="Search categories...">
                    </div>
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
                    <select id="type" name="type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="">All Types</option>
                        <option value="product">Product</option>
                        <option value="service">Service</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Categories list -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 border border-gray-200 dark:border-gray-700">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Parent Category</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Description</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Products/Services</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($categories ?? [] as $category)
                    <tr data-category-id="{{ $category->id }}" data-parent-id="{{ $category->parent_id ?? 'null' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @if($category->image)
                                        @php
                                            // Handle both old and new image path formats
                                            $imageSrc = $category->image;
                                            if (strpos($imageSrc, 'categories/') === 0) {
                                                // New format: categories/filename.jpg -> /storage/categories/filename.jpg
                                                $imageSrc = '/storage/' . $imageSrc;
                                            } elseif (!str_starts_with($imageSrc, '/') && !str_starts_with($imageSrc, 'http')) {
                                                // Old format without leading slash
                                                $imageSrc = '/' . $imageSrc;
                                            }
                                        @endphp
                                        <img src="{{ $imageSrc }}" alt="{{ $category->name }}" class="h-10 w-10 rounded-md object-cover">
                                    @elseif($category->icon)
                                        <div class="h-10 w-10 rounded-md bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                                            <i class="{{ $category->icon }} text-indigo-600 dark:text-indigo-400"></i>
                                        </div>
                                    @else
                                        <div class="h-10 w-10 rounded-md bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                                            <i class="fas fa-tag text-indigo-600 dark:text-indigo-400"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        @if(app()->getLocale() == 'ar' && $category->category_name_arabic)
                                            {{ $category->category_name_arabic }}
                                        @else
                                            {{ $category->name }}
                                        @endif
                                    </div>
                                    @if(app()->getLocale() == 'ar' && $category->category_name_arabic)
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $category->name }}</div>
                                    @elseif($category->category_name_arabic)
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $category->category_name_arabic }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($category->type == 'product') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                @else bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 @endif">
                                {{ ucfirst($category->type ?? 'product') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                @if($category->parent)
                                    {{ $category->parent->name }}
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">None (Main Category)</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-xs">{{ $category->description ?? 'No description' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $category->items_count ?? 0 }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button type="button" onclick="openEditCategoryModal({{ $category->id }})" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 mr-3">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 delete-btn" data-id="{{ $category->id }}" data-name="{{ $category->name }}" data-is-parent="{{ $category->parent_id ? 'false' : 'true' }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center justify-center py-4">
                                <i class="fas fa-tags text-gray-300 dark:text-gray-600 text-5xl mb-4"></i>
                                <p>No categories found</p>
                                <div class="mt-2 space-x-2">
                                    <button type="button" onclick="openAddParentCategoryModal()" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                                        <i class="fas fa-folder-plus mr-2"></i> Add Parent Category
                                    </button>
                                    <button type="button" onclick="openAddCategoryModal()" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                        <i class="fas fa-plus mr-2"></i> Add Subcategory
                                    </button>
                                </div>
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
        {{ $categories->links() ?? '' }}
    </div>
</div>

<!-- Add Parent Category Modal -->
<div id="addParentCategoryModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="parentCategoryForm" action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="parent_id" value="">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-folder-plus text-green-600 dark:text-green-200"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                Add Parent Category
                            </h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label for="parent_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category Name (English) *</label>
                                    <input type="text" name="name" id="parent_name" class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" required>
                                </div>
                                <div>
                                    <label for="parent_name_arabic" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category Name (Arabic) *</label>
                                    <input type="text" name="category_name_arabic" id="parent_name_arabic" dir="rtl" class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" required>
                                </div>
                                <div>
                                    <label for="parent_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
                                    <select name="type" id="parent_type" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white bg-white rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm" required>
                                        <option value="product">Product</option>
                                        <option value="service">Service</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="parent_image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category Image</label>
                                    <input type="file" name="image" id="parent_image" accept="image/*" class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Upload an image for the category (recommended size: 800x600px)</p>
                                </div>
                                <div>
                                    <label for="parent_icon" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Icon (Font Awesome class) - Optional</label>
                                    <input type="text" name="icon" id="parent_icon" class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" placeholder="fas fa-tag">
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Used as fallback if no image is provided</p>
                                </div>
                                <div>
                                    <label for="parent_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description (English)</label>
                                    <textarea name="description" id="parent_description" rows="3" class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md"></textarea>
                                </div>
                                <div>
                                    <label for="parent_description_arabic" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description (Arabic)</label>
                                    <textarea name="category_description_arabic" id="parent_description_arabic" dir="rtl" rows="3" class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Create Parent Category
                    </button>
                    <button type="button" onclick="closeAddParentCategoryModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Subcategory Modal -->
<div id="addCategoryModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="subcategoryForm" action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">Add Category</h3>
                    <div class="mt-4 space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name (English) *</label>
                            <input type="text" name="name" id="name" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" required>
                        </div>
                        <div>
                            <label for="name_arabic" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name (Arabic) *</label>
                            <input type="text" name="category_name_arabic" id="name_arabic" dir="rtl" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" required>
                        </div>
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
                            <select name="type" id="type" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                <option value="product">Product</option>
                                <option value="service">Service</option>
                            </select>
                        </div>
                        <div>
                            <label for="parent_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Parent Category</label>
                            <select name="parent_id" id="parent_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">None (Main Category)</option>
                                @foreach($categories ?? [] as $parentCategory)
                                    <option value="{{ $parentCategory->id }}">{{ $parentCategory->name }} ({{ ucfirst($parentCategory->type ?? 'product') }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category Image</label>
                            <input type="file" name="image" id="image" accept="image/*" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Upload an image for the category (recommended size: 800x600px)</p>
                        </div>
                        <div>
                            <label for="icon" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Icon (Font Awesome class) - Optional</label>
                            <input type="text" name="icon" id="icon" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" placeholder="fas fa-tag">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Used as fallback if no image is provided</p>
                        </div>
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description (English)</label>
                            <textarea name="description" id="description" rows="3" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md"></textarea>
                        </div>
                        <div>
                            <label for="description_arabic" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description (Arabic)</label>
                            <textarea name="category_description_arabic" id="description_arabic" dir="rtl" rows="3" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md"></textarea>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Save
                    </button>
                    <button type="button" onclick="closeAddCategoryModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div id="editCategoryModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="editCategoryForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_category_id" name="category_id">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">Edit Category</h3>
                    <div class="mt-4 space-y-4">
                        <div>
                            <label for="edit_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name (English) *</label>
                            <input type="text" name="name" id="edit_name" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" required>
                        </div>
                        <div>
                            <label for="edit_name_arabic" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name (Arabic) *</label>
                            <input type="text" name="category_name_arabic" id="edit_name_arabic" dir="rtl" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" required>
                        </div>
                        <div>
                            <label for="edit_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
                            <select name="type" id="edit_type" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" required>
                                <option value="product">Product</option>
                                <option value="service">Service</option>
                            </select>
                        </div>
                        <div>
                            <label for="edit_parent_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Parent Category</label>
                            <select name="parent_id" id="edit_parent_id" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                <option value="">None (Parent Category)</option>
                            </select>
                        </div>
                        <div>
                            <label for="edit_icon" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Icon (Font Awesome class)</label>
                            <input type="text" name="icon" id="edit_icon" placeholder="e.g., fas fa-shopping-cart" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div>
                        <div>
                            <label for="edit_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description (English)</label>
                            <textarea name="description" id="edit_description" rows="3" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md"></textarea>
                        </div>
                        <div>
                            <label for="edit_description_arabic" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description (Arabic)</label>
                            <textarea name="category_description_arabic" id="edit_description_arabic" dir="rtl" rows="3" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md"></textarea>
                        </div>
                        <div>
                            <label for="edit_image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Image</label>
                            <input type="file" name="image" id="edit_image" accept="image/*" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                            <div id="edit_current_image" class="mt-2 hidden">
                                <p class="text-sm text-gray-600 dark:text-gray-400">Current image:</p>
                                <img id="edit_current_image_preview" src="" alt="Current category image" class="mt-1 h-20 w-20 object-cover rounded">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Update Category
                    </button>
                    <button type="button" onclick="closeEditCategoryModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Function to fetch parent categories via AJAX
    function fetchParentCategories() {
        // Create a new XMLHttpRequest object
        const xhr = new XMLHttpRequest();

        // Configure it to make a GET request to the categories endpoint
        xhr.open('GET', '{{ route('admin.categories.index') }}?parents_only=true', true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        // Set up a function to handle the response
        xhr.onload = function() {
            if (xhr.status === 200) {
                // Parse the JSON response
                const parentCategories = JSON.parse(xhr.responseText);

                // Get the parent categories select element
                const parentCategorySelect = document.getElementById('parent_id');

                // Clear existing options except the first one (None)
                while (parentCategorySelect.options.length > 1) {
                    parentCategorySelect.remove(1);
                }

                // Add each parent category to the select
                parentCategories.forEach(category => {
                    // Create a new option
                    const option = document.createElement('option');
                    option.value = category.id;
                    option.textContent = `${category.name} (${category.type ? category.type.charAt(0).toUpperCase() + category.type.slice(1) : 'Product'})`;

                    // Add the option to the select
                    parentCategorySelect.appendChild(option);
                });
            }
        };

        // Send the request
        xhr.send();
    }

    function openAddParentCategoryModal() {
        document.getElementById('addParentCategoryModal').classList.remove('hidden');
    }

    function closeAddParentCategoryModal() {
        document.getElementById('addParentCategoryModal').classList.add('hidden');
    }

    function openAddCategoryModal() {
        // Fetch parent categories before opening the modal
        fetchParentCategories();
        document.getElementById('addCategoryModal').classList.remove('hidden');
    }

    function closeAddCategoryModal() {
        document.getElementById('addCategoryModal').classList.add('hidden');
    }

    function openEditCategoryModal(categoryId) {
        // Fetch category data via AJAX
        fetch(`{{ url('admin/categories') }}/${categoryId}/edit`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Populate the form with category data
                document.getElementById('edit_category_id').value = categoryId;
                document.getElementById('edit_name').value = data.category.name || '';
                document.getElementById('edit_name_arabic').value = data.category.category_name_arabic || '';
                document.getElementById('edit_type').value = data.category.type || 'product';
                document.getElementById('edit_icon').value = data.category.icon || '';
                document.getElementById('edit_description').value = data.category.description || '';
                document.getElementById('edit_description_arabic').value = data.category.category_description_arabic || '';

                // Populate parent categories dropdown
                const parentSelect = document.getElementById('edit_parent_id');
                parentSelect.innerHTML = '<option value="">None (Parent Category)</option>';

                data.parentCategories.forEach(parent => {
                    const option = document.createElement('option');
                    option.value = parent.id;
                    option.textContent = parent.name;
                    if (data.category.parent_id == parent.id) {
                        option.selected = true;
                    }
                    parentSelect.appendChild(option);
                });

                // Show current image if exists
                if (data.category.image) {
                    document.getElementById('edit_current_image').classList.remove('hidden');

                    // Handle both old and new image path formats
                    let imageSrc = data.category.image;
                    if (imageSrc.startsWith('categories/')) {
                        // New format: categories/filename.jpg -> /storage/categories/filename.jpg
                        imageSrc = '/storage/' + imageSrc;
                    } else if (!imageSrc.startsWith('/') && !imageSrc.startsWith('http')) {
                        // Old format without leading slash
                        imageSrc = '/' + imageSrc;
                    }

                    document.getElementById('edit_current_image_preview').src = `{{ url('/') }}${imageSrc}`;
                } else {
                    document.getElementById('edit_current_image').classList.add('hidden');
                }

                // Show the modal
                document.getElementById('editCategoryModal').classList.remove('hidden');
            } else {
                alert('Error loading category data: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading category data. Please try again.');
        });
    }

    function closeEditCategoryModal() {
        document.getElementById('editCategoryModal').classList.add('hidden');
        // Reset form
        document.getElementById('editCategoryForm').reset();
        document.getElementById('edit_current_image').classList.add('hidden');
    }

    // Add event listeners to all delete buttons
    document.addEventListener('DOMContentLoaded', function() {
        // Get all delete forms
        const deleteForms = document.querySelectorAll('.delete-form');

        // Add event listener to each form
        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const btn = form.querySelector('.delete-btn');
                const categoryId = btn.getAttribute('data-id');
                const categoryName = btn.getAttribute('data-name');
                const isParent = btn.getAttribute('data-is-parent') === 'true';

                let confirmMessage = `Are you sure you want to delete the category "${categoryName}"?`;

                if (isParent) {
                    // Check if this parent category has children
                    const childRows = document.querySelectorAll(`tr[data-parent-id="${categoryId}"]`);

                    if (childRows.length > 0) {
                        confirmMessage = `Warning: "${categoryName}" has subcategories. Deleting this parent category will also delete all its subcategories. Are you sure you want to continue?`;
                    }
                }

                if (confirm(confirmMessage)) {
                    form.submit();
                }
            });
        });
    });

    // Submit forms via AJAX
    document.addEventListener('DOMContentLoaded', function() {
        // Fetch parent categories when the page loads
        fetchParentCategories();
        // Handle parent category form submission
        const parentCategoryForm = document.getElementById('parentCategoryForm');

        if (parentCategoryForm) {
            parentCategoryForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Create FormData object
                const formData = new FormData(parentCategoryForm);

                // Create a new XMLHttpRequest object
                const xhr = new XMLHttpRequest();

                // Configure it to make a POST request to the categories store endpoint
                xhr.open('POST', '{{ route('admin.categories.store') }}', true);

                // Set up a function to handle the response
                xhr.onload = function() {
                    if (xhr.status === 200 || xhr.status === 302) {
                        // Close the modal
                        closeAddParentCategoryModal();

                        // Show success message
                        const successMessage = document.createElement('div');
                        successMessage.className = 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4';
                        successMessage.innerHTML = '<strong class="font-bold">Success!</strong><span class="block sm:inline"> Parent category created successfully.</span>';

                        // Insert the message at the top of the page
                        const container = document.querySelector('.container');
                        container.insertBefore(successMessage, container.firstChild);

                        // Remove the message after 3 seconds
                        setTimeout(function() {
                            successMessage.remove();
                        }, 3000);

                        // Reset the form
                        parentCategoryForm.reset();

                        // Refresh the page to show the new parent category
                        window.location.reload();
                    }
                };

                // Send the request with the form data (FormData automatically handles file uploads)
                xhr.send(formData);
            });
        }

        // Handle subcategory form submission
        const subcategoryForm = document.getElementById('subcategoryForm');

        if (subcategoryForm) {
            subcategoryForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Create FormData object
                const formData = new FormData(subcategoryForm);

                // Create a new XMLHttpRequest object
                const xhr = new XMLHttpRequest();

                // Configure it to make a POST request to the categories store endpoint
                xhr.open('POST', '{{ route('admin.categories.store') }}', true);

                // Set up a function to handle the response
                xhr.onload = function() {
                    if (xhr.status === 200 || xhr.status === 302) {
                        // Close the modal
                        closeAddCategoryModal();

                        // Show success message
                        const successMessage = document.createElement('div');
                        successMessage.className = 'bg-indigo-100 border border-indigo-400 text-indigo-700 px-4 py-3 rounded relative mb-4';
                        successMessage.innerHTML = '<strong class="font-bold">Success!</strong><span class="block sm:inline"> Category created successfully.</span>';

                        // Insert the message at the top of the page
                        const container = document.querySelector('.container');
                        container.insertBefore(successMessage, container.firstChild);

                        // Remove the message after 3 seconds
                        setTimeout(function() {
                            successMessage.remove();
                        }, 3000);

                        // Reset the form
                        subcategoryForm.reset();

                        // Refresh the page to show the new category
                        window.location.reload();
                    }
                };

                // Send the request with the form data (FormData automatically handles file uploads)
                xhr.send(formData);
            });
        }

        // Handle edit category form submission
        const editCategoryForm = document.getElementById('editCategoryForm');

        if (editCategoryForm) {
            editCategoryForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const categoryId = document.getElementById('edit_category_id').value;

                // Create FormData object
                const formData = new FormData(editCategoryForm);

                // Create a new XMLHttpRequest object
                const xhr = new XMLHttpRequest();

                // Configure it to make a PUT request to the categories update endpoint
                xhr.open('POST', `{{ url('admin/categories') }}/${categoryId}`, true);

                // Add method override for PUT request
                formData.append('_method', 'PUT');

                // Add CSRF token
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                // Set up a function to handle the response
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.success) {
                                // Close the modal
                                closeEditCategoryModal();

                                // Show success message
                                const successMessage = document.createElement('div');
                                successMessage.className = 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4';
                                successMessage.innerHTML = '<strong class="font-bold">Success!</strong><span class="block sm:inline"> ' + response.message + '</span>';

                                // Insert the message at the top of the page
                                const container = document.querySelector('.container');
                                container.insertBefore(successMessage, container.firstChild);

                                // Remove the message after 3 seconds
                                setTimeout(function() {
                                    successMessage.remove();
                                }, 3000);

                                // Refresh the page to show the updated category
                                window.location.reload();
                            } else {
                                alert('Error: ' + (response.message || 'Unknown error occurred'));
                            }
                        } catch (e) {
                            console.error('Error parsing response:', e);
                            alert('Error updating category. Please try again.');
                        }
                    } else {
                        alert('Error updating category. Please try again.');
                    }
                };

                xhr.onerror = function() {
                    alert('Network error. Please try again.');
                };

                // Send the request with the form data
                xhr.send(formData);
            });
        }
    });
</script>
@endsection
