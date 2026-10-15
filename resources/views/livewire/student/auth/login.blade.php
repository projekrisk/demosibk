<div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-200">
        <!-- Header Gradient -->
        <div class="bg-gradient-to-b from-indigo-600 to-indigo-800 text-white p-8 pb-12 rounded-b-[40px] shadow-lg flex-shrink-0">
            <div class="flex justify-center mb-6">
                <div class="bg-white/20 p-3 rounded-xl backdrop-blur-sm">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                </div>
            </div>
            <h1 class="text-3xl font-bold text-center">Portal Siswa</h1>
            <p class="text-indigo-200 text-center mt-2 text-sm">Masuk untuk layanan Bimbingan Konseling</p>
        </div>

        <!-- Form Area -->
        <div class="px-6 -mt-8 relative z-10 pb-8">
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-2 border border-gray-100">
                
                <!-- SATU FORM INDUK UNTUK SEMUA STEP -->
                <form wire:submit="submit" class="space-y-4">
                    
                    @if($step == 1)
                        <!-- STEP 1: CEK DATA -->
                        <h2 class="text-lg font-bold text-gray-800 mb-4">Cek Data Siswa</h2>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">NPSN Sekolah</label>
                            <input type="number" wire:model="npsn" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-3 border bg-gray-50" placeholder="Masukkan NPSN" required>
                            @error('npsn') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Induk Siswa (NIS)</label>
                            <input type="number" wire:model="nis" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-3 border bg-gray-50" placeholder="Masukkan NIS" required>
                            @error('nis') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">NISN</label>
                            <input type="number" wire:model="nisn" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-3 border bg-gray-50" placeholder="Masukkan NISN" required>
                            @error('nisn') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <button type="submit" class="w-full py-3.5 bg-indigo-600 text-white rounded-xl font-bold shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition mt-2">
                            <span wire:loading.remove wire:target="submit">Lanjutkan</span>
                            <span wire:loading wire:target="submit">Memeriksa...</span>
                        </button>

                    @elseif($step == 2)
                        <!-- STEP 2: LOGIN NORMAL -->
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-bold text-gray-800">Halo, {{ explode(' ', $studentName)[0] }}</h2>
                            <button type="button" wire:click="resetStep" class="text-sm text-indigo-600 underline">Ganti Akun</button>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kata Sandi</label>
                            <input type="password" wire:model="password" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-3 border bg-gray-50" placeholder="Masukkan kata sandi" required>
                            @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <button type="submit" class="w-full py-3.5 bg-indigo-600 text-white rounded-xl font-bold shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition mt-2">
                            <span wire:loading.remove wire:target="submit">Masuk Sekarang</span>
                            <span wire:loading wire:target="submit">Memproses...</span>
                        </button>

                    @elseif($step == 3)
                        <!-- STEP 3: AKTIVASI SANDI BARU -->
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-bold text-gray-800">Aktivasi Akun</h2>
                            <button type="button" wire:click="resetStep" class="text-sm text-indigo-600 underline">Batal</button>
                        </div>
                        <p class="text-sm text-gray-500 mb-4">Buat kata sandi baru untuk akun Anda ({{ $studentName }}).</p>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kata Sandi Baru</label>
                            <input type="password" wire:model="password" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-3 border bg-gray-50" placeholder="Minimal 6 karakter" required>
                            @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ulangi Kata Sandi</label>
                            <input type="password" wire:model="password_confirmation" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-3 border bg-gray-50" placeholder="Ketik ulang kata sandi" required>
                        </div>
                        <button type="submit" class="w-full py-3.5 bg-indigo-600 text-white rounded-xl font-bold shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition mt-2">
                            <span wire:loading.remove wire:target="submit">Aktifkan & Masuk</span>
                            <span wire:loading wire:target="submit">Memproses...</span>
                        </button>

                    @endif
                </form>

            </div>
        </div>
    </div>
</div>