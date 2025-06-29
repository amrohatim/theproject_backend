<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose Registration Type - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .choice-card {
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        .choice-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border-color: #3B82F6;
        }
        .choice-card.selected {
            border-color: #3B82F6;
            background: linear-gradient(135deg, #EBF4FF 0%, #F0F9FF 100%);
        }
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .icon-wrapper {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="/" class="text-2xl font-bold text-gray-900">
                        {{ config('app.name') }}
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 transition-colors">
                        Already have an account? Sign in
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Hero Section -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">
                    Join Our Marketplace
                </h1>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Choose your registration type to get started. Whether you're selling products or supplying to vendors, we have the perfect solution for you.
                </p>
            </div>

            <!-- Registration Choice Cards -->
            <div class="grid md:grid-cols-3 gap-8 mb-8">
                <!-- Vendor Registration Card -->
                <div class="choice-card bg-white rounded-2xl p-8 shadow-lg cursor-pointer" data-testid="vendor-registration-link" onclick="selectChoice('vendor')">
                    <div class="text-center">
                        <!-- Icon -->
                        <div class="icon-wrapper w-20 h-20 rounded-full mx-auto mb-6 flex items-center justify-center">
                            <i class="fas fa-store text-3xl text-white"></i>
                        </div>

                        <!-- Title -->
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">
                            Vendor Registration
                        </h3>

                        <!-- Description -->
                        <p class="text-gray-600 mb-6 leading-relaxed">
                            Perfect for businesses selling physical products. Manage your inventory, showcase your products, and reach customers across the marketplace.
                        </p>

                        <!-- Features -->
                        <ul class="text-left space-y-3 mb-8">
                            <li class="flex items-center text-gray-700">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                Product catalog management
                            </li>
                            <li class="flex items-center text-gray-700">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                Inventory tracking
                            </li>
                            <li class="flex items-center text-gray-700">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                Order management
                            </li>
                            <li class="flex items-center text-gray-700">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                Delivery options
                            </li>
                        </ul>

                        <!-- Button -->
                        <button type="button" class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                            Register as Vendor
                        </button>
                    </div>
                </div>

                <!-- Provider Registration Card -->
                <div class="choice-card bg-white rounded-2xl p-8 shadow-lg cursor-pointer" data-testid="provider-registration-link" onclick="selectChoice('provider')">
                    <div class="text-center">
                        <!-- Icon -->
                        <div class="icon-wrapper w-20 h-20 rounded-full mx-auto mb-6 flex items-center justify-center">
                            <i class="fas fa-truck text-3xl text-white"></i>
                        </div>

                        <!-- Title -->
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">
                            Provider Registration
                        </h3>

                        <!-- Description -->
                        <p class="text-gray-600 mb-6 leading-relaxed">
                            Perfect for suppliers providing products to vendors. Supply inventory, manage wholesale orders, and grow your distribution network.
                        </p>

                        <!-- Features -->
                        <ul class="text-left space-y-3 mb-8">
                            <li class="flex items-center text-gray-700">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                Wholesale product catalog
                            </li>
                            <li class="flex items-center text-gray-700">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                Bulk order management
                            </li>
                            <li class="flex items-center text-gray-700">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                Vendor relationship management
                            </li>
                            <li class="flex items-center text-gray-700">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                Supply chain tracking
                            </li>
                        </ul>

                        <!-- Button -->
                        <button type="button" class="w-full bg-purple-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-purple-700 transition-colors">
                            Register as Provider
                        </button>
                    </div>
                </div>

                <!-- Merchant Registration Card -->
                <div class="choice-card bg-white rounded-2xl p-8 shadow-lg cursor-pointer" data-testid="merchant-registration-link" onclick="selectChoice('merchant')">
                    <div class="text-center">
                        <!-- Icon -->
                        <div class="icon-wrapper w-20 h-20 rounded-full mx-auto mb-6 flex items-center justify-center">
                            <i class="fas fa-store text-3xl text-white"></i>
                        </div>

                        <!-- Title -->
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">
                            Merchant Registration
                        </h3>

                        <!-- Description -->
                        <p class="text-gray-600 mb-6 leading-relaxed">
                            Perfect for individual women merchants. Start your small business, sell your products, and provide services directly to customers.
                        </p>

                        <!-- Features -->
                        <ul class="text-left space-y-3 mb-8">
                            <li class="flex items-center text-gray-700">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                Individual business setup
                            </li>
                            <li class="flex items-center text-gray-700">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                Direct customer sales
                            </li>
                            <li class="flex items-center text-gray-700">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                Delivery capability
                            </li>
                            <li class="flex items-center text-gray-700">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                Small store location
                            </li>
                        </ul>

                        <!-- Button -->
                        <button type="button" class="w-full bg-orange-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-orange-700 transition-colors">
                            Register as Merchant
                        </button>
                    </div>
                </div>
            </div>

            <!-- Additional Info -->
            <div class="text-center">
                <p class="text-gray-500 text-sm">
                    Not sure which option is right for you?
                    <a href="#" class="text-blue-600 hover:text-blue-700 font-medium">Contact our support team</a>
                </p>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center text-gray-500 text-sm">
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </div>
        </div>
    </footer>

    <script>
        function selectChoice(type) {
            // Remove selected class from all cards
            document.querySelectorAll('.choice-card').forEach(card => {
                card.classList.remove('selected');
            });

            // Add selected class to clicked card
            event.currentTarget.classList.add('selected');

            // Redirect after a short delay for visual feedback
            setTimeout(() => {
                if (type === 'vendor') {
                    window.location.href = '{{ route("register.vendor") }}';
                } else if (type === 'provider') {
                    window.location.href = '{{ route("register.provider") }}';
                } else if (type === 'merchant') {
                    window.location.href = '{{ route("register.merchant") }}';
                }
            }, 300);
        }

        // Add hover effects
        document.querySelectorAll('.choice-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                if (!this.classList.contains('selected')) {
                    this.style.transform = 'translateY(-4px)';
                }
            });

            card.addEventListener('mouseleave', function() {
                if (!this.classList.contains('selected')) {
                    this.style.transform = 'translateY(0)';
                }
            });
        });
    </script>
</body>
</html>