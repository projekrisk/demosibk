<?php

namespace App\Filament\App\Pages\Auth;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;

class EditProfile extends BaseEditProfile
{
    public function getHeading(): string
    {
        return 'Halo, ' . auth()->user()->name;
    }

    public function getSubheading(): ?string
    {
        return 'Kelola informasi profil dan keamanan akun Anda di sini.';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informasi Akun')
                    ->description('Perbarui data diri dan kata sandi Anda.')
                    ->icon('heroicon-o-user-circle')
                    ->schema([
                        $this->getNameFormComponent()
                            ->label('Nama Lengkap')
                            ->required(),
                        
                        $this->getEmailFormComponent()
                            ->label('Alamat Email')
                            ->required(),

                        TextInput::make('phone')
                            ->label('Nomor WhatsApp')
                            ->tel()
                            ->placeholder('Contoh: 08123456789')
                            ->maxLength(20)
                            ->helperText('Nomor ini digunakan untuk keperluan komunikasi penting (Admin Sekolah).'),

                        $this->getPasswordFormComponent()
                            ->label('Password Baru')
                            ->helperText('Kosongkan jika tidak ingin mengubah password.'),
                        
                        $this->getPasswordConfirmationFormComponent()
                            ->label('Konfirmasi Password'),
                    ])
                    ->columns(1),
            ]);
    }
}