<nav class="mt-5 space-y-1">
    <a href="{{ route('admin.dashboard') }}" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.dashboard') ? 'sidebar-active' : 'text-gray-700 dark:text-gray-300' }}">
        <i class="fas fa-tachometer-alt mr-3"></i>
        Dashboard
    </a>
    
    <a href="{{ route('admin.users.index') }}" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.users.*') ? 'sidebar-active' : 'text-gray-700 dark:text-gray-300' }}">
        <i class="fas fa-users mr-3"></i>
        Users
    </a>
    
    <a href="{{ route('admin.categories.index') }}" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.categories.*') ? 'sidebar-active' : 'text-gray-700 dark:text-gray-300' }}">
        <i class="fas fa-tags mr-3"></i>
        Categories
    </a>

    <a href="{{ route('admin.business-types.index') }}" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.business-types.*') ? 'sidebar-active' : 'text-gray-700 dark:text-gray-300' }}">
        <i class="fas fa-industry mr-3"></i>
        Business Types
    </a>

    <a href="{{ route('admin.avatars.index') }}" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.avatars.*') ? 'sidebar-active' : 'text-gray-700 dark:text-gray-300' }}">
        <i class="fas fa-user-circle mr-3"></i>
        Avatar Management
    </a>
    
    <a href="{{ route('admin.companies.index') }}" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.companies.*') ? 'sidebar-active' : 'text-gray-700 dark:text-gray-300' }}">
        <i class="fas fa-building mr-3"></i>
        Companies
    </a>
    
    <a href="{{ route('admin.branches.index') }}" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.branches.*') ? 'sidebar-active' : 'text-gray-700 dark:text-gray-300' }}">
        <i class="fas fa-store mr-3"></i>
        Branches
    </a>
    
    <a href="{{ route('admin.products.index') }}" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.products.*') ? 'sidebar-active' : 'text-gray-700 dark:text-gray-300' }}">
        <i class="fas fa-shopping-bag mr-3"></i>
        Products
    </a>
    
    <a href="{{ route('admin.providers.index') }}" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.providers.*') ? 'sidebar-active' : 'text-gray-700 dark:text-gray-300' }}">
        <i class="fas fa-user-tie mr-3"></i>
        Providers
    </a>
    
    <a href="{{ route('admin.services.index') }}" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.services.*') ? 'sidebar-active' : 'text-gray-700 dark:text-gray-300' }}">
        <i class="fas fa-concierge-bell mr-3"></i>
        Services
    </a>

    <a href="{{ route('admin.merchant-licenses.index') }}" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.merchant-licenses.*') ? 'sidebar-active' : 'text-gray-700 dark:text-gray-300' }}">
        <i class="fas fa-certificate mr-3"></i>
        Merchant Licenses
    </a>

    <a href="{{ route('admin.branch-licenses.index') }}" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.branch-licenses.*') ? 'sidebar-active' : 'text-gray-700 dark:text-gray-300' }}">
        <i class="fas fa-store-alt mr-3"></i>
        Branch Licenses
    </a>

    <a href="{{ route('admin.provider-licenses.index') }}" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.provider-licenses.*') ? 'sidebar-active' : 'text-gray-700 dark:text-gray-300' }}">
        <i class="fas fa-id-card mr-3"></i>
        Provider Licenses
    </a>



    <a href="{{ route('admin.settings') }}" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.settings') ? 'sidebar-active' : 'text-gray-700 dark:text-gray-300' }}">
        <i class="fas fa-cog mr-3"></i>
        Settings
    </a>
</nav>
