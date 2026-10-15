<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Support\Facades\Blade;
use Livewire\Livewire; 

use App\Filament\Widgets\GlobalPlatformStats;
use App\Filament\Widgets\RevenueStats;
use App\Filament\Widgets\SchoolSummaryTable;
use App\Filament\Widgets\RegisteredSchoolsChart;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->favicon(asset('favicon.png'))
            ->login()
            ->colors([
                'primary' => Color::Red,
            ])
            
            ->renderHook(
                'panels::sidebar.footer',
                fn (): string => Blade::render(<<<HTML
                    <div class="flex items-center gap-3 px-4 py-4 border-t border-gray-200 dark:border-white/10 mt-auto">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary-600 text-sm font-bold text-white shadow-sm ring-1 ring-white/20">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div class="flex flex-1 flex-col overflow-hidden">
                            <p class="truncate text-sm font-semibold text-gray-950 dark:text-white">
                                {{ auth()->user()->name }}
                            </p>
                            <p class="truncate text-xs text-gray-500 dark:text-gray-400">
                                Super Admin
                            </p>
                        </div>
                    </div>
                HTML)
            )

            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->widgets([
                GlobalPlatformStats::class,
                
                SchoolSummaryTable::class,

                RegisteredSchoolsChart::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    public function boot(): void
    {
        Livewire::component('app.filament.widgets.revenue-stats', RevenueStats::class);
    }
}