<?php

namespace App\Filament\App\Pages;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Pages\Page; 
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Facades\Filament;

class EditSchoolProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-office';
    protected static ?int $navigationSort = 5;
    protected static ?string $title = 'Profil Sekolah';
    protected static ?string $slug = 'profile';

    protected string $view = 'filament.app.pages.edit-school-profile';

    public ?array $data = [];

    public function mount(): void
    {
        $tenant = Filament::getTenant();
        $this->form->fill($tenant->attributesToArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Pengaturan Akademik')
                    ->description('Atur Tahun Ajaran dan Semester yang sedang aktif saat ini.')
                    ->icon('heroicon-o-calendar-days')
                    ->schema([
                        TextInput::make('current_academic_year')
                            ->label('Tahun Ajaran Aktif')
                            ->placeholder('Contoh: 2024/2025')
                            ->required()
                            ->maxLength(20),
                        
                        Select::make('current_semester')
                            ->label('Semester Aktif')
                            ->options([
                                'Ganjil' => 'Ganjil',
                                'Genap' => 'Genap',
                            ])
                            ->required()
                            ->native(false),
                    ])->columns(2),

                Section::make('Identitas Sekolah')
                    ->description('Data ini akan digunakan untuk Kop Surat Laporan.')
                    ->schema([
                        FileUpload::make('logo_path')
                            ->label('Logo Sekolah')
                            ->image()
                            ->directory('school-logos')
                            ->disk('public_uploads')
                            ->visibility('public')
                            ->imageEditor()
                            ->columnSpanFull()
                            ->helperText('Format: JPG/PNG. Digunakan untuk Kop Surat.'),
                        
                        TextInput::make('npsn')
                            ->label('NPSN')
                            ->disabled()
                            ->required()
                            ->maxLength(8),

                        TextInput::make('name')
                            ->label('Nama Sekolah')
                            ->required()
                            ->maxLength(255),
                        
                        Textarea::make('address')
                            ->label('Alamat Lengkap')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Pimpinan Sekolah')
                    ->description('Penandatangan Laporan Bulanan.')
                    ->schema([
                        TextInput::make('headmaster_name')
                            ->label('Nama Kepala Sekolah')
                            ->placeholder('Contoh: Dr. Budi Santoso, M.Pd')
                            ->maxLength(255),
                        
                        TextInput::make('headmaster_nip')
                            ->label('NIP Kepala Sekolah')
                            ->numeric()
                            ->maxLength(20),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();
            Filament::getTenant()->update($data);
            
            Notification::make() 
                ->success()
                ->title('Pengaturan sekolah berhasil disimpan')
                ->send();
        } catch (\Exception $exception) {
            Notification::make() 
                ->danger()
                ->title('Gagal menyimpan')
                ->body($exception->getMessage())
                ->send();
        }
    }

    public static function canAccess(): bool
    {
        return auth()->user()->role === 'admin_sekolah' || auth()->user()->role === 'super_admin';
    }
}