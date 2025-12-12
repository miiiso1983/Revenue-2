<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Revenue Management System')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans">
    @auth
    <!-- Navigation -->
    <nav class="bg-gradient-to-r from-blue-600 to-purple-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-xl font-bold">Revenue Management</a>
                    <div class="ml-10 flex space-x-4">
                        <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-md hover:bg-white/10 transition">Dashboard</a>
                        <a href="{{ route('contracts.index') }}" class="px-3 py-2 rounded-md hover:bg-white/10 transition">Contracts</a>
                        <a href="{{ route('reports.pivot') }}" class="px-3 py-2 rounded-md hover:bg-white/10 transition">Reports</a>
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('bulk-upload.index') }}" class="px-3 py-2 rounded-md hover:bg-white/10 transition">Bulk Upload</a>
                        <a href="{{ route('audit-logs.index') }}" class="px-3 py-2 rounded-md hover:bg-white/10 transition">Audit Logs</a>
                        @endif
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm">{{ auth()->user()->username }} ({{ ucfirst(auth()->user()->role) }})</span>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-white/20 rounded-md hover:bg-white/30 transition">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    @endauth

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="flash-message max-w-7xl mx-auto mt-4 px-4">
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="flash-message max-w-7xl mx-auto mt-4 px-4">
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="flash-message max-w-7xl mx-auto mt-4 px-4">
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white mt-12 py-6 border-t">
        <div class="max-w-7xl mx-auto px-4 text-center text-gray-600">
            <p>&copy; {{ date('Y') }} Revenue Management System. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>

