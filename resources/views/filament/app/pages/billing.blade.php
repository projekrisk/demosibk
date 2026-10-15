<x-filament-panels::page>
    <x-filament-actions::modals />

    @if(!$pendingTransaction)
        <x-filament::grid default="1" lg="3" class="gap-6 -mt-6">
            
            <x-filament::grid.column default="1">
                <x-filament::section>
                    <x-slot name="heading">Status Langganan</x-slot>
                    <x-slot name="headerEnd">
                        <x-heroicon-o-clock class="h-5 w-5 text-gray-400 dark:text-gray-500" />
                    </x-slot>

                    <div class="space-y-4">
                        <div class="flex flex-col gap-1">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Status Saat Ini</span>
                            <div>
                                @if($tenant->subscription_status === 'active')
                                    <x-filament::badge class="px-3 py-2" color="success" icon="heroicon-m-check-badge">PREMIUM</x-filament::badge>
                                @elseif($tenant->subscription_status === 'trial')
                                    <x-filament::badge class="px-3 py-2" color="warning" icon="heroicon-m-clock">TRIAL</x-filament::badge>
                                @else
                                    <x-filament::badge class="px-3 py-2" color="danger" icon="heroicon-m-x-circle">EXPIRED</x-filament::badge>
                                @endif
                            </div>
                        </div>
                        
                        <div class="border-t border-gray-100 dark:border-white/10"></div>

                        <div class="flex flex-col gap-1">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Masa Aktif</span>
                            <span class="text-xl font-bold text-gray-900 dark:text-white">
                                {{ $tenant->subscription_ends_at ? \Carbon\Carbon::parse($tenant->subscription_ends_at)->translatedFormat('d F Y') : 'Tidak Terbatas' }}
                            </span>
                            @if($tenant->subscription_ends_at)
                                @if(\Carbon\Carbon::parse($tenant->subscription_ends_at)->isFuture())
                                    <p class="text-xs text-primary-600 dark:text-primary-400 font-medium">
                                        {{ \Carbon\Carbon::parse($tenant->subscription_ends_at)->diffForHumans(null, true) }} lagi
                                    </p>
                                @else
                                    <p class="text-xs text-danger-600 dark:text-danger-400 font-medium">Telah berakhir</p>
                                @endif
                            @endif
                        </div>
                    </div>
                </x-filament::section>
            </x-filament::grid.column>

            <x-filament::grid.column default="1" lg="2">
                <x-filament::section>
                    <x-slot name="heading">Pilih Paket Perpanjangan</x-slot>

                    @if($plans->isEmpty())
                        <div class="p-4 bg-gray-50 rounded text-center text-sm text-gray-500 dark:bg-white/5 dark:text-gray-400">Belum ada paket tersedia.</div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                            @foreach($plans as $plan)
                                <div 
                                    class="relative flex flex-col p-6 rounded-xl shadow-sm ring-1 transition duration-150 cursor-pointer group h-full
                                    {{ $plan->is_featured 
                                        ? 'bg-primary-50/30 ring-primary-600 dark:bg-primary-900/10 dark:ring-primary-500' 
                                        : 'bg-white ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 hover:ring-primary-500/50 dark:hover:ring-primary-400/50 dark:bg-gray-900' 
                                    }}"
                                    wire:click="mountAction('subscribe', { plan_id: {{ $plan->id }} })"
                                >
                                    @if($plan->is_featured)
                                        <div class="absolute top-0 right-0 -mt-3 mr-4">
                                            <span class="inline-flex items-center rounded-full bg-primary-600 px-3 py-1 text-xs font-bold text-white shadow-sm ring-1 ring-white/20">
                                                REKOMENDASI
                                            </span>
                                        </div>
                                    @endif
                                    
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="text-base font-bold text-gray-900 dark:text-white group-hover:text-primary-600 transition-colors">
                                            {{ $plan->name }}
                                        </h3>
                                    </div>
                                    
                                    <div class="flex items-baseline mb-6 border-b border-gray-100 dark:border-white/10 pb-4">
                                        <span class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                                            Rp {{ number_format($plan->price, 0, ',', '.') }}
                                        </span>
                                        <span class="ml-1 text-sm font-medium text-gray-500 dark:text-gray-400" style="align-content: center;margin-left: 10px;">
                                            {{ $plan->duration_label }}
                                        </span>
                                    </div>

                                    @if($plan->features)
                                        <ul class="space-y-3 text-sm text-gray-600 dark:text-gray-400 flex-1 mb-6" style="margin:20px 0">
                                            @foreach($plan->features as $feature)
                                                <li class="flex gap-3 items-start">
                                                    <x-heroicon-m-check-circle class="h-5 w-5 text-primary-600 dark:text-primary-400 shrink-0"/>
                                                    <span class="text-sm">{{ $feature }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif

                                    <div class="mt-auto">
                                        <x-filament::button size="md" class="w-full" color="{{ $plan->is_featured ? 'primary' : 'gray' }}">
                                            Pilih Paket Ini
                                        </x-filament::button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </x-filament::section>
            </x-filament::grid.column>
        </x-filament::grid>

    @else
        <div class="w-full -mt-6">
            <div class="bg-white dark:bg-gray-900 shadow-lg ring-1 ring-gray-950/5 dark:ring-white/10 mx-auto print:shadow-none print:ring-0 overflow-hidden rounded-xl">
                
                <div class="p-6 md:p-10 border-b border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900">
                    <div class="flex flex-col md:flex-row justify-between items-start gap-6 md:gap-8">
                        <div class="w-full md:w-auto">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="p-2 bg-primary-600 rounded-lg shrink-0">
                                    <x-heroicon-o-academic-cap class="w-8 h-8 text-white"/>
                                </div>
                                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">SIBK-digi</h1>
                            </div>
                            <div class="text-gray-500 dark:text-gray-400 space-y-1">
                                <p class="font-medium text-gray-900 dark:text-white">Platform Digital Bimbingan Konseling</p>
                                <p>support@sibk-digi.com</p>
                                <p>Banten, Indonesia</p>
                            </div>
                        </div>

                        <div class="w-full md:w-auto text-left md:text-right">
                            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 dark:text-white uppercase tracking-tight mb-4 md:mb-6">INVOICE</h2>
                            <div class="space-y-3 text-sm w-full">
                                <div class="flex justify-between md:justify-end gap-4 md:gap-8 border-b border-dashed border-gray-200 dark:border-gray-700 pb-2 md:border-0 md:pb-0">
                                    <span class="text-gray-500 dark:text-gray-400 w-auto md:w-32 text-left">Nomor Referensi</span>
                                    <span class="font-mono font-medium text-gray-900 dark:text-white text-right">{{ $pendingTransaction->order_id }}</span>
                                </div>
                                <div class="flex justify-between md:justify-end gap-4 md:gap-8 border-b border-dashed border-gray-200 dark:border-gray-700 pb-2 md:border-0 md:pb-0">
                                    <span class="text-gray-500 dark:text-gray-400 w-auto md:w-32 text-left">Tanggal Terbit</span>
                                    <span class="font-medium text-gray-900 dark:text-white text-right">{{ $pendingTransaction->created_at->translatedFormat('d F Y') }}</span>
                                </div>
                                <div class="flex justify-between md:justify-end gap-4 md:gap-8 items-center pt-1 md:pt-0">
                                    <span class="text-gray-500 dark:text-gray-400 w-auto md:w-32 text-left">Status</span>
                                    <span class="text-right">
                                        @if($pendingTransaction->status == 'pending')
                                            <span class="inline-flex items-center rounded-md bg-yellow-50 px-2.5 py-1 text-xs font-bold text-yellow-800 ring-1 ring-inset ring-yellow-600/20">BELUM LUNAS</span>
                                        @elseif($pendingTransaction->status == 'paid')
                                            <span class="inline-flex items-center rounded-md bg-green-50 px-2.5 py-1 text-xs font-bold text-green-700 ring-1 ring-inset ring-green-600/20">LUNAS</span>
                                        @else
                                            <span class="inline-flex items-center rounded-md bg-red-50 px-2.5 py-1 text-xs font-bold text-red-700 ring-1 ring-inset ring-red-600/20">DIBATALKAN</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6 md:p-10 bg-gray-50/50 dark:bg-white/5 border-b border-gray-200 dark:border-white/10">
                    <div class="max-w-md">
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">DITAGIHKAN KEPADA</h3>
                        <p class="text-lg md:text-xl font-bold text-gray-900 dark:text-white mb-1">{{ $tenant->name }}</p>
                        <div class="text-sm text-gray-600 dark:text-gray-300 space-y-1">
                            <p class="font-medium">NPSN: {{ $tenant->npsn }}</p>
                            @if($tenant->address)
                                <p class="leading-relaxed">{{ $tenant->address }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="p-6 md:p-10">
                    <div class="overflow-x-auto -mx-6 md:mx-0 px-6 md:px-0">
                        <table class="w-full text-left text-sm whitespace-nowrap">
                            <thead class="border-b-2 border-gray-200 dark:border-white/10 text-gray-500 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-0 py-3 font-semibold uppercase tracking-wider w-3/4">Deskripsi Layanan</th>
                                    <th scope="col" class="px-0 py-3 font-semibold uppercase tracking-wider text-right w-1/4">Harga</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-white/5 text-gray-700 dark:text-gray-300">
                                <tr>
                                    <td class="px-0 py-5 align-top">
                                        <p class="font-bold text-gray-900 dark:text-white text-base">{{ $pendingTransaction->plan_type }}</p>
                                        <p class="text-ms text-gray-500 mt-1 whitespace-normal">Langganan Akses Fitur Premium SIBK-digi</p>
                                    </td>
                                    <td class="px-0 py-5 text-right font-bold text-gray-900 dark:text-white align-top">
                                        Rp {{ number_format($pendingTransaction->amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="pt-6">
                                        <div class="flex flex-col md:flex-row justify-end items-end md:items-center gap-2 md:gap-12 border-t border-gray-200 dark:border-white/10 pt-6">
                                            <span class="text-base font-bold text-gray-900 dark:text-white">TOTAL TAGIHAN</span>
                                            <span class="text-2xl md:text-3xl font-extrabold text-primary-600 dark:text-primary-400">
                                                Rp {{ number_format($pendingTransaction->amount, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                @if($pendingTransaction->status != 'paid' && $pendingTransaction->status != 'cancelled')
                    <div class="px-6 md:px-10 pb-10 grid grid-cols-1 lg:grid-cols-2 gap-8 md:gap-12 print:hidden">
                        
                        <div class="space-y-4">
                            <h4 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b border-gray-200 dark:border-white/10 pb-2">Metode Pembayaran</h4>
                            
                            @php
                                $targetBank = $pendingTransaction->bankAccount ?? 
                                    \App\Models\BankAccount::find($pendingTransaction->bank_account_id) ?? 
                                    \App\Models\BankAccount::where('is_active', true)->first();
                            @endphp

                            @if($targetBank)
                                <div class="p-6 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-white/5">
                                    <p class="text-xs font-medium text-gray-500 mb-3 uppercase tracking-wide">Transfer Bank Manual</p>
                                    <div class="flex justify-between items-start mb-2">
                                        <span class="font-bold text-lg text-gray-900 dark:text-white">{{ $targetBank->bank_name }}</span>
                                        <x-filament::icon-button 
                                            icon="heroicon-m-clipboard-document" 
                                            color="gray" 
                                            size="sm" 
                                            tooltip="Salin No. Rek"
                                            onclick="navigator.clipboard.writeText('{{ $targetBank->account_number }}'); new FilamentNotification().title('Disalin').success().send()" 
                                        />
                                    </div>
                                    <p class="text-2xl font-mono font-bold text-gray-900 dark:text-white tracking-wide mb-1 break-all">{{ $targetBank->account_number }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">a.n {{ $targetBank->account_holder }}</p>
                                </div>
                            @else
                                <p class="text-sm text-danger-500">Informasi bank tidak tersedia.</p>
                            @endif
                        </div>

                        <div class="space-y-4" style="margin-bottom:40px">
                            <h4 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b border-gray-200 dark:border-white/10 pb-2">Konfirmasi Pembayaran</h4>
                            
                            @if(!$pendingTransaction->proof_of_payment)
                                <form wire:submit.prevent="uploadProof" class="space-y-4">
                                    <div class="flex flex-col gap-4">
                                        <div>
                                            <label class="block w-full cursor-pointer">
                                                <div class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                        @if($proofAttachment)
                                                            <x-heroicon-o-document-check class="w-8 h-8 text-success-500 mb-2"/>
                                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $proofAttachment->getClientOriginalName() }}</p>
                                                        @else
                                                            <x-heroicon-o-cloud-arrow-up class="w-8 h-8 text-gray-500 dark:text-gray-400 mb-2"/>
                                                            <p class="text-xs text-gray-500 dark:text-gray-400"><span class="font-semibold">Klik untuk upload</span> bukti transfer</p>
                                                            <p class="text-[10px] text-gray-500 dark:text-gray-400">PNG, JPG (MAX. 2MB)</p>
                                                        @endif
                                                    </div>
                                                    <input type="file" wire:model="proofAttachment" class="hidden" />
                                                </div>
                                            </label>
                                            @error('proofAttachment') <p class="text-xs text-danger-600 mt-1">{{ $message }}</p> @enderror
                                        </div>

                                        <div class="flex items-center gap-3">
                                            <x-filament::button type="submit" class="px-3 py-2" size="sm" icon="heroicon-m-paper-airplane" class="w-full">
                                                Kirim Bukti Pembayaran
                                            </x-filament::button>
                                        </div>
                                        <div wire:loading wire:target="uploadProof" class="text-xs text-center text-gray-400">Sedang mengupload...</div>
                                    </div>
                                </form>
                            @else
                                <div class="flex flex-col gap-4">
                                    <div class="flex items-start gap-3 p-4 bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300">
                                        <div class="p-1 bg-green-100 dark:bg-green-800 rounded-full shrink-0">
                                            <x-heroicon-m-check class="w-4 h-4"/>
                                        </div>
                                        <div>
                                            <p class="font-bold">Bukti Pembayaran Diterima</p>
                                            <p class="text-sm mt-1 opacity-90 leading-relaxed">Tim kami sedang memverifikasi pembayaran Anda.</p>
                                        </div>
                                    </div>
                                    <div class="w-full">
                                        {{ $this->confirmWaAction }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="bg-gray-50 dark:bg-white/5 px-6 md:px-10 py-6 border-t border-gray-200 dark:border-white/10 flex flex-col-reverse md:flex-row justify-between items-center gap-4 print:hidden">
                    <div class="text-xs text-gray-500 text-center md:text-left">
                    </div>
                    
                    <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                        @if($pendingTransaction->status == 'pending')
                             <div class="w-full sm:w-auto">
                                {{ $this->cancelOrderAction }}
                            </div>
                        @endif
                        
                        <div class="w-full sm:w-auto">
                            <x-filament::button 
                                color="gray" 
                                variant="outlined" 
                                icon="heroicon-m-printer"
                                onclick="window.print()"
                                class="w-full"
                            >
                                Cetak Invoice
                            </x-filament::button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('open-wa-link', (event) => {
                window.open(event.url, '_blank');
            });
        });
    </script>
</x-filament-panels::page>