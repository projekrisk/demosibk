<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Notifications\VerifyEmail; 
use Illuminate\Notifications\Messages\MailMessage; 
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

// Import Models & Policies
use App\Models\DailyJournal;
use App\Policies\DailyJournalPolicy;
use App\Models\CounselingService;
use App\Policies\CounselingServicePolicy;
use App\Models\Student;
use App\Policies\StudentPolicy;
use App\Models\User;
use App\Policies\UserPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // FIX HOSTING: Mengatur Public Path secara Dinamis
        // Memastikan Laravel mengenali folder public_html sebagai root public
        $this->app->bind('path.public', function () {
            $nestedPublic = base_path('../public_html/public');
            if (is_dir($nestedPublic)) return $nestedPublic;

            $flatPublic = base_path('../public_html');
            if (is_dir($flatPublic)) return $flatPublic;

            return base_path('public');
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // FIX HTTPS: Mencegah error 403 (Invalid Signature) pada link verifikasi email
        // Memaksa Laravel menggunakan HTTPS untuk semua signed URL jika APP_URL menggunakan https
        if (str_contains(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        // FIX URL GAMBAR
        $appUrl = rtrim(config('app.url'), '/'); 

        if (is_dir(base_path('../public_html/public'))) {
            config(['filesystems.disks.public_uploads.url' => $appUrl.'/public/uploads']);
        } 
        elseif (is_dir(base_path('../public_html'))) {
            config(['filesystems.disks.public_uploads.url' => $appUrl.'/uploads']);
        }

        // 1. Registrasi Policy
        Gate::policy(DailyJournal::class, DailyJournalPolicy::class);
        Gate::policy(CounselingService::class, CounselingServicePolicy::class);
        Gate::policy(Student::class, StudentPolicy::class);
        Gate::policy(User::class, UserPolicy::class);

        // 2. Kustomisasi Email Verifikasi
        // Mengganti route default dengan route verifikasi kustom tanpa auth
        VerifyEmail::createUrlUsing(function (object $notifiable) {
            return URL::temporarySignedRoute(
                'custom.verification.verify',
                Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );
        });

        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Verifikasi Email - SIBK-digi') 
                ->view('emails.verify-email', ['url' => $url, 'user' => $notifiable]); 
        });
    }
}