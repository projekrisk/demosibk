<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hubungi Kami - SIBK-digi</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
                        },
                        secondary: {
                            500: '#3b82f6',
                            600: '#2563eb',
                        }
                    },
                    animation: {
                        'blob': 'blob 7s infinite',
                        'marquee': 'marquee 40s linear infinite',
                    },
                    keyframes: {
                        blob: {
                            '0%': { transform: 'translate(0px, 0px) scale(1)' },
                            '33%': { transform: 'translate(30px, -50px) scale(1.1)' },
                            '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                            '100%': { transform: 'translate(0px, 0px) scale(1)' },
                        },
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
        .glass {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body class="bg-[#F8FAFC] text-gray-900 font-sans antialiased overflow-x-hidden selection:bg-primary-500 selection:text-white">

    <nav x-data="{ mobileMenuOpen: false }" class="fixed w-full z-50 glass transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <a href="/" class="flex items-center gap-2 cursor-pointer">
                    <div class="bg-gradient-to-br from-primary-600 to-secondary-600 text-white p-2 rounded-xl shadow-lg shadow-primary-500/30">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                    </div>
                    <span class="text-xl font-bold text-gray-900 tracking-tight">SIBK-digi</span>
                </a>

                <div class="hidden md:flex items-center gap-8">
                    <a href="{{ url('/#fitur') }}" class="text-sm font-medium text-gray-600 hover:text-primary-600 transition">Fitur</a>
                    <a href="{{ url('/#harga') }}" class="text-sm font-medium text-gray-600 hover:text-primary-600 transition">Harga</a>
                    <div class="flex items-center gap-3 pl-4 border-l border-gray-200">
                        @auth
                            <a href="/app" class="group flex items-center gap-2 px-5 py-2.5 bg-primary-900 text-white rounded-full font-medium hover:bg-primary-800 transition shadow-lg shadow-primary-900/20">
                                Dashboard
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        @else
                            <a href="/app/login" class="text-sm font-semibold text-gray-900 hover:text-primary-600 px-4 py-2 transition">Masuk</a>
                            <a href="/app/register" class="group flex items-center gap-2 px-5 py-2.5 bg-primary-600 text-white rounded-full font-medium hover:bg-primary-700 transition shadow-lg shadow-primary-500/30">
                                Coba Gratis
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        @endauth
                    </div>
                </div>

                <div class="flex md:hidden items-center">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="text-gray-600 hover:text-primary-600 p-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            <path x-show="mobileMenuOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div x-show="mobileMenuOpen" x-cloak class="md:hidden bg-white/95 backdrop-blur-md border-b border-gray-100 absolute w-full left-0 shadow-xl z-40">
            <div class="px-4 pt-4 pb-6 space-y-3">
                <a href="{{ url('/#fitur') }}" class="block px-3 py-3 rounded-lg text-base font-medium text-gray-700 hover:bg-primary-50 hover:text-primary-700">Fitur Unggulan</a>
                <a href="{{ url('/#harga') }}" class="block px-3 py-3 rounded-lg text-base font-medium text-gray-700 hover:bg-primary-50 hover:text-primary-700">Paket Harga</a>
                <div class="grid grid-cols-2 gap-3 mt-4 pt-4 border-t border-gray-100">
                    <a href="/app/login" class="flex justify-center items-center px-4 py-3 text-base font-bold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl">Masuk</a>
                    <a href="/app/register" class="flex justify-center items-center px-4 py-3 text-base font-bold text-white bg-primary-600 hover:bg-primary-700 rounded-xl shadow-lg shadow-primary-500/20">Daftar</a>
                </div>
            </div>
        </div>
    </nav>

    <section class="relative pt-32 pb-20 overflow-hidden min-h-screen" style="align-content: center;">
        <div class="absolute top-0 left-1/2 w-full -translate-x-1/2 h-full z-0 pointer-events-none">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-primary-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
            <div class="absolute top-0 right-1/4 w-96 h-96 bg-blue-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
            <div class="absolute -bottom-32 left-1/3 w-96 h-96 bg-indigo-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-4000"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-primary-600 font-bold tracking-wide uppercase text-sm mb-2">Bantuan & Dukungan</h2>
                <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-6">Hubungi Kami</h1>
                <p class="text-xl text-gray-500">
                    Punya pertanyaan tentang fitur atau kendala teknis? Tim kami siap membantu sekolah Anda bertransformasi.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <div class="bg-white/80 backdrop-blur-sm p-8 rounded-3xl border border-white/50 shadow-xl hover:shadow-2xl transition duration-300 flex flex-col items-center text-center group">
                    <div class="w-16 h-16 bg-primary-50 text-primary-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-sm">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Email Dukungan</h3>
                    <p class="text-gray-500 mb-6">Kirim pertanyaan Anda kapan saja, kami akan membalas dalam 24 jam.</p>
                    <a href="mailto:support@sibk-digi.com" class="text-primary-600 font-bold hover:text-primary-700 text-lg">support@sibk-digi.com</a>
                </div>

                <div class="bg-white/80 backdrop-blur-sm p-8 rounded-3xl border border-white/50 shadow-xl hover:shadow-2xl transition duration-300 flex flex-col items-center text-center group">
                    <div class="w-16 h-16 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-sm">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">WhatsApp</h3>
                    <p class="text-gray-500 mb-6">Chat langsung dengan tim support kami. Senin - Jumat, 08:00 - 17:00 WIB.</p>
                    <a href="https://wa.me/6281311665601" target="_blank" class="inline-flex items-center px-8 py-3 bg-green-500 text-white font-bold rounded-full hover:bg-green-600 transition shadow-lg shadow-green-500/30 transform hover:-translate-y-1">
                        Chat Sekarang
                    </a>
                </div>

                <div class="bg-white/80 backdrop-blur-sm p-8 rounded-3xl border border-white/50 shadow-xl hover:shadow-2xl transition duration-300 flex flex-col items-center text-center group">
                    <div class="w-16 h-16 bg-gray-50 text-gray-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-sm">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Alamat</h3>
                    <p class="text-gray-500 mb-4">Datang dan kunjungi kami untuk konsultasi langsung.</p>
                    <p class="text-gray-800 font-medium">
                        Jln. Raya Bayah - Malingping<br>
                        Lebak - Banten, 42391
                    </p>
                </div>
            </div>

            <div class="mt-20 text-center">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/50 border border-white/60 shadow-sm backdrop-blur-sm">
                    <span class="flex h-2 w-2 rounded-full bg-green-500"></span>
                    <span class="text-sm font-medium text-gray-600">Jam Operasional: Senin - Jumat, 08:00 - 17:00 WIB</span>
                </div>
            </div>
        </div>
    </section>

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
                </div>
            </div>
        </div>
    </footer>

</body>
</html>