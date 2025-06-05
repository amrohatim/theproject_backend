<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/debug.js') }}"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-4">Test Links</h1>
        <div class="space-y-4">
            <a href="{{ route('admin.demo') }}" class="block w-full bg-blue-500 text-white py-2 px-4 rounded text-center hover:bg-blue-600">
                Admin Dashboard
            </a>
            <a href="{{ route('vendor.demo') }}" class="block w-full bg-green-500 text-white py-2 px-4 rounded text-center hover:bg-green-600">
                Vendor Dashboard
            </a>
            <a href="{{ route('login') }}" class="block w-full bg-gray-500 text-white py-2 px-4 rounded text-center hover:bg-gray-600">
                Login
            </a>
        </div>
    </div>
</body>
</html>
