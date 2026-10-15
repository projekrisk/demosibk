<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\StudentMessageResource\Pages;
use App\Models\StudentMessage;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Filament\Facades\Filament;

class StudentMessageResource extends Resource
{
    protected static ?string $model = StudentMessage::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-inbox-arrow-down';

    protected static ?string $modelLabel = 'Pesan Siswa';
    protected static ?string $pluralModelLabel = 'Kotak Masalah';
    
    protected static string | \UnitEnum | null $navigationGroup = 'Layanan BK';
    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'pesan';

    public static function getNavigationBadge(): ?string
    {
        return static::getEloquentQuery()->where('replied_at', null)->count() ?: null;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->where('tenant_id', Filament::getTenant()->id);
        
        $user = Auth::user();
        
        if ($user->role === 'konselor') {
            $query->where(function (Builder $q) use ($user) {
                $q->where('counselor_id', $user->id)
                
                  ->orWhere(function (Builder $subQ) use ($user) {
                      $subQ->whereNull('counselor_id')
                           ->whereHas('student', function (Builder $studentQ) use ($user) {
                               $studentQ->whereHas('classroom', function (Builder $classQ) use ($user) {
                                   $classQ->whereHas('counselors', function (Builder $counselorQ) use ($user) {
                                       $counselorQ->where('users.id', $user->id);
                                   });
                               });
                           });
                  });
            });
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
                Forms\Components\Section::make('Isi Pesan')
                    ->schema([
                        Forms\Components\TextInput::make('sender_display')
                            ->label('Pengirim')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(function (?StudentMessage $record) {
                                if (!$record) return '-';
                                return $record->is_anonymous 
                                    ? 'Anonim (Rahasia)' 
                                    : ($record->student?->name ?? 'Siswa Tidak Dikenal');
                            }),

                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('Diterima Pada')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('target_counselor')
                            ->label('Ditujukan Kepada')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn (?StudentMessage $record) => $record?->targetCounselor?->name ?? 'Umum (Siapa Saja)'),

                        Forms\Components\TextInput::make('subject')
                            ->label('Subjek')
                            ->disabled()
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('message')
                            ->label('Isi Curhatan / Masalah')
                            ->rows(6)
                            ->disabled()
                            ->columnSpanFull(),
                    ])->columns(2),
                
                Forms\Components\Section::make('Balasan Guru')
                    ->schema([
                        Forms\Components\Textarea::make('response')
                            ->label('Respon / Tindak Lanjut')
                            ->placeholder('Belum ada respon...')
                            ->disabled(),
                        
                        Forms\Components\Placeholder::make('replied_info')
                            ->label('Dibalas Oleh')
                            ->content(fn (?StudentMessage $record) => $record?->replied_at ? $record->replier->name . ' pada ' . $record->replied_at->format('d M Y H:i') : '-'),
                    ])
                    ->visible(fn (?StudentMessage $record) => $record && $record->response !== null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->since()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sender_info')
                    ->label('Pengirim')
                    ->state(fn (StudentMessage $record) => $record->is_anonymous ? 'Anonim' : $record->student?->name)
                    ->description(fn (StudentMessage $record) => $record->is_anonymous ? 'Identitas Dirahasiakan' : $record->student?->class_name)
                    ->icon(fn (StudentMessage $record) => $record->is_anonymous ? 'heroicon-o-eye-slash' : 'heroicon-o-user')
                    ->color(fn (StudentMessage $record) => $record->is_anonymous ? 'danger' : 'success')
                    ->searchable(),

                Tables\Columns\TextColumn::make('subject')
                    ->label('Subjek')
                    ->limit(20)
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('targetCounselor.name')
                    ->label('Kepada')
                    ->placeholder('Umum')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('replied_at')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->tooltip(fn ($state) => $state ? 'Sudah Dibalas' : 'Belum Dibalas'),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
                
                Tables\Actions\Action::make('reply')
                    ->label('Balas')
                    ->icon('heroicon-m-pencil-square')
                    ->color('primary')
                    ->modalHeading('Balas Pesan Siswa')
                    ->modalDescription('Balasan ini akan dicatat dalam sistem sebagai tindak lanjut.')
                    ->form([
                        Forms\Components\TextInput::make('subject_context')
                            ->label('Subjek')
                            ->default(fn (StudentMessage $record) => $record->subject)
                            ->disabled()
                            ->dehydrated(false),
                            
                        Forms\Components\Textarea::make('message_context')
                            ->label('Isi Pesan Siswa')
                            ->default(fn (StudentMessage $record) => $record->message)
                            ->disabled()
                            ->dehydrated(false)
                            ->rows(4),

                        Forms\Components\Textarea::make('response')
                            ->label('Isi Balasan / Catatan Tindak Lanjut')
                            ->required()
                            ->rows(4),
                    ])
                    ->action(function (StudentMessage $record, array $data): void {
                        $record->update([
                            'response' => $data['response'],
                            'replied_at' => now(),
                            'replied_by' => Auth::id(),
                        ]);
                    })
                    ->visible(fn (StudentMessage $record) => $record->replied_at === null),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudentMessages::route('/'),
            'create' => Pages\CreateStudentMessage::route('/create'),
            'view' => Pages\ViewStudentMessage::route('/{record}'),
        ];
    }
}