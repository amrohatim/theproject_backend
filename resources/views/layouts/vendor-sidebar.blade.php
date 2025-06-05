<nav class="mt-5 space-y-1">
    <a href="{{ route('vendor.dashboard') }}" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('vendor.dashboard') ? 'sidebar-active' : 'text-gray-700 dark:text-gray-300' }}">
        <i class="fas fa-tachometer-alt mr-3"></i>
        Dashboard
    </a>

    <a href="{{ route('vendor.company.index') }}" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('vendor.company.*') ? 'sidebar-active' : 'text-gray-700 dark:text-gray-300' }}">
        <i class="fas fa-building mr-3"></i>
        Company
    </a>

    <a href="{{ route('vendor.branches.index') }}" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('vendor.branches.*') ? 'sidebar-active' : 'text-gray-700 dark:text-gray-300' }}">
        <i class="fas fa-store mr-3"></i>
        Branches
    </a>

    <a href="{{ route('vendor.products.index') }}" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('vendor.products.*') ? 'sidebar-active' : 'text-gray-700 dark:text-gray-300' }}">
        <i class="fas fa-shopping-bag mr-3"></i>
        Products
    </a>

    <a href="{{ route('vendor.services.index') }}" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('vendor.services.*') ? 'sidebar-active' : 'text-gray-700 dark:text-gray-300' }}">
        <i class="fas fa-concierge-bell mr-3"></i>
        Services
    </a>

    <a href="{{ route('vendor.deals.index') }}" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('vendor.deals.*') ? 'sidebar-active' : 'text-gray-700 dark:text-gray-300' }}">
        <i class="fas fa-percentage mr-3"></i>
        Deals
    </a>

    <a href="{{ route('vendor.orders.index') }}" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('vendor.orders.index') || (request()->routeIs('vendor.orders.*') && !request()->routeIs('vendor.orders.pending')) ? 'sidebar-active' : 'text-gray-700 dark:text-gray-300' }}">
        <i class="fas fa-shopping-cart mr-3"></i>
        All Orders
    </a>

    <a href="{{ route('vendor.orders.pending') }}" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('vendor.orders.pending') ? 'sidebar-active' : 'text-gray-700 dark:text-gray-300' }}">
        <i class="fas fa-clock mr-3"></i>
        Pending Orders
    </a>

    <a href="{{ route('vendor.bookings.index') }}" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('vendor.bookings.*') ? 'sidebar-active' : 'text-gray-700 dark:text-gray-300' }}">
        <i class="fas fa-calendar-alt mr-3"></i>
        Bookings
    </a>

    <a href="{{ route('vendor.settings') }}" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('vendor.settings') ? 'sidebar-active' : 'text-gray-700 dark:text-gray-300' }}">
        <i class="fas fa-cog mr-3"></i>
        Settings
    </a>
</nav>
