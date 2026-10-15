<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $title ?? 'Portal Siswa - SIBK-digi' }}</title>    
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">    
    <script src="https://cdn.tailwindcss.com"></script>    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
    @livewireStyles
</head>
<body class="antialiased text-gray-800 bg-gray-50" x-data="{ sidebarOpen: false }">

    @auth('student')
    <div class="flex h-screen overflow-hidden">
        
        <aside class="hidden md:flex flex-col w-64 bg-white border-r border-gray-200 z-20">
            <div class="flex items-center h-16 border-b border-gray-200 px-5 shrink-0">
                <div class="bg-indigo-600 p-1.5 rounded-lg mr-2.5 shadow-sm">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                </div>
                <span class="text-base font-bold text-gray-900 tracking-tight">SIBK-digi</span>
            </div>
            
            <div class="p-4 border-b border-gray-100 flex items-center gap-3 shrink-0 bg-gray-50/50">
                <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm border border-indigo-200 shrink-0">
                    {{ substr(auth()->guard('student')->user()->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 truncate">{{ auth()->guard('student')->user()->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ auth()->guard('student')->user()->class_name }}</p>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto py-4 px-3">
                <nav class="space-y-1">
                    <a href="{{ route('student.dashboard') }}" class="{{ request()->routeIs('student.dashboard') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 font-medium' }} group flex items-center px-3 py-2 text-sm rounded-lg transition-colors">
                        <svg class="mr-3 h-5 w-5 {{ request()->routeIs('student.dashboard') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Dashboard
                    </a>
                    <a href="{{ route('student.curhat') }}" class="{{ request()->routeIs('student.curhat') ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 font-medium' }} group flex items-center px-3 py-2 text-sm rounded-lg transition-colors">
                        <svg class="mr-3 h-5 w-5 {{ request()->routeIs('student.curhat') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        Layanan Konseling
                    </a>
                    <a href="{{ route('student.records') }}" class="{{ request()->routeIs('student.records') ? 'bg-rose-50 text-rose-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 font-medium' }} group flex items-center px-3 py-2 text-sm rounded-lg transition-colors">
                        <svg class="mr-3 h-5 w-5 {{ request()->routeIs('student.records') ? 'text-rose-600' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Catatan Kedisiplinan
                    </a>
                </nav>
            </div>
            <div class="p-3 border-t border-gray-100 shrink-0">
                <form action="{{ route('student.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-3 py-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg text-sm font-medium transition-colors">
                        <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Keluar Akun
                    </button>
                </form>
            </div>
        </aside>

        <div x-show="sidebarOpen" class="fixed inset-0 flex z-50 md:hidden" x-cloak>
            <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="sidebarOpen = false"></div>
            <div x-show="sidebarOpen" x-transition.transform.translate-x-start class="relative flex-1 flex flex-col max-w-xs w-full bg-white shadow-xl">
                <div class="absolute top-0 right-0 -mr-12 pt-3">
                    <button type="button" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none" @click="sidebarOpen = false">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-5 border-b border-gray-100 bg-gray-50">
                    <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center font-bold text-indigo-700 text-lg border border-indigo-200 mb-3">
                        {{ substr(auth()->guard('student')->user()->name, 0, 1) }}
                    </div>
                    <p class="text-base font-semibold text-gray-900 truncate">{{ auth()->guard('student')->user()->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ auth()->guard('student')->user()->class_name }}</p>
                </div>
                <div class="flex-1 overflow-y-auto py-4 px-3">
                    <nav class="space-y-1">
                        <a href="{{ route('student.dashboard') }}" class="{{ request()->routeIs('student.dashboard') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 font-medium' }} group flex items-center px-3 py-2.5 text-sm rounded-lg">Dashboard</a>
                        <a href="{{ route('student.curhat') }}" class="{{ request()->routeIs('student.curhat') ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 font-medium' }} group flex items-center px-3 py-2.5 text-sm rounded-lg">Layanan Konseling</a>
                        <a href="{{ route('student.records') }}" class="{{ request()->routeIs('student.records') ? 'bg-rose-50 text-rose-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 font-medium' }} group flex items-center px-3 py-2.5 text-sm rounded-lg">Catatan Kedisiplinan</a>
                    </nav>
                </div>
                <div class="p-4 border-t border-gray-100">
                    <form action="{{ route('student.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="flex items-center w-full px-3 py-2.5 text-red-600 hover:bg-red-50 rounded-lg text-sm font-medium">Keluar Akun</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="flex-1 flex flex-col overflow-hidden relative w-full bg-gray-50">
            
            @php
                $mobileHeaderBg = 'bg-white';
                $mobileHeaderBorder = 'border-b border-gray-200';
                $mobileHeaderText = 'text-gray-900';
                $mobileHeaderBtn = 'text-gray-600 hover:text-gray-900 hover:bg-gray-100';

                if (request()->routeIs('student.dashboard')) {
                    $mobileHeaderBg = 'bg-indigo-600';
                    $mobileHeaderBorder = 'border-none';
                    $mobileHeaderText = 'text-white';
                    $mobileHeaderBtn = 'text-indigo-100 hover:text-white hover:bg-indigo-500';
                } elseif (request()->routeIs('student.curhat')) {
                    $mobileHeaderBg = 'bg-emerald-600';
                    $mobileHeaderBorder = 'border-none';
                    $mobileHeaderText = 'text-white';
                    $mobileHeaderBtn = 'text-emerald-100 hover:text-white hover:bg-emerald-500';
                } elseif (request()->routeIs('student.records')) {
                    $mobileHeaderBg = 'bg-rose-600';
                    $mobileHeaderBorder = 'border-none';
                    $mobileHeaderText = 'text-white';
                    $mobileHeaderBtn = 'text-rose-100 hover:text-white hover:bg-rose-500';
                } elseif (request()->routeIs('student.assessment.take')) {
                    $mobileHeaderBg = 'bg-amber-500';
                    $mobileHeaderBorder = 'border-none';
                    $mobileHeaderText = 'text-white';
                    $mobileHeaderBtn = 'text-amber-100 hover:text-white hover:bg-amber-600';
                }
            @endphp

            <header class="md:hidden {{ $mobileHeaderBg }} {{ $mobileHeaderBorder }} {{ $mobileHeaderText }} flex items-center justify-between h-14 px-4 z-30 shrink-0 transition-colors duration-300 relative">
                <div class="flex items-center gap-2">
                    <button @click="sidebarOpen = true" class="p-1.5 -ml-1.5 {{ $mobileHeaderBtn }} focus:outline-none rounded-md transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <span class="font-bold tracking-tight">SIBK-digi</span>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto focus:outline-none flex flex-col">
                {{ $slot }}
            </main>
        </div>
    </div>
    @else
        {{ $slot }}
    @endauth

    @livewireScripts
</body>
</html>