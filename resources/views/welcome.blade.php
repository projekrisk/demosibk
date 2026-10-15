<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    
    <title>SIBK-digi | Platform Bimbingan Konseling</title>
    <meta name="description" content="SIBK-digi adalah platform Bimbingan Konseling (BK) digital terpadu untuk sekolah. Kelola data siswa, asesmen AKPD & RIASEC, layanan konseling, dan cetak rapor BK otomatis dengan mudah.">
    <meta name="keywords" content="aplikasi bk, bimbingan konseling digital, software bk sekolah, asesmen akpd, tes riasec, rapor bk, administrasi bk, konseling online siswa, guru bk">
    <meta name="author" content="SIBK-digi">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/') }}">

    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="SIBK-digi | Platform Bimbingan Konseling Modern">
    <meta property="og:description" content="Tinggalkan kertas, fokus pada siswa. Kelola administrasi Bimbingan Konseling, asesmen, dan rapor BK otomatis dalam satu platform terpadu.">
    <meta property="og:image" content="{{ asset('images/og-cover.png') }}">
    <meta property="og:site_name" content="SIBK-digi">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url('/') }}">
    <meta name="twitter:title" content="SIBK-digi | Platform Bimbingan Konseling Modern">
    <meta name="twitter:description" content="Tinggalkan kertas, fokus pada siswa. Kelola administrasi Bimbingan Konseling, asesmen, dan rapor BK otomatis dalam satu platform terpadu.">
    <meta name="twitter:image" content="{{ asset('images/og-cover.png') }}">

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Inter"', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5', 
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                            950: '#1e1b4b',
                        }
                    },
                    animation: {
                        'marquee': 'marquee 40s linear infinite',
                    },
                    keyframes: {
                        marquee: {
                            '0%': { transform: 'translateX(0%)' },
                            '100%': { transform: 'translateX(-50%)' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #ffffff; }
        .glass-header {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .bg-dots {
            background-image: radial-gradient(#e5e7eb 1px, transparent 1px);
            background-size: 24px 24px;
        }
    </style>
</head>
<body class="text-slate-800 font-sans antialiased overflow-x-hidden selection:bg-primary-200 selection:text-primary-900 flex flex-col min-h-screen">

    @php
        $dashboardUrl = '/app/login';
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->role === 'super_admin') {
                $dashboardUrl = '/admin';
            } else {
                $tenant = $user->tenants->first();
                if ($tenant) {
                    $dashboardUrl = '/app/' . $tenant->slug;
                }
            }
        }
    @endphp

    <header x-data="{ mobileMenuOpen: false }" class="fixed top-0 w-full z-50 glass-header border-b border-slate-200 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 sm:h-20">
                
                <div class="flex items-center gap-2 sm:gap-3 cursor-pointer shrink-0" onclick="window.scrollTo(0,0)">
                    <div class="bg-primary-600 text-white p-1.5 rounded-lg flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                    </div>
                    <span class="text-xl font-extrabold text-slate-900 tracking-tight">SIBK-digi</span>
                </div>

                <nav class="hidden md:flex items-center gap-8">
                    <a href="#fitur" class="text-sm font-semibold text-slate-600 hover:text-primary-600 transition-colors">Fitur Utama</a>
                    <a href="#harga" class="text-sm font-semibold text-slate-600 hover:text-primary-600 transition-colors">Biaya Langganan</a>
                    
                    <div class="flex items-center gap-4 pl-6 border-l border-slate-200">
                        <a href="/siswa/login" class="text-sm font-bold text-slate-700 hover:text-primary-600 transition-colors flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Portal Siswa
                        </a>
                        
                        @auth
                            <a href="{{ $dashboardUrl }}" class="px-5 py-2.5 bg-slate-900 text-white rounded-lg text-sm font-bold hover:bg-slate-800 transition-colors shadow-sm">
                                Buka Dashboard
                            </a>
                        @else
                            <a href="/app/login" class="text-sm font-bold text-primary-600 hover:text-primary-700 transition-colors">Login Admin</a>
                            <a href="/app/register" class="px-5 py-2.5 bg-primary-600 text-white rounded-lg text-sm font-bold hover:bg-primary-700 transition-colors shadow-sm shadow-primary-500/30">
                                Daftar Gratis
                            </a>
                        @endauth
                    </div>
                </nav>

                <div class="flex items-center md:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-slate-600 hover:text-slate-900 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors bg-slate-50 hover:bg-slate-100">
                        <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        <svg x-show="mobileMenuOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>
        </div>

        <div x-show="mobileMenuOpen" 
             x-collapse
             x-cloak 
             class="md:hidden bg-white border-b border-slate-200 shadow-xl absolute w-full left-0 origin-top">
            <div class="px-4 pt-2 pb-6 space-y-1">
                <a href="#fitur" @click="mobileMenuOpen = false" class="block px-4 py-3 text-base font-semibold text-slate-700 hover:bg-slate-50 hover:text-primary-600 rounded-lg transition-colors">Fitur Utama</a>
                <a href="#harga" @click="mobileMenuOpen = false" class="block px-4 py-3 text-base font-semibold text-slate-700 hover:bg-slate-50 hover:text-primary-600 rounded-lg transition-colors">Biaya Langganan</a>
                
                <div class="my-2 border-t border-slate-100"></div>
                
                <a href="/siswa/login" class="flex items-center gap-3 px-4 py-3 text-base font-bold text-primary-600 hover:bg-primary-50 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Portal Siswa
                </a>
                
                <div class="pt-4 flex flex-col gap-3 px-2">
                    @auth
                        <a href="{{ $dashboardUrl }}" class="w-full py-3.5 bg-slate-900 text-white text-center rounded-xl font-bold shadow-sm">Buka Dashboard</a>
                    @else
                        <a href="/app/login" class="w-full py-3.5 bg-slate-100 text-slate-800 text-center rounded-xl font-bold hover:bg-slate-200 transition-colors">Login Admin</a>
                        <a href="/app/register" class="w-full py-3.5 bg-primary-600 text-white text-center rounded-xl font-bold shadow-sm shadow-primary-500/30 hover:bg-primary-700 transition-colors">Daftar Sekolah Baru</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <main class="flex-grow">
        
        <section class="relative pt-24 pb-12 sm:pt-32 sm:pb-16 lg:pt-40 lg:pb-24 overflow-hidden bg-gradient-to-b from-primary-50/50 to-white bg-dots">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="lg:grid lg:grid-cols-12 lg:gap-16 items-center">
                    
                    <div class="text-center lg:text-left lg:col-span-6 md:max-w-2xl md:mx-auto lg:mx-0">
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white border border-slate-200 text-slate-600 font-semibold text-xs sm:text-sm mb-6 shadow-sm">
                            <span class="relative flex h-2.5 w-2.5">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary-400 opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-primary-500"></span>
                            </span>
                            Sistem Informasi BK Digital
                        </div>
                        
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight leading-[1.15] sm:leading-tight mb-6">
                            BK Lebih Modern.<br>
                            <span class="text-primary-600">Siswa Makin Dekat.</span>
                        </h1>
                        
                        <p class="text-base sm:text-lg text-slate-600 mb-8 sm:mb-10 leading-relaxed px-2 sm:px-0">
                            Sistem cerdas untuk mengelola data siswa, sesi konseling, instrumen asesmen, hingga pencetakan rapor BK secara otomatis dan terintegrasi.
                        </p>
                        
                        <div class="flex flex-col sm:flex-row justify-center lg:justify-start gap-3 sm:gap-4 px-4 sm:px-0">
                            <a href="/app/register" class="w-full sm:w-auto px-6 py-3.5 bg-primary-600 text-white font-bold rounded-lg hover:bg-primary-700 transition shadow-sm text-center text-sm sm:text-base">
                                Mulai Gunakan Gratis
                            </a>
                            <a href="#fitur" class="w-full sm:w-auto px-6 py-3.5 bg-white text-slate-700 font-bold rounded-lg border border-slate-200 hover:bg-slate-50 transition shadow-sm text-center text-sm sm:text-base flex items-center justify-center gap-2">
                                Jelajahi Fitur
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </a>
                        </div>
                    </div>

                    <div class="mt-12 lg:mt-0 lg:col-span-6 relative hidden sm:block">
                        <div class="relative mx-auto w-full max-w-lg lg:max-w-none">
                            <div class="absolute inset-0 -translate-x-4 translate-y-4 bg-slate-100 rounded-2xl transform"></div>
                            
                            <div class="relative bg-white border border-slate-200 rounded-2xl shadow-xl p-4 sm:p-6 flex flex-col gap-4">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                                    <div class="flex gap-2">
                                        <div class="w-3 h-3 rounded-full bg-slate-200"></div>
                                        <div class="w-3 h-3 rounded-full bg-slate-200"></div>
                                    </div>
                                    <div class="h-4 w-1/3 bg-slate-100 rounded"></div>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4 mb-2">
                                    <div class="bg-primary-50 p-4 rounded-xl border border-primary-100">
                                        <div class="h-3 w-16 bg-primary-200 rounded mb-2"></div>
                                        <div class="h-6 w-10 bg-primary-500 rounded"></div>
                                    </div>
                                    <div class="bg-emerald-50 p-4 rounded-xl border border-emerald-100">
                                        <div class="h-3 w-20 bg-emerald-200 rounded mb-2"></div>
                                        <div class="h-6 w-12 bg-emerald-500 rounded"></div>
                                    </div>
                                </div>

                                <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 h-32 flex items-end gap-2 pt-8">
                                    <div class="w-full bg-slate-200 rounded-t h-1/3"></div>
                                    <div class="w-full bg-slate-200 rounded-t h-2/3"></div>
                                    <div class="w-full bg-primary-400 rounded-t h-1/2"></div>
                                    <div class="w-full bg-primary-600 rounded-t h-full"></div>
                                    <div class="w-full bg-slate-200 rounded-t h-3/4"></div>
                                </div>

                                <div class="space-y-3 mt-2">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 shrink-0"></div>
                                        <div class="flex-1"><div class="h-3 w-24 bg-slate-200 rounded mb-1"></div><div class="h-2 w-32 bg-slate-100 rounded"></div></div>
                                        <div class="h-4 w-12 bg-slate-100 rounded-full"></div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 shrink-0"></div>
                                        <div class="flex-1"><div class="h-3 w-20 bg-slate-200 rounded mb-1"></div><div class="h-2 w-28 bg-slate-100 rounded"></div></div>
                                        <div class="h-4 w-16 bg-slate-100 rounded-full"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        @php
            $schools = \App\Models\Tenant::whereNotNull('logo_path')->inRandomOrder()->limit(8)->get();
            $useDummy = $schools->isEmpty(); 
        @endphp
        <section class="py-8 sm:py-10 border-b border-slate-100 overflow-hidden relative bg-white">
            <div class="max-w-7xl mx-auto px-4 text-center mb-6">
                <p class="text-xs sm:text-sm font-semibold text-slate-400 uppercase tracking-widest">Telah dipercaya oleh institusi pendidikan</p>
            </div>
            
            <div class="absolute inset-y-0 left-0 w-16 sm:w-32 bg-gradient-to-r from-white to-transparent z-10 pointer-events-none"></div>
            <div class="absolute inset-y-0 right-0 w-16 sm:w-32 bg-gradient-to-l from-white to-transparent z-10 pointer-events-none"></div>
            
            <div class="flex w-max animate-marquee">
                @for ($i = 0; $i < 4; $i++) 
                    <div class="flex items-center gap-10 sm:gap-16 px-6 sm:px-8 shrink-0 grayscale opacity-50 hover:grayscale-0 hover:opacity-100 transition duration-300">
                        @if(!$useDummy)
                            @foreach($schools as $school)
                                <div class="flex items-center gap-2 sm:gap-3">
                                    <img src="{{ asset('public/uploads/' . $school->logo_path) }}" alt="{{ $school->name }}" class="h-10 w-auto object-contain">
                                    <span class="font-bold text-slate-700 text-sm sm:text-base">{{ $school->name }}</span>
                                </div>
                            @endforeach
                        @else
                             <div class="flex items-center gap-2"><span class="font-bold text-slate-500 text-sm sm:text-lg">SMA NEGERI 1</span></div>
                             <div class="flex items-center gap-2"><span class="font-bold text-slate-500 text-sm sm:text-lg">SMK TEKNOLOGI</span></div>
                             <div class="flex items-center gap-2"><span class="font-bold text-slate-500 text-sm sm:text-lg">SMP BINA BANGSA</span></div>
                             <div class="flex items-center gap-2"><span class="font-bold text-slate-500 text-sm sm:text-lg">MAN 2 KOTA</span></div>
                        @endif
                    </div>
                @endfor
            </div>
        </section>

        <section id="fitur" class="py-16 sm:py-24 bg-slate-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center max-w-3xl mx-auto mb-12 sm:mb-16">
                    <h2 class="text-primary-600 font-bold tracking-wide uppercase text-xs sm:text-sm mb-2">Solusi Menyeluruh</h2>
                    <h3 class="text-3xl sm:text-4xl font-extrabold text-slate-900 leading-tight">Dirancang Khusus untuk Guru BK</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                    
                    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center mb-5 sm:mb-6 border border-blue-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                        </div>
                        <h4 class="text-lg sm:text-xl font-bold text-slate-900 mb-3">Database Siswa</h4>
                        <p class="text-sm sm:text-base text-slate-600 leading-relaxed">Arsipkan biodata, riwayat keluarga, dan catatan khusus siswa secara aman. Pencarian instan kapanpun dibutuhkan.</p>
                    </div>

                    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                        <div class="w-12 h-12 bg-primary-50 text-primary-600 rounded-xl flex items-center justify-center mb-5 sm:mb-6 border border-primary-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        </div>
                        <h4 class="text-lg sm:text-xl font-bold text-slate-900 mb-3">Asesmen Kebutuhan</h4>
                        <p class="text-sm sm:text-base text-slate-600 leading-relaxed">Buat dan sebar angket diagnostik (AKPD) ke siswa secara online. Sistem menganalisis prioritas masalah otomatis.</p>
                    </div>

                    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                        <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center mb-5 sm:mb-6 border border-emerald-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        </div>
                        <h4 class="text-lg sm:text-xl font-bold text-slate-900 mb-3">Layanan Konseling</h4>
                        <p class="text-sm sm:text-base text-slate-600 leading-relaxed">Dokumentasikan layanan konseling individu, kelompok, hingga klasikal lengkap dengan laporan pelaksanaan.</p>
                    </div>

                    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                        <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center mb-5 sm:mb-6 border border-rose-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <h4 class="text-lg sm:text-xl font-bold text-slate-900 mb-3">Catatan Kedisiplinan</h4>
                        <p class="text-sm sm:text-base text-slate-600 leading-relaxed">Rekap poin pelanggaran tata tertib dan prestasi siswa. Peringatan otomatis untuk siswa dalam pantauan.</p>
                    </div>

                    <div class="bg-slate-900 text-white p-6 sm:p-8 rounded-2xl border border-slate-800 shadow-xl md:col-span-2 flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 bg-white/10 text-white rounded-xl flex items-center justify-center mb-5 sm:mb-6 border border-white/20">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <h4 class="text-lg sm:text-2xl font-bold text-white mb-3">Rapor BK & Laporan PDF Otomatis</h4>
                            <p class="text-sm sm:text-base text-slate-300 leading-relaxed mb-6">Akhir semester bukan lagi beban. Cetak Rapor Bimbingan Konseling dengan narasi otomatis berdasarkan data perilaku siswa sepanjang semester. Unduh juga rekap bulanan jurnal guru dengan 1 klik.</p>
                        </div>
                        <div>
                            <span class="inline-flex items-center text-primary-400 text-sm font-semibold">Fitur Auto-Text Generator</span>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <section id="portal-siswa" class="py-16 sm:py-24 bg-white border-t border-slate-100 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="lg:grid lg:grid-cols-2 lg:gap-16 items-center">
                    
                    <div class="mb-12 lg:mb-0">
                        <span class="px-3 py-1.5 rounded-md bg-emerald-50 text-emerald-700 text-xs font-bold tracking-wide border border-emerald-200 uppercase mb-4 inline-block">Terintegrasi</span>
                        <h2 class="text-3xl sm:text-4xl font-extrabold mb-6 text-slate-900 leading-tight">Portal Siswa<br>Kini Ada di Genggaman.</h2>
                        <p class="text-base sm:text-lg text-slate-600 mb-8 leading-relaxed">
                            Siswa bukan sekadar objek data. Berikan mereka akses langsung lewat web untuk berinteraksi dengan layanan bimbingan konseling secara mandiri.
                        </p>
                        
                        <ul class="space-y-4 mb-8">
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <div>
                                    <strong class="text-slate-900 block mb-1">Curhat Online & Anonim</strong>
                                    <span class="text-sm text-slate-600 block">Siswa dapat berkirim pesan secara rahasia langsung ke Guru BK jika takut bertatap muka.</span>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <div>
                                    <strong class="text-slate-900 block mb-1">Asesmen Mandiri via HP</strong>
                                    <span class="text-sm text-slate-600 block">Pengisian instrumen cukup lewat browser HP masing-masing siswa tanpa perlu mengunduh aplikasi tambahan.</span>
                                </div>
                            </li>
                        </ul>
                        
                        <a href="/siswa/login" class="inline-flex items-center justify-center px-6 py-3 bg-emerald-600 text-white font-bold rounded-lg hover:bg-emerald-700 transition w-full sm:w-auto shadow-sm">
                            Coba Login Portal Siswa
                        </a>
                    </div>
                    
                    <div class="relative mx-auto w-full max-w-sm lg:max-w-none">
                        <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 sm:p-6 shadow-lg">
                            <div class="flex items-center gap-4 mb-6 pb-4 border-b border-slate-200">
                                <div class="w-12 h-12 bg-white rounded-full shadow-sm border border-slate-100 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900 text-sm">Anwar Setiawan</div>
                                    <div class="text-xs text-slate-500">Siswa Kelas XII IPA 1</div>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg></div>
                                        <span class="text-sm font-semibold text-slate-800">Tugas Asesmen (1)</span>
                                    </div>
                                    <span class="text-xs font-bold text-red-600 bg-red-50 px-2 py-1 rounded">Belum Diisi</span>
                                </div>
                                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg></div>
                                        <span class="text-sm font-semibold text-slate-800">Konseling Online</span>
                                    </div>
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </section>

        <section id="harga" class="py-16 sm:py-24 bg-slate-50 border-t border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-12 sm:mb-16">
                    <h2 class="text-primary-600 font-bold tracking-wide uppercase text-xs sm:text-sm mb-2">Investasi Pendidikan</h2>
                    <h3 class="text-3xl sm:text-4xl font-extrabold text-slate-900 leading-tight">Harga Transparan. Tanpa Biaya Tersembunyi.</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8 max-w-5xl mx-auto items-start">
                    @php
                        try { $plans = \App\Models\SubscriptionPlan::where('is_active', true)->orderBy('sort_order')->get(); } catch (\Exception $e) { $plans = collect([]); }
                    @endphp
                    @if($plans->count() > 0)
                        @foreach($plans as $plan)
                            <div class="relative flex flex-col p-6 sm:p-8 bg-white border border-slate-200 rounded-2xl shadow-sm hover:shadow-lg transition duration-300 {{ $plan->is_featured ? 'ring-2 ring-primary-500 scale-100 md:scale-105 z-10' : '' }}">
                                
                                @if($plan->is_featured)
                                    <div class="absolute top-0 inset-x-0 -mt-3 flex justify-center">
                                        <span class="bg-primary-600 text-white px-3 py-1 rounded-full text-[10px] sm:text-xs font-bold uppercase tracking-wide shadow-sm">
                                            Rekomendasi
                                        </span>
                                    </div>
                                @endif

                                <h3 class="text-lg font-bold text-slate-900">{{ $plan->name }}</h3>
                                <div class="mt-4 flex flex-wrap items-baseline gap-1">
                                    <span class="text-3xl sm:text-4xl font-extrabold text-slate-900">
                                        @if($plan->price == 0) Gratis @elseif($plan->price >= 1000000) Rp{{ number_format($plan->price / 1000000, fmod($plan->price, 1000000) == 0 ? 0 : 1) }}jt @else Rp{{ number_format($plan->price / 1000, 0) }}rb @endif
                                    </span>
                                    <span class="text-sm font-medium text-slate-500">{{ $plan->duration_label ?? '/Bulan' }}</span>
                                </div>
                                <p class="mt-4 text-sm text-slate-600 min-h-[40px] border-b border-slate-100 pb-5">
                                    {{ $plan->description }}
                                </p>
                                
                                <ul class="mt-5 space-y-3 flex-1 mb-8">
                                    @if(is_array($plan->features))
                                        @foreach($plan->features as $feature)
                                            <li class="flex items-start">
                                                <svg class="h-5 w-5 shrink-0 mr-3 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                <span class="text-sm text-slate-700">{{ $feature }}</span>
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>
                                
                                <a href="{{ $plan->button_url ?? '/app/register' }}" class="mt-auto block w-full py-3 px-6 rounded-lg text-center text-sm font-bold transition border {{ $plan->is_featured ? 'bg-primary-600 text-white border-primary-600 hover:bg-primary-700' : 'bg-white text-slate-900 border-slate-300 hover:bg-slate-50' }}">
                                    {{ $plan->button_label ?? 'Pilih Paket' }}
                                </a>
                            </div>
                        @endforeach
                    @else
                        <div class="col-span-1 md:col-span-3 text-center py-10 text-slate-500 text-sm border border-dashed border-slate-300 rounded-xl">Paket langganan sedang dalam pembaruan.</div>
                    @endif
                </div>
            </div>
        </section>
        
    </main>

    <footer class="bg-white border-t border-slate-200 pt-12 pb-8 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 md:gap-12 mb-8 md:mb-12">
                
                <div class="col-span-1 sm:col-span-2 md:col-span-1 text-center sm:text-left">
                    <div class="flex items-center justify-center sm:justify-start gap-2 mb-4">
                        <div class="bg-primary-600 text-white p-1.5 rounded-lg shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                        </div>
                        <span class="font-bold text-slate-900 text-lg tracking-tight">SIBK-digi</span>
                    </div>
                    <p class="text-slate-500 text-sm leading-relaxed max-w-xs mx-auto sm:mx-0">Platform Bimbingan Konseling digital terdepan untuk sekolah menengah di Indonesia.</p>
                </div>
                
                <div class="text-center sm:text-left">
                    <h4 class="font-bold text-slate-900 mb-3 uppercase tracking-wider text-xs">Menu Cepat</h4>
                    <ul class="space-y-2 text-sm text-slate-600">
                        <li><a href="{{ url('/#fitur') }}" class="hover:text-primary-600 transition-colors">Fitur Aplikasi</a></li>
                        <li><a href="{{ url('/#harga') }}" class="hover:text-primary-600 transition-colors">Harga Berlangganan</a></li>
                        <li><a href="/app/register" class="hover:text-primary-600 transition-colors">Daftar Sekolah</a></li>
                        <li><a href="/siswa/login" class="hover:text-primary-600 transition-colors">Masuk Portal Siswa</a></li>
                    </ul>
                </div>
                
                <div class="text-center sm:text-left">
                    <h4 class="font-bold text-slate-900 mb-3 uppercase tracking-wider text-xs">Pusat Bantuan</h4>
                    <ul class="space-y-2 text-sm text-slate-600">
                        <li><a href="/contact" class="hover:text-primary-600 transition-colors">Hubungi Kami</a></li>
                        <li><a href="/guide" class="hover:text-primary-600 transition-colors">Panduan Penggunaan</a></li>
                        <li><a href="#" class="hover:text-primary-600 transition-colors">FAQ</a></li>
                    </ul>
                </div>
                
                <div class="text-center sm:text-left">
                    <h4 class="font-bold text-slate-900 mb-3 uppercase tracking-wider text-xs">Legal</h4>
                    <ul class="space-y-2 text-sm text-slate-600">
                        <li><a href="/privacy" class="hover:text-primary-600 transition-colors">Kebijakan Privasi</a></li>
                        <li><a href="/terms" class="hover:text-primary-600 transition-colors">Syarat & Ketentuan</a></li>
                        <li><a href="/disclaimer" class="hover:text-primary-600 transition-colors">Sanggahan</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-slate-100 pt-6 sm:pt-8 flex flex-col md:flex-row justify-between items-center text-center md:text-left text-xs sm:text-sm text-slate-500 gap-3 md:gap-0">
                <p>&copy; {{ date('Y') }} SIBK-digi. Hak Cipta Dilindungi.</p>
                <div class="flex gap-4 sm:gap-6 justify-center">
                    <a href="#" class="hover:text-slate-900 transition-colors">Instagram</a>
                    <a href="#" class="hover:text-slate-900 transition-colors">WhatsApp</a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>