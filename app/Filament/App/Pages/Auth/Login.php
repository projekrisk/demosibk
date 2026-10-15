<?php

namespace App\Filament\App\Pages\Auth;

use Filament\Schemas\Schema;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Facades\Filament;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ])
            ->statePath('data');
    }

    /**
     * LOGIKA LOGIN & REDIRECT (METODE SESSION INTENDED)
     * Menggunakan mekanisme native Filament untuk redirect agar kompatibel dengan Livewire.
     */
    public function authenticate(): ?LoginResponse
    {
        $data = $this->form->getState();

        // 1. Coba Login
        if (! Filament::auth()->attempt($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
            $this->throwFailureValidationException();
        }

        // 2. Ambil User
        $user = Filament::auth()->user();

        // (Opsional) Cek akses panel jika diperlukan
        // if (($user instanceof FilamentUser) && (! $user->canAccessPanel(Filament::getCurrentPanel()))) { ... }

        // 3. Regenerasi Sesi (Wajib untuk keamanan)
        session()->regenerate();

        // 4. Tentukan Tujuan Redirect (Multi-tenancy)
        $tenant = $user->tenants->first();

        // Fallback Super Admin
        if (! $tenant && $user->role === 'super_admin') {
            $tenant = \App\Models\Tenant::first();
        }

        // 5. Eksekusi Redirect Aman
        if ($tenant) {
            $targetUrl = url('/app/' . $tenant->slug);
            
            // TRIK UTAMA: Simpan URL tujuan ke session 'url.intended'
            // Filament LoginResponse secara otomatis akan mengecek session ini
            // dan melakukan redirect yang benar (kompatibel dengan Livewire).
            session()->put('url.intended', $targetUrl);

            // Kembalikan Response Standar Filament
            return app(LoginResponse::class);
        }

        // 6. Jika Gagal (Tidak punya sekolah)
        Filament::auth()->logout();
        
        throw ValidationException::withMessages([
            'data.email' => 'Akun Anda tidak terhubung dengan sekolah manapun.',
        ]);
    }
}