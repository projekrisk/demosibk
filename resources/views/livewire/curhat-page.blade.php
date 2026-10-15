<div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md mx-auto bg-white rounded-xl shadow-md overflow-hidden md:max-w-2xl">
        
        <div class="bg-gradient-to-r from-pink-500 to-rose-500 px-6 py-4">
            <div class="flex items-center">
                <div class="h-12 w-12 rounded-full bg-white/20 text-white flex items-center justify-center font-bold text-xl mr-4 border border-white/30">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">{{ $tenant->name }}</h2>
                    <p class="text-pink-100 text-sm">Kotak Masalah & Curhat Siswa</p>
                </div>
            </div>
        </div>

        <div class="p-8">
            @error('rate_limit')
                <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded">
                    <p class="text-sm text-red-700 font-bold">{{ $message }}</p>
                </div>
            @enderror

            @if($successMessage)
                <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded animate-fade-in-up">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700 font-medium">{{ $successMessage }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <form wire:submit.prevent="submit" class="space-y-6">
                
                <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg border border-gray-200">
                    <label for="is_anonymous" class="text-sm font-medium text-gray-700 cursor-pointer select-none flex-1">
                        Kirim sebagai Anonim?
                        <p class="text-xs text-gray-500 font-normal mt-0.5">Identitas Anda akan dirahasiakan dari Guru.</p>
                    </label>
                    <div class="flex items-center h-5">
                        <input id="is_anonymous" type="checkbox" wire:model.live="is_anonymous" class="focus:ring-pink-500 h-5 w-5 text-pink-600 border-gray-300 rounded cursor-pointer">
                    </div>
                </div>

                @if(!$is_anonymous)
                    <div class="space-y-4 p-4 bg-pink-50 rounded-xl border border-pink-100">
                        <div class="relative transition-all duration-300">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cari Nama Anda</label>
                            
                            @if($selectedStudentName)
                                <div class="flex items-center justify-between p-3 border border-pink-200 bg-white rounded-lg">
                                    <span class="text-sm font-semibold text-pink-800">{{ $selectedStudentName }}</span>
                                    <button type="button" wire:click="$set('selectedStudentName', null); $set('selectedStudentId', null)" class="text-xs text-gray-500 hover:text-red-600 underline">Ganti</button>
                                </div>
                            @else
                                <input type="text" 
                                       wire:model.live.debounce.300ms="studentSearch" 
                                       class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm p-3 border" 
                                       placeholder="Ketik nama...">
                                
                                @if(!empty($studentsFound))
                                    <ul class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-xl ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                        @foreach($studentsFound as $student)
                                            <li class="cursor-pointer select-none py-2 pl-3 pr-9 hover:bg-pink-50 transition" 
                                                wire:click="selectStudent({{ $student->id }}, '{{ $student->name }}')">
                                                <div class="flex items-center">
                                                    <span class="font-medium text-gray-900 truncate block">{{ $student->name }}</span>
                                                    <span class="ml-2 text-gray-500 text-xs">({{ $student->class_name }})</span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            @endif
                            @error('selectedStudentId') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Verifikasi NISN</label>
                            <input type="text" wire:model="verify_nisn" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm p-3 border" placeholder="Masukkan NISN Anda untuk verifikasi">
                            <p class="text-xs text-gray-500 mt-1">Kami perlu memastikan ini benar-benar Anda.</p>
                            @error('verify_nisn') <span class="text-red-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                        </div>
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul / Topik</label>
                    <input type="text" wire:model="subject" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm p-3 border" placeholder="Contoh: Masalah dengan teman sekelas...">
                    @error('subject') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ceritakan Masalah Anda</label>
                    <textarea wire:model="message" rows="5" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm p-3 border" placeholder="Tuliskan curhatan atau masalah yang Anda hadapi..."></textarea>
                    @error('message') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-pink-600 hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 transition-all transform hover:-translate-y-0.5">
                        Kirim Pesan
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>