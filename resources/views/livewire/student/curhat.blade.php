<div x-data="{ showFormModal: {{ $errors->any() ? 'true' : 'false' }}, detailModal: null }" class="flex flex-col h-full pb-10">
    <!-- Header -->
    <div class="bg-emerald-600 pb-12 pt-6 md:pt-8 px-6 md:px-8 relative flex-shrink-0 border-b border-emerald-700">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 20px 20px;"></div>
        <div class="relative z-10 max-w-4xl mx-auto flex items-center gap-4">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center shrink-0 text-white border border-white/20">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
            </div>
            <div>
                <h1 class="text-xl md:text-2xl font-bold text-white tracking-tight">Layanan Konseling</h1>
                <p class="text-emerald-100 text-sm mt-0.5">Sampaikan masalahmu secara aman dan rahasia.</p>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="px-4 md:px-8 -mt-6 relative z-20 max-w-4xl mx-auto w-full flex-1">
        @if(session()->has('success'))
            <!-- x-init digunakan untuk memaksa modal form menutup saat sukses -->
            <div x-init="showFormModal = false" class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3.5 rounded-xl flex items-center gap-3 text-sm shadow-sm animate-fade-in">
                <svg class="w-5 h-5 flex-shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
        @endif

        <!-- KOTAK AREA HISTORI -->
        <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden mb-8">
            
            <!-- Action Bar & Header Histori -->
            <div class="px-6 md:px-8 py-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gray-50/50">
                <div>
                    <h2 class="text-xl font-extrabold text-gray-900 tracking-tight">Riwayat Percakapan</h2>
                    <p class="text-sm text-gray-500 mt-1">Daftar pesan dan balasan dari Guru BK Anda.</p>
                </div>
                <button @click="showFormModal = true" class="px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl text-sm font-extrabold shadow-md shadow-emerald-200 hover:shadow-lg hover:from-emerald-600 hover:to-teal-700 transition-all flex items-center justify-center gap-2 transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tulis Pesan Baru
                </button>
            </div>

            <!-- List Histori (Lebih Ringkas) -->
            <div class="p-6 md:p-8">
                <div class="space-y-4">
                    @if($messages->isEmpty())
                        <div class="py-12 text-center h-full flex flex-col justify-center">
                            <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                            <h3 class="text-lg font-extrabold text-gray-900">Belum ada percakapan</h3>
                            <p class="text-gray-500 text-sm mt-2 max-w-sm mx-auto">Mulai ceritakan masalahmu menggunakan tombol "Tulis Pesan Baru" di atas.</p>
                        </div>
                    @else
                        <!-- TAMPILAN PER BARIS -->
                        <div class="space-y-4">
                            @foreach($messages as $msg)
                                <!-- KARTU LIST RINGKAS -->
                                <div @click="detailModal = {{ $msg->id }}" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 hover:shadow-md hover:border-emerald-400 transition duration-300 cursor-pointer group flex flex-col sm:flex-row justify-between min-h-[80px] gap-4">
                                    <div class="flex flex-col justify-center flex-1">
                                        <h3 class="text-base font-bold text-gray-900 group-hover:text-emerald-700 transition leading-snug line-clamp-2 mb-1">{{ $msg->subject }}</h3>
                                        <div class="flex flex-wrap items-center gap-3 text-xs font-medium text-gray-500 mt-1">
                                            <div class="flex items-center gap-1.5 text-gray-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> 
                                                {{ $msg->created_at->format('d M Y') }}
                                            </div>
                                            <span>&bull;</span>
                                            <div class="truncate">
                                                Kepada: <span class="text-gray-700 font-bold">{{ $msg->targetCounselor ? $msg->targetCounselor->name : 'Semua Guru BK' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="shrink-0 flex items-center sm:items-start mt-2 sm:mt-0">
                                        @if($msg->replied_at)
                                            <span class="px-3 py-1.5 bg-emerald-100 text-emerald-800 text-[11px] font-bold rounded-md border border-emerald-200 uppercase tracking-wider">Dibalas</span>
                                        @else
                                            <span class="px-3 py-1.5 bg-amber-100 text-amber-800 text-[11px] font-bold rounded-md border border-amber-200 uppercase tracking-wider">Menunggu</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL FORM POPUP (CREATE PESAN) -->
    <div x-show="showFormModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="showFormModal" x-transition.opacity class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="showFormModal = false"></div>

        <div x-show="showFormModal" x-transition.scale class="relative bg-white rounded-3xl shadow-2xl border border-gray-100 w-full max-w-xl max-h-[90vh] flex flex-col overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-white shrink-0">
                <h3 class="text-lg font-extrabold text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                    Tulis Pesan Baru
                </h3>
                <button type="button" @click="showFormModal = false" class="text-gray-400 hover:text-gray-700 bg-gray-50 hover:bg-gray-100 p-2 rounded-full transition-colors focus:outline-none">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form wire:submit="submit" class="flex flex-col flex-1 overflow-hidden">
                <div class="p-6 overflow-y-auto flex-1 space-y-6">
                    
                    <!-- DROPDOWN GURU BK -->
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Tujuan (Opsional)</label>
                        <div x-data="{ 
                            open: false, 
                            selectedId: @entangle('counselor_id'),
                            counselors: [
                                @foreach($counselors as $counselor)
                                    { id: {{ $counselor->id }}, name: '{{ addslashes($counselor->name) }}' },
                                @endforeach
                            ],
                            get selectedName() {
                                if (!this.selectedId) return 'Semua Guru BK';
                                const found = this.counselors.find(c => c.id == this.selectedId);
                                return found ? found.name : 'Semua Guru BK';
                            }
                        }" @click.outside="open = false" class="relative" wire:ignore.self>
                            <button type="button" @click.prevent="open = !open" class="w-full flex items-center justify-between rounded-xl border border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none text-sm p-3.5 bg-white transition-all text-left group hover:bg-gray-50">
                                <span x-text="selectedName" class="text-gray-900 font-medium truncate pr-4" :class="!selectedId ? 'text-gray-500' : ''"></span>
                                <svg class="w-5 h-5 text-gray-400 transition-transform duration-200 flex-shrink-0 group-hover:text-gray-600" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-transition.opacity.duration.200ms x-cloak class="absolute z-50 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden flex flex-col">
                                <ul class="max-h-52 overflow-y-auto p-1.5 space-y-0.5">
                                    <li @click="selectedId = null; open = false" class="px-3 py-2.5 text-sm rounded-lg cursor-pointer transition-colors flex items-center justify-between" :class="!selectedId ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-gray-700 hover:bg-gray-100 font-medium'">
                                        <span>Semua Guru BK</span>
                                        <svg x-show="!selectedId" class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </li>
                                    @foreach($counselors as $counselor)
                                        <li @click="selectedId = {{ $counselor->id }}; open = false" class="px-3 py-2.5 text-sm rounded-lg cursor-pointer transition-colors flex items-center justify-between" :class="selectedId == {{ $counselor->id }} ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-gray-700 hover:bg-gray-100 font-medium'">
                                            <span class="truncate pr-2">{{ $counselor->name }}</span>
                                            <svg x-show="selectedId == {{ $counselor->id }}" class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        @error('counselor_id') <span class="text-red-500 text-xs mt-1.5 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <!-- TOPIK INPUT -->
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Topik / Judul</label>
                        <input type="text" wire:model="subject" class="w-full rounded-xl border border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none text-sm p-3.5 bg-white hover:bg-gray-50 transition-all font-medium text-gray-900" placeholder="Contoh: Susah fokus belajar...">
                        @error('subject') <span class="text-red-500 text-xs mt-1.5 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <!-- PESAN TEXTAREA -->
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Isi Pesan</label>
                        <textarea wire:model="message" rows="4" class="w-full rounded-xl border border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none text-sm p-3.5 bg-white hover:bg-gray-50 transition-all font-medium text-gray-900 resize-none" placeholder="Ceritakan detail masalahmu di sini..."></textarea>
                        @error('message') <span class="text-red-500 text-xs mt-1.5 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <!-- CHECKBOX ANONIM -->
                    <label class="flex items-start bg-gray-50/80 hover:bg-emerald-50/50 p-4 rounded-xl border border-gray-200 cursor-pointer transition-colors group">
                        <div class="flex items-center h-5 mt-0.5">
                            <input type="checkbox" wire:model="is_anonymous" class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500 cursor-pointer">
                        </div>
                        <div class="ml-3 text-sm flex-1">
                            <span class="font-bold text-gray-900 group-hover:text-emerald-800 transition-colors">Kirim secara Anonim (Rahasia)</span>
                            <p class="text-gray-500 text-xs mt-1 leading-relaxed">Identitas nama dan kelasmu akan disembunyikan dari Guru BK.</p>
                        </div>
                    </label>

                </div>

                <!-- Modal Footer & Tombol Loading -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex gap-3 shrink-0">
                    <button type="button" @click="showFormModal = false" class="flex-1 py-3 bg-white border border-gray-300 text-gray-700 rounded-xl text-sm font-bold shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-all">
                        Batal
                    </button>
                    <!-- Animasi Loading & Disabled Saat Pengiriman -->
                    <button type="submit" wire:loading.attr="disabled" class="flex-1 py-3 bg-emerald-600 text-white rounded-xl text-sm font-bold shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all flex justify-center items-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="submit" class="flex items-center gap-2">
                            Kirim Pesan
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        </span>
                        <span wire:loading wire:target="submit" class="flex items-center gap-2">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Mengirim...
                        </span>
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- MODAL DETAIL PERCAKAPAN (MUNCUL SAAT LIST DIKLIK) -->
    @foreach($messages as $msg)
        <div x-show="detailModal === {{ $msg->id }}" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4 sm:p-6" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <!-- Background Overlay -->
            <div x-show="detailModal === {{ $msg->id }}" x-transition.opacity class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="detailModal = null"></div>

            <!-- Modal Panel -->
            <div x-show="detailModal === {{ $msg->id }}" x-transition.scale class="relative bg-white rounded-3xl shadow-2xl border border-gray-100 w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden">
                
                <!-- Modal Header -->
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-white shrink-0">
                    <h3 class="text-lg font-extrabold text-gray-900">Detail Percakapan</h3>
                    <button type="button" @click="detailModal = null" class="text-gray-400 hover:text-gray-700 bg-gray-50 hover:bg-gray-100 p-2 rounded-full transition-colors focus:outline-none">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 overflow-y-auto flex-1 space-y-6 bg-gray-50/50">
                    <!-- Balon Pesan Siswa -->
                    <div class="bg-white p-5 md:p-6 rounded-2xl border border-gray-200 shadow-sm">
                        <div class="flex items-center gap-3 mb-4 border-b border-gray-100 pb-4">
                            <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900">{{ $msg->is_anonymous ? 'Anda (Anonim)' : 'Anda' }}</p>
                                <p class="text-xs text-gray-500">{{ $msg->created_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                        <h4 class="font-bold text-gray-900 text-lg mb-2">{{ $msg->subject }}</h4>
                        <p class="text-sm text-gray-800 leading-relaxed whitespace-pre-wrap">{{ $msg->message }}</p>
                    </div>

                    <!-- Balon Balasan Guru BK -->
                    @if($msg->response)
                        <div class="bg-emerald-50/60 p-5 md:p-6 rounded-2xl border border-emerald-100 shadow-sm">
                            <div class="flex items-center gap-3 mb-4 border-b border-emerald-100/60 pb-4">
                                <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900">{{ $msg->replier ? $msg->replier->name : 'Guru BK' }}</p>
                                    <p class="text-xs text-emerald-600 font-medium">{{ $msg->replied_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                            <p class="text-sm text-gray-800 leading-relaxed whitespace-pre-wrap">{{ $msg->response }}</p>
                        </div>
                    @else
                        <!-- Status Menunggu Balasan -->
                        <div class="bg-amber-50 border border-amber-100 p-6 rounded-2xl text-center flex flex-col items-center justify-center gap-2 shadow-sm">
                            <svg class="w-10 h-10 text-amber-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-sm font-bold text-amber-800">Menunggu Balasan</p>
                            <p class="text-xs text-amber-600 max-w-xs">Pesan Anda telah masuk dan sedang menunggu antrean untuk dibaca oleh Guru BK.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endforeach

</div>