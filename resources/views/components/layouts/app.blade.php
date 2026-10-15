<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'SIBK-digi' }}</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4f46e5',
                        secondary: '#e0e7ff',
                    }
                }
            }
        }
    </script>
    
    @livewireStyles
</head>
<body class="bg-gray-50 font-sans text-gray-900 antialiased">

    <!-- Navbar Sederhana -->
    <nav class="bg-white shadow-sm border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="text-2xl font-bold text-primary">SIBK<span class="text-gray-600">-digi</span></a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="/admin" class="text-sm text-gray-600 hover:text-primary">Login Admin</a>
                    <a href="/app" class="px-4 py-2 bg-primary text-white text-sm rounded-lg hover:bg-indigo-700 transition">Login Sekolah</a>
                </div>
            </div>
        </div>
    </nav>

    <main>
        {{ $slot }}
    </main>

    <footer class="bg-white border-t border-gray-200 mt-12 py-8">
        <div class="max-w-7xl mx-auto px-4 text-center text-gray-500 text-sm">
            &copy; {{ date('Y') }} SIBK-digi. Sistem Informasi Bimbingan Konseling Digital.
        </div>
    </footer>

    @livewireScripts
</body>
</html>