<div class="flex flex-col h-full min-h-screen relative bg-gray-50">
    
    <!-- Header -->
    <div class="bg-amber-500 pb-12 pt-6 md:pt-8 px-4 md:px-8 relative flex-shrink-0 z-10 border-b border-amber-600">
        <div class="relative z-10 max-w-4xl mx-auto flex items-center gap-4">
            <a href="{{ route('student.dashboard') }}" class="p-2 bg-white/20 rounded-lg hover:bg-white/30 transition text-white border border-white/20 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div class="flex-1 min-w-0">
                <h1 class="text-lg md:text-xl font-bold text-white tracking-tight truncate">{{ $packageTitle }}</h1>
                <p class="text-amber-100 text-xs md:text-sm mt-0.5">Bagian {{ $currentSegmentIndex + 1 }} dari {{ count($segments) }}</p>
            </div>
        </div>
        
        <!-- Progress Bar -->
        <div class="max-w-4xl mx-auto mt-5 bg-black/10 h-1.5 w-full rounded-full overflow-hidden">
            <div class="bg-white h-full transition-all duration-300 rounded-full" style="width: {{ (($currentSegmentIndex) / count($segments)) * 100 }}%"></div>
        </div>
    </div>

    <!-- Area Pertanyaan -->
    <div class="px-4 md:px-8 -mt-6 relative z-20 max-w-4xl mx-auto flex-1 w-full pb-24" id="assessment-container">
        @if(!empty($segments))
            @php $currentSegment = $segments[$currentSegmentIndex]; @endphp
            
            <div class="mb-5 bg-white p-4 border border-gray-200 rounded-xl shadow-sm">
                <span class="inline-block px-2.5 py-1 bg-amber-50 text-amber-700 rounded text-[10px] font-bold mb-1 uppercase tracking-wider border border-amber-100">
                    Kategori: {{ $currentSegment['category_name'] }}
                </span>
                <p class="text-sm font-medium text-gray-600">Pilih atau isi jawaban yang paling sesuai dengan kondisi Anda saat ini.</p>
                <div class="mt-2 text-xs text-amber-600 flex items-center gap-1 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Jawaban otomatis tersimpan
                </div>
            </div>

            <div class="space-y-4">
                @foreach($currentSegment['questions'] as $index => $q)
                    @php $hasError = $errors->has('answers.'.$q['id']); @endphp
                    <!-- PERBAIKAN: Kotak soal akan berubah merah menyala jika belum dijawab -->
                    <div wire:key="question-{{ $q['id'] }}" id="question-{{ $q['id'] }}" class="bg-white p-5 md:p-6 rounded-xl shadow-sm border {{ $hasError ? 'border-red-400 ring-4 ring-red-50' : 'border-gray-200' }} transition-all duration-300">
                        <div class="flex gap-3">
                            <span class="font-bold {{ $hasError ? 'text-red-500' : 'text-gray-400' }} mt-0.5">{{ $index + 1 }}.</span>
                            <div class="flex-1">
                                <p class="text-sm md:text-base text-gray-900 font-medium mb-3 leading-relaxed">{{ $q['question_text'] }}</p>
                                
                                @if($hasError) 
                                    <div class="text-red-600 text-xs font-bold bg-red-50 px-3 py-1.5 rounded-md w-fit mb-4 flex items-center gap-1.5 border border-red-100">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        Belum dijawab
                                    </div> 
                                @endif

                                @if($q['type'] == 'guttman' || $q['type'] == 'checklist')
                                    <div class="grid grid-cols-2 gap-3">
                                        <label class="cursor-pointer">
                                            <!-- PERBAIKAN: Gunakan wire:model.live -->
                                            <input type="radio" wire:model.live="answers.{{ $q['id'] }}" name="q_{{ $q['id'] }}" value="1" class="peer sr-only">
                                            <div class="text-center py-2.5 rounded-lg border border-gray-300 peer-checked:border-amber-500 peer-checked:bg-amber-50 peer-checked:text-amber-700 text-gray-600 text-sm font-semibold transition">
                                                Ya / Sesuai
                                            </div>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" wire:model.live="answers.{{ $q['id'] }}" name="q_{{ $q['id'] }}" value="0" class="peer sr-only">
                                            <div class="text-center py-2.5 rounded-lg border border-gray-300 peer-checked:border-amber-500 peer-checked:bg-amber-50 peer-checked:text-amber-700 text-gray-600 text-sm font-semibold transition">
                                                Tidak
                                            </div>
                                        </label>
                                    </div>
                                @elseif($q['type'] == 'likert' || $q['type'] == 'scale')
                                    <div class="space-y-2">
                                        @foreach(['4'=>'Sangat Sesuai', '3'=>'Sesuai', '2'=>'Kurang Sesuai', '1'=>'Tidak Sesuai'] as $val => $label)
                                        <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition [&:has(input:checked)]:border-amber-500 [&:has(input:checked)]:bg-amber-50">
                                            <input type="radio" wire:model.live="answers.{{ $q['id'] }}" name="q_{{ $q['id'] }}" value="{{ $val }}" class="w-4 h-4 text-amber-600 border-gray-300 focus:ring-amber-500">
                                            <span class="ml-3 text-sm font-medium text-gray-700">{{ $label }}</span>
                                        </label>
                                        @endforeach
                                    </div>
                                @elseif($q['type'] == 'rating')
                                    <div class="bg-gray-50 p-3 rounded-xl border border-gray-200 shadow-sm">
                                        <div class="grid grid-cols-5 sm:grid-cols-10 gap-2 mb-2">
                                            @for($i=1; $i<=10; $i++)
                                            <label class="cursor-pointer w-full">
                                                <input type="radio" wire:model.live="answers.{{ $q['id'] }}" name="q_{{ $q['id'] }}" value="{{ $i }}" class="peer sr-only">
                                                <div class="w-full aspect-square flex items-center justify-center rounded-lg text-sm font-bold text-gray-700 bg-white border border-gray-200 peer-checked:border-amber-500 peer-checked:bg-amber-500 peer-checked:text-white hover:bg-gray-100 transition shadow-sm">
                                                    {{ $i }}
                                                </div>
                                            </label>
                                            @endfor
                                        </div>
                                        <div class="flex justify-between text-[10px] text-gray-500 uppercase font-extrabold px-1 tracking-wider">
                                            <span>1 = Rendah</span>
                                            <span>10 = Tinggi</span>
                                        </div>
                                    </div>
                                @else
                                    <!-- Menggunakan modifier debounce khusus untuk input teks agar tidak kirim data setiap ketikan -->
                                    <textarea wire:model.live.debounce.1s="answers.{{ $q['id'] }}" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 p-3 text-sm" placeholder="Tulis jawaban di sini..."></textarea>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Bottom Action Bar -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-3 md:p-4 z-40 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
        <div class="max-w-4xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
            
            <!-- PERBAIKAN: Banner Error sebelum tombol lanjut -->
            <div class="w-full md:w-auto">
                @if($errors->any())
                    <div class="text-red-600 text-sm font-bold flex items-center justify-center md:justify-start gap-2 bg-red-50 px-4 py-2.5 rounded-xl border border-red-200 animate-pulse">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Ada {{ count($errors->messages()) }} soal belum dijawab!
                    </div>
                @else
                    <span class="text-sm text-gray-500 font-medium hidden md:inline-block bg-gray-50 px-4 py-2 rounded-lg border border-gray-100">Pastikan semua soal terisi sebelum lanjut.</span>
                @endif
            </div>

            <button wire:click="nextSegment" class="w-full md:w-auto px-8 py-3.5 bg-amber-500 text-white rounded-xl text-sm font-extrabold shadow-md shadow-amber-200 hover:bg-amber-600 transition-all flex justify-center items-center gap-2 transform hover:-translate-y-0.5">
                <span wire:loading.remove wire:target="nextSegment">
                    {{ $currentSegmentIndex < count($segments) - 1 ? 'Lanjut ke Halaman Berikutnya' : 'Selesai & Kirim Asesmen' }}
                </span>
                <span wire:loading wire:target="nextSegment" class="flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Memproses...
                </span>
            </button>
        </div>
    </div>
</div>

<!-- PERBAIKAN SCRIPT AUTO-SCROLL & ALERT -->
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('scroll-to-top', () => {
            const mainScrollArea = document.querySelector('main');
            if (mainScrollArea) {
                mainScrollArea.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });

        // Menghapus alert bawaan browser dan menggantinya dengan scroll otomatis ke soal yang belum diisi
        Livewire.on('validation-failed', () => {
            setTimeout(() => {
                const firstError = document.querySelector('.border-red-400');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }, 100); // Sedikit jeda agar Livewire selesai merender border merah
        });
    });
</script>