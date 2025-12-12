<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Revenue Management System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-blue-500 to-purple-600 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-2xl p-8 w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Revenue Management</h1>
            <p class="text-gray-600 mt-2">Sign in to your account</p>
        </div>

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label for="email" class="label">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" 
                       class="input-field" required autofocus>
            </div>

            <div class="mb-6">
                <label for="password" class="label">Password</label>
                <input type="password" id="password" name="password" 
                       class="input-field" required>
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-600">Remember me</span>
                </label>
            </div>

            <button type="submit" class="btn-primary w-full">
                Sign In
            </button>
        </form>

        <div class="mt-6 text-center text-sm text-gray-600">
            <p>Demo Accounts:</p>
            <p class="mt-1">Admin: admin@example.com / password</p>
            <p>Guest: guest@example.com / password</p>
        </div>
    </div>
</body>
</html>

