<?php

namespace App\Providers\Filament;

use App\Filament\App\Pages\Auth\Login;
use App\Filament\App\Pages\Auth\Register;
use App\Filament\App\Pages\Auth\EditProfile;
use App\Models\Tenant;
use App\Http\Middleware\CheckSubscriptionStatus;
use Filament\Forms\Components\FileUpload;
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

use App\Filament\App\Widgets\StatsOverview;
use App\Filament\App\Widgets\ServiceTypeChart;
use App\Filament\App\Widgets\LatestScheduled;
use App\Filament\App\Widgets\AnnouncementBanner;
use App\Filament\App\Widgets\StudentsAtRisk;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('app')
            ->path('app')
            ->login(Login::class)
            ->registration(Register::class)
            ->profile(EditProfile::class)
            ->favicon(asset('favicon.png'))            
            ->emailVerification()            
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->tenant(Tenant::class, slugAttribute: 'slug')
            ->tenantMiddleware([
                CheckSubscriptionStatus::class,
            ], isPersistent: true)
            ->collapsibleNavigationGroups(true) 
            ->navigationGroups([
                'Data Master',        
                'Pengaturan',         
                'Pengaturan Sekolah', 
                'Layanan BK',         
                'Asesmen Kebutuhan',  
                'Laporan',            
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
                                {{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}
                            </p>
                        </div>
                        <a href="/app/{{ \Filament\Facades\Filament::getTenant()->slug }}/profile" 
                           class="text-gray-400 hover:text-primary-600 dark:text-gray-500 dark:hover:text-primary-400 transition"
                           title="Pengaturan Akun">
                            <x-heroicon-m-cog-6-tooth class="h-5 w-5" />
                        </a>
                    </div>
                HTML)
            )

            ->discoverResources(in: app_path('Filament/App/Resources'), for: 'App\\Filament\\App\\Resources')
            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\\Filament\\App\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->widgets([
                AnnouncementBanner::class,
                StatsOverview::class,
                ServiceTypeChart::class,
                LatestScheduled::class,
                StudentsAtRisk::class,
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
        FileUpload::configureUsing(function (FileUpload $component) {
            $component->disk('public_uploads');
            $component->visibility('public');
        });
    }
}