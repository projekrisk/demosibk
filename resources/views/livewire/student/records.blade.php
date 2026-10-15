<div class="flex flex-col h-full pb-10">
    <!-- Header -->
    <div class="bg-rose-600 pb-12 pt-6 md:pt-8 px-6 md:px-8 relative flex-shrink-0 border-b border-rose-700">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 20px 20px;"></div>
        <div class="relative z-10 max-w-5xl mx-auto flex items-center gap-4">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center shrink-0 text-white border border-white/20">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <h1 class="text-xl md:text-2xl font-bold text-white tracking-tight">Catatan Kedisiplinan</h1>
                <p class="text-rose-100 text-sm mt-0.5">Rekam jejak prestasi dan pelanggaran selama di sekolah.</p>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="px-4 md:px-8 -mt-6 relative z-20 max-w-5xl mx-auto w-full flex-1">
        @if($records->isEmpty())
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <h3 class="text-base font-bold text-gray-900">Belum ada catatan</h3>
                <p class="mt-1 text-sm text-gray-500 max-w-sm mx-auto">Anda belum memiliki catatan prestasi atau pelanggaran apapun di sistem.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($records as $record)
                    @php
                        $isAchievement = $record->type === 'achievement';
                        $isViolation = $record->type === 'violation';
                        
                        $accentColor = $isAchievement ? 'text-emerald-600' : ($isViolation ? 'text-rose-600' : 'text-amber-600');
                        $bgColor = $isAchievement ? 'bg-emerald-50' : ($isViolation ? 'bg-rose-50' : 'bg-amber-50');
                        $borderColor = $isAchievement ? 'border-emerald-200' : ($isViolation ? 'border-rose-200' : 'border-amber-200');
                        
                        $iconAchievement = 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z';
                        $iconViolation = 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z';
                        $iconOther = 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
                        
                        $icon = $isAchievement ? $iconAchievement : ($isViolation ? $iconViolation : $iconOther);
                        $label = $isAchievement ? 'Prestasi' : ($isViolation ? 'Pelanggaran' : 'Catatan');
                    @endphp
                    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200 flex gap-4 items-start">
                        <div class="w-10 h-10 rounded-lg {{ $bgColor }} flex items-center justify-center shrink-0 border {{ $borderColor }}">
                            <svg class="h-5 w-5 {{ $accentColor }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-[10px] font-bold {{ $accentColor }} uppercase tracking-wide">{{ $label }}</span>
                                <span class="text-xs text-gray-400">&bull; {{ $record->recorded_at->format('d M Y') }}</span>
                            </div>
                            <h3 class="text-sm font-bold text-gray-900 leading-snug">{{ $record->title }}</h3>
                            @if($record->description)
                                <p class="text-sm text-gray-600 mt-1.5 leading-relaxed truncate whitespace-normal line-clamp-2">{{ $record->description }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>