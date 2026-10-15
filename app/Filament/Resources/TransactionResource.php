<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use App\Models\Tenant;
use App\Models\SubscriptionPlan;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-banknotes';
    protected static string | \UnitEnum | null $navigationGroup = 'Sistem';
    protected static ?int $navigationSort = 4;
    protected static ?string $modelLabel = 'Transaksi Masuk';

    protected static ?string $slug = 'transaksi';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
                Forms\Components\Section::make('Detail Transaksi')
                    ->schema([
                        Forms\Components\Select::make('tenant_id')
                            ->relationship('tenant', 'name')
                            ->label('Sekolah')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('order_id')
                            ->label('Order ID')
                            ->disabled(),

                        Forms\Components\TextInput::make('amount')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('plan_type')
                            ->label('Paket')
                            ->disabled(),

                        Forms\Components\TextInput::make('status')
                            ->disabled(),
                    ])->columns(2),

                Forms\Components\Section::make('Bukti Pembayaran')
                    ->schema([
                        Forms\Components\FileUpload::make('proof_of_payment')
                            ->label('Foto Bukti Transfer')
                            ->image()
                            ->disk('public_uploads')
                            ->visibility('public')
                            ->columnSpanFull()
                            ->openable()
                            ->deletable(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('tenant.name')
                    ->label('Sekolah')
                    ->searchable()
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('plan_type')
                    ->label('Paket'),
                
                Tables\Columns\TextColumn::make('amount')
                    ->money('IDR')
                    ->sortable(),
                
                Tables\Columns\ImageColumn::make('proof_of_payment')
                    ->label('Bukti')
                    ->disk('public_uploads')
                    ->visibility('public')
                    ->url(fn (Transaction $record) => $record->proof_of_payment ? Storage::disk('public_uploads')->url($record->proof_of_payment) : null)
                    ->openUrlInNewTab(), 

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Terima')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Verifikasi Pembayaran')
                    ->modalDescription('Apakah Anda yakin dana sudah masuk? Langganan sekolah akan otomatis diperpanjang sesuai paket.')
                    ->visible(fn (Transaction $record) => $record->status === 'pending')
                    ->action(function (Transaction $record) {
                        $record->update(['status' => 'paid']);

                        $tenant = Tenant::find($record->tenant_id);
                        if ($tenant) {
                            $currentEnd = $tenant->subscription_ends_at ? Carbon::parse($tenant->subscription_ends_at) : now();
                            if ($currentEnd->isPast()) {
                                $currentEnd = now();
                            }
                            
                            $plan = SubscriptionPlan::where('name', $record->plan_type)->first();
                            $daysToAdd = $plan ? $plan->duration_in_days : 30; 

                            $newEnd = $currentEnd->addDays($daysToAdd);

                            $tenant->update([
                                'subscription_status' => 'active',
                                'subscription_ends_at' => $newEnd,
                            ]);
                        }

                        Notification::make()->title('Langganan Diaktifkan')->success()->send();
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Transaction $record) => $record->status === 'pending')
                    ->action(fn (Transaction $record) => $record->update(['status' => 'cancelled'])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
        ];
    }
}