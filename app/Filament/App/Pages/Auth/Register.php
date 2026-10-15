<?php

namespace App\Filament\App\Pages\Auth;

use App\Models\Tenant;
use App\Models\User;
use App\Models\SubscriptionPlan;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;
use Filament\Notifications\Notification;
use Filament\Auth\Http\Responses\Contracts\RegistrationResponse; 
use Illuminate\Http\RedirectResponse; 
use Symfony\Component\HttpFoundation\Response; 
use Filament\Actions\Action; 

class Register extends BaseRegister
{
    public function getMaxWidth(): Width | string | null
    {
        return Width::TwoExtraLarge;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Akun Admin')
                        ->description('Buat akun pengelola')
                        ->icon('heroicon-o-user')
                        ->schema([
                            $this->getNameFormComponent(),
                            $this->getEmailFormComponent(),
                            
                            TextInput::make('phone')
                                ->label('Nomor WhatsApp')
                                ->tel()
                                ->placeholder('Contoh: 628123456789')
                                ->required()
                                ->maxLength(20),

                            $this->getPasswordFormComponent(),
                            $this->getPasswordConfirmationFormComponent(),
                        ]),

                    Wizard\Step::make('Data Sekolah')
                        ->description('Identitas instansi')
                        ->icon('heroicon-o-academic-cap')
                        ->schema([
                            TextInput::make('school_name')
                                ->label('Nama Sekolah')
                                ->placeholder('Contoh: SMA Negeri 1 ...')
                                ->required()
                                ->maxLength(255)
                                ->autofocus(),
                            
                            TextInput::make('npsn')
                                ->label('NPSN')
                                ->placeholder('Nomor Pokok Sekolah Nasional')
                                ->required()
                                ->numeric()
                                ->length(8)
                                ->unique('tenants', 'npsn')
                                ->validationMessages([
                                    'unique' => 'NPSN ini sudah terdaftar di sistem kami.',
                                    'digits' => 'NPSN harus terdiri dari 8 angka.',
                                ]),
                        ]),
                ]),
            ]);
    }
    
    public function register(): ?RegistrationResponse
    {
        try {
            $data = $this->form->getState();
            $this->handleRegistration($data);
            $this->mountAction('success');
        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal Registrasi')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }

        return null;
    }

    public function successAction(): Action
    {
        return Action::make('success')
            ->label('Sukses')
            ->modalHeading('Registrasi Berhasil')
            ->modalDescription('Akun sekolah Anda telah dibuat. Link aktivasi telah dikirim ke email Anda. Harap verifikasi sebelum login.')
            ->modalSubmitActionLabel('Ke Halaman Login')
            ->color('success')
            ->icon('heroicon-o-check-circle')
            ->action(function () {
                return redirect()->route('filament.app.auth.login');
            })
            ->closeModalByClickingAway(false)
            ->modalCloseButton(false);
    }

    protected function handleRegistration(array $data): Model
    {
        $user = DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => $data['password'], 
                'role' => 'admin_sekolah',
            ]);

            $slug = Str::slug($data['school_name']);
            $originalSlug = $slug;
            $count = 2;
            
            while (Tenant::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count;
                $count++;
            }

            $trialDays = 14; 
            try {
                $trialPlan = SubscriptionPlan::where('price', 0)->where('is_active', true)->first();
                if ($trialPlan) {
                    $trialDays = $trialPlan->duration_in_days;
                }
            } catch (\Exception $e) {}

            $tenant = Tenant::create([
                'name' => $data['school_name'],
                'npsn' => $data['npsn'],
                'slug' => $slug, 
                'subscription_status' => 'trial',
                'subscription_ends_at' => now()->addDays($trialDays),
            ]);

            $user->tenants()->attach($tenant);

            return $user;
        });

        event(new Registered($user));
        
        return $user;
    }
}