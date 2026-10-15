<x-filament-widgets::widget>
    @if($this->getAnnouncements()->count() > 0)
        <div class="flex flex-col gap-4">
            @foreach($this->getAnnouncements() as $announcement)
                @php
                    // Mapping warna aksen (border kiri & ikon)
                    $colorHex = match ($announcement->color) {
                        'success' => '#10b981', // emerald-500
                        'warning' => '#f59e0b', // amber-500
                        'danger'  => '#ef4444', // red-500
                        default   => '#3b82f6', // blue-500
                    };
                @endphp

                <section class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden">
                    <div class="flex items-stretch">
                        <div class="w-1.5 flex-shrink-0" style="background-color: {{ $colorHex }};"></div>
                        
                        <div class="flex-1 p-4">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 mt-0.5">
                                    <x-heroicon-m-megaphone class="h-5 w-5" style="color: {{ $colorHex }};" />
                                </div>
                                
                                <div class="flex-1">
                                    <h3 class="text-sm font-semibold text-gray-950 dark:text-white" style="color: {{ $colorHex }};">
                                        {{ $announcement->title }}
                                    </h3>
                                    <div class="mt-1 text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
                                        {!! nl2br(e($announcement->content)) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            @endforeach
        </div>
    @endif
</x-filament-widgets::widget>