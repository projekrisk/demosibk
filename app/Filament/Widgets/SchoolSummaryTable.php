<?php

namespace App\Filament\Widgets;

use App\Models\Tenant;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class SchoolSummaryTable extends BaseWidget
{
    protected static ?int $sort = 2;    
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Ringkasan Data';
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Tenant::query()
                    ->with('members')
                    ->withCount('students')
                    ->withCount(['members as counselors_count' => function (Builder $query) {
                        $query->where('role', 'konselor');
                    }])
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Sekolah')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn (Tenant $record) => $record->npsn),

                Tables\Columns\TextColumn::make('subscription_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'trial' => 'warning',
                        'expired' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('subscription_ends_at')
                    ->label('Masa Aktif')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return '∞';
                        $date = Carbon::parse($state);
                        
                        $days = (int) now()->diffInDays($date, false);
                        
                        if ($days < 0) return 'Expired ' . abs($days) . ' hari lalu';
                        if ($days == 0) return 'Hari ini';
                        return $days . ' Hari lagi';
                    })
                    ->color(fn ($state) => $state && Carbon::parse($state)->isPast() ? 'danger' : 'gray')
                    ->sortable(),

                Tables\Columns\TextColumn::make('students_count')
                    ->label('Siswa')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('counselors_count')
                    ->label('Konselor')
                    ->alignCenter(),
            ])
            ->actions([
                Tables\Actions\Action::make('whatsapp')
                    ->label('Hubungi')
                    ->icon('heroicon-m-chat-bubble-left-right')
                    ->color('success')
                    ->button()
                    ->size('xs')
                    ->url(function (Tenant $record) {
                        $admin = $record->members->where('role', 'admin_sekolah')->first();
                        
                        if (!$admin) {
                            $admin = $record->members->whereNotNull('phone')->first();
                        }
                        
                        if (!$admin || !$admin->phone) return null;
                        
                        $phone = $admin->phone;
                        if (str_starts_with($phone, '0')) {
                            $phone = '62' . substr($phone, 1);
                        }
                        
                        $status = ucfirst($record->subscription_status);
                        $date = $record->subscription_ends_at ? Carbon::parse($record->subscription_ends_at)->format('d M Y') : '-';
                        
                        $text = "Halo Admin {$record->name},%0A";
                        $text .= "Info Status Langganan SIBK-digi:%0A";
                        $text .= "Status: *{$status}*%0A";
                        if ($record->subscription_ends_at) {
                            $text .= "Exp: *{$date}*%0A";
                        }
                        
                        return "https://wa.me/{$phone}?text={$text}";
                    }, shouldOpenInNewTab: true)
                    ->disabled(fn (Tenant $record) => !$record->members->whereNotNull('phone')->count())
                    ->tooltip(fn (Tenant $record) => !$record->members->whereNotNull('phone')->count() ? 'Nomor WA tidak tersedia di data user' : 'Chat via WhatsApp'),
            ])
            ->paginated([5, 10, 25]);
    }
}