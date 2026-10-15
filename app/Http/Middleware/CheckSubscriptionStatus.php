<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Filament\Notifications\Notification;
use Carbon\Carbon;

class CheckSubscriptionStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = Filament::getTenant();

        if (! $tenant) {
            return $next($request);
        }

        $isStatusInvalid = in_array($tenant->subscription_status, ['expired', 'inactive']);
        
        $isDateExpired = $tenant->subscription_ends_at && Carbon::parse($tenant->subscription_ends_at)->isPast();

        if ($isStatusInvalid || $isDateExpired) {
            
            if ($request->routeIs('filament.app.pages.billing')) {
                return $next($request);
            }

            Notification::make()
                ->title('Masa Aktif Habis')
                ->body('Masa berlangganan sekolah Anda telah berakhir. Silakan lakukan perpanjangan untuk mengakses menu.')
                ->warning()
                ->send();

            return redirect()->route('filament.app.pages.billing', ['tenant' => $tenant->slug]);
        }

        return $next($request);
    }
}