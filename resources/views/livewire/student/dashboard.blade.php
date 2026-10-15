<div class="flex flex-col h-full pb-10">
    <!-- Hero Section -->
    <div class="bg-indigo-600 pb-12 pt-6 md:pt-8 px-6 md:px-8 relative flex-shrink-0 border-b border-indigo-700">
        <!-- Subtle Pattern -->
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 20px 20px;"></div>
        
        <div class="relative z-10 max-w-6xl mx-auto flex items-center justify-between">
            <div>
                <p class="text-indigo-200 text-sm font-medium mb-0.5">Dashboard Utama</p>
                <h1 class="text-2xl md:text-3xl font-bold text-white tracking-tight">
                    Halo, {{ explode(' ', $student->name)[0] }}
                </h1>
            </div>
            <div class="hidden md:block text-right">
                <p class="text-indigo-100 text-sm font-medium">{{ $student->class_name }}</p>
                <p class="text-indigo-200 text-xs">NISN: {{ $student->nisn }}</p>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="px-4 md:px-8 -mt-6 relative z-20 max-w-6xl mx-auto w-full flex-shrink-0">
        <div class="grid grid-cols-3 gap-3 md:gap-5">
            <!-- Stat 1 -->
            <div class="bg-white rounded-xl p-4 md:p-5 shadow-sm border border-gray-200 flex flex-col items-center justify-center text-center">
                <div class="w-8 h-8 md:w-10 md:h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center mb-2">
                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                </div>
                <p class="text-xl md:text-2xl font-bold text-gray-900 leading-none">{{ $packages->count() }}</p>
                <p class="text-[10px] md:text-xs font-medium text-gray-500 mt-1">Asesmen</p>
            </div>
            
            <!-- Stat 2 -->
            <a href="{{ route('student.curhat') }}" class="bg-white rounded-xl p-4 md:p-5 shadow-sm border border-gray-200 flex flex-col items-center justify-center text-center hover:border-emerald-300 hover:shadow-md transition">
                <div class="w-8 h-8 md:w-10 md:h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center mb-2">
                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                </div>
                <p class="text-xl md:text-2xl font-bold text-gray-900 leading-none">{{ $student->messages()->count() }}</p>
                <p class="text-[10px] md:text-xs font-medium text-gray-500 mt-1">Konseling</p>
            </a>
            
            <!-- Stat 3 -->
            <a href="{{ route('student.records') }}" class="bg-white rounded-xl p-4 md:p-5 shadow-sm border border-gray-200 flex flex-col items-center justify-center text-center hover:border-rose-300 hover:shadow-md transition">
                <div class="w-8 h-8 md:w-10 md:h-10 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center mb-2">
                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <p class="text-xl md:text-2xl font-bold text-gray-900 leading-none">{{ $student->records()->count() }}</p>
                <p class="text-[10px] md:text-xs font-medium text-gray-500 mt-1">Catatan</p>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="px-4 md:px-8 py-8 max-w-6xl mx-auto w-full flex-1">
        
        @if(session()->has('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-3 text-sm">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-900">Tugas Asesmen Anda</h2>
        </div>

        @if($packages->isEmpty())
            <div class="bg-white rounded-xl p-8 text-center border border-gray-200 shadow-sm">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <h3 class="text-base font-bold text-gray-900 mb-1">Semua Selesai!</h3>
                <p class="text-sm text-gray-500">Saat ini tidak ada angket asesmen yang perlu Anda kerjakan.</p>
            </div>
        @else
            <!-- PERUBAHAN: Daftar Asesmen Berbentuk Baris (List) -->
            <div class="space-y-4">
                @foreach($packages as $pkg)
                    <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4 {{ isset($pkg->is_completed) && $pkg->is_completed ? 'opacity-60 bg-gray-50' : 'hover:border-indigo-300 transition-colors' }}">
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                <span class="text-[10px] font-bold {{ isset($pkg->is_completed) && $pkg->is_completed ? 'text-gray-500 bg-gray-200 border-gray-300' : 'text-indigo-700 bg-indigo-50 border-indigo-100' }} px-2 py-0.5 rounded border uppercase tracking-wide">
                                    Angket BK
                                </span>
                                
                                @if($pkg->deadline_at && (!isset($pkg->is_completed) || !$pkg->is_completed))
                                    <span class="text-[10px] font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded border border-red-100 uppercase tracking-wide flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Batas: {{ $pkg->deadline_at->format('d M Y') }}
                                    </span>
                                @endif

                                @if(isset($pkg->is_completed) && $pkg->is_completed)
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded border border-emerald-200 uppercase tracking-wide flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Selesai Hari Ini
                                    </span>
                                @endif
                            </div>
                            
                            <h3 class="text-base font-bold {{ isset($pkg->is_completed) && $pkg->is_completed ? 'text-gray-500' : 'text-gray-900' }} mb-1">{{ $pkg->title }}</h3>
                            
                            <div class="flex items-center gap-2 text-xs font-medium text-gray-500">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span>{{ $pkg->questions_count }} Pertanyaan</span>
                                </div>
                            </div>
                        </div>
                        <div class="shrink-0 w-full md:w-auto">
                            @if(isset($pkg->is_completed) && $pkg->is_completed)
                                <button disabled class="block w-full md:w-auto px-6 py-2.5 bg-gray-200 text-gray-500 text-center rounded-lg text-sm font-semibold cursor-not-allowed border border-gray-300">
                                    Selesai Dikerjakan
                                </button>
                            @else
                                <a href="{{ route('student.assessment.take', $pkg->id) }}" class="block w-full md:w-auto px-6 py-2.5 bg-indigo-50 text-indigo-700 text-center rounded-lg text-sm font-semibold hover:bg-indigo-600 hover:text-white transition-colors">
                                    Mulai Kerjakan
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Quick Action Banner -->
        <div class="mt-8 bg-emerald-50 border border-emerald-200 rounded-xl p-5 md:p-6 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-start md:items-center gap-4">
                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center shrink-0 text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 text-base">Punya beban pikiran?</h3>
                    <p class="text-gray-600 text-sm mt-0.5">Konselor siap mendengarkan cerita Anda secara rahasia.</p>
                </div>
            </div>
            <a href="{{ route('student.curhat') }}" class="whitespace-nowrap px-5 py-2.5 bg-emerald-600 text-white font-medium text-sm rounded-lg hover:bg-emerald-700 transition w-full md:w-auto text-center">
                Mulai Curhat
            </a>
        </div>
    </div>
</div>