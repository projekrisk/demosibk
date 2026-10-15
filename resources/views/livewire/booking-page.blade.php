<div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md mx-auto bg-white rounded-xl shadow-md overflow-hidden md:max-w-2xl">
        
        <div class="bg-primary px-6 py-4">
            <div class="flex items-center">
                <div class="h-12 w-12 rounded-full bg-white flex items-center justify-center text-primary font-bold text-xl mr-4">
                    {{ substr($tenant->name, 0, 1) }}
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">{{ $tenant->name }}</h2>
                    <p class="text-indigo-200 text-sm">Formulir Booking Konseling</p>
                </div>
            </div>
        </div>

        <div class="p-8">
            @if($successMessage)
                <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded">
                    <div class="flex">
                        <div class="ml-3">
                            <p class="text-sm text-green-700 font-medium">
                                {{ $successMessage }}
                            </p>
                            <p class="mt-2 text-sm font-bold text-green-800">
                                Kode Booking: <span class="text-lg bg-green-200 px-2 rounded">{{ $bookingCode }}</span>
                            </p>
                            <p class="text-xs text-green-600 mt-1">Simpan kode ini sebagai bukti.</p>
                        </div>
                    </div>
                </div>
            @endif

            <form wire:submit.prevent="submit" class="space-y-6">
                
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama / NISN Anda</label>
                    
                    @if($selectedStudentName)
                        <div class="flex items-center justify-between p-2 border border-green-300 bg-green-50 rounded-md">
                            <span class="text-sm font-semibold text-green-800">{{ $selectedStudentName }}</span>
                            <button type="button" wire:click="$set('selectedStudentName', null); $set('selectedStudentId', null)" class="text-xs text-red-500 hover:text-red-700 font-medium underline">Ganti</button>
                        </div>
                    @else
                        <input type="text" 
                               wire:model.live.debounce.300ms="studentSearch" 
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-3 border" 
                               placeholder="Ketik Nama atau NISN siswa...">
                        
                        @if(!empty($studentsFound))
                            <ul class="absolute z-50 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm border border-gray-200">
                                @foreach($studentsFound as $student)
                                    <li class="cursor-pointer select-none py-2 pl-3 pr-9 hover:bg-indigo-50 transition" 
                                        wire:click="selectStudent({{ $student->id }}, '{{ $student->name }}', '{{ $student->nisn }}')">
                                        <div class="flex items-center">
                                            <span class="font-medium text-gray-900 truncate block">{{ $student->name }}</span>
                                            <span class="ml-2 text-gray-500 text-xs">({{ $student->nisn }})</span>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    @endif
                    @error('selectedStudentId') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Guru BK</label>
                    <select wire:model="counselorId" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-3 border">
                        <option value="">-- Pilih Guru --</option>
                        @foreach($counselors as $guru)
                            <option value="{{ $guru->id }}">{{ $guru->name }}</option>
                        @endforeach
                    </select>
                    @error('counselorId') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                        <input type="date" wire:model="date" min="{{ date('Y-m-d') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-3 border">
                        @error('date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jam</label>
                        <input type="time" wire:model="time" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-3 border">
                        @error('time') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Topik / Masalah Singkat</label>
                    <textarea wire:model="message" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-3 border" placeholder="Contoh: Saya ingin konsultasi masalah belajar..."></textarea>
                    @error('message') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-bold text-white bg-primary hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                        Ajukan Jadwal Konseling
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>