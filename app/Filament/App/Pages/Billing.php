<?php

namespace App\Filament\App\Pages;

use App\Models\BankAccount;
use App\Models\SubscriptionPlan;
use App\Models\Transaction;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class Billing extends Page implements HasActions
{
    use InteractsWithActions;
    use WithFileUploads;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $title = 'Status Langganan';
    protected string $view = 'filament.app.pages.billing';
    protected static ?int $navigationSort = 6;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->role === 'admin_sekolah' || auth()->user()->role === 'super_admin';
    }

    public $tenant;
    public $pendingTransaction = null;
    public $proofAttachment;
    public $bankAccounts;
    public $plans;
    public $history = [];

    public function mount()
    {
        $this->tenant = Filament::getTenant();
        $this->bankAccounts = BankAccount::where('is_active', true)->get();
        
        $this->plans = SubscriptionPlan::where('is_active', true)
            ->where('price', '>', 0)
            ->orderBy('sort_order')
            ->get();

        $this->refreshData();
    }

    public function refreshData()
    {
        $this->pendingTransaction = Transaction::with('bankAccount')
            ->where('tenant_id', $this->tenant->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        $this->history = Transaction::where('tenant_id', $this->tenant->id)
            ->when($this->pendingTransaction, function ($query) {
                $query->where('id', '!=', $this->pendingTransaction->id);
            })
            ->latest()
            ->get();
    }

    public function subscribeAction(): Action
    {
        return Action::make('subscribe')
            ->label('Pilih Paket')
            ->modalHeading('Metode Pembayaran')
            ->modalDescription('Silakan pilih Bank Tujuan Transfer.')
            ->modalSubmitActionLabel('Buat Tagihan')
            ->form([
                Select::make('bank_account_id')
                    ->label('Bank Tujuan')
                    ->options(BankAccount::where('is_active', true)->get()->pluck('bank_name', 'id'))
                    ->required()
                    ->native(true),
            ])
            ->action(function (array $data, array $arguments) {
                if (auth()->user()->role === 'konselor') {
                    Notification::make()->title('Akses Ditolak')->danger()->send();
                    return;
                }

                $planId = $arguments['plan_id'] ?? null;
                $plan = SubscriptionPlan::find($planId);

                if (!$plan) {
                    Notification::make()->title('Paket tidak ditemukan.')->danger()->send();
                    return;
                }

                $orderId = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(4));

                Transaction::create([
                    'tenant_id' => $this->tenant->id,
                    'bank_account_id' => $data['bank_account_id'],
                    'order_id' => $orderId,
                    'amount' => $plan->price,
                    'status' => 'pending',
                    'plan_type' => $plan->name,
                    'payment_method' => 'manual_transfer',
                ]);

                $this->refreshData();

                Notification::make()
                    ->title('Tagihan Dibuat')
                    ->body("Silakan selesaikan pembayaran untuk {$plan->name}.")
                    ->success()
                    ->send();
            });
    }

    public function uploadProof()
    {
        $this->validate([
            'proofAttachment' => 'required|image|max:2048',
        ]);

        if ($this->pendingTransaction) {
            $path = $this->proofAttachment->store('payment-proofs', 'public_uploads');

            $this->pendingTransaction->update([
                'proof_of_payment' => $path,
            ]);

            $this->proofAttachment = null;
            $this->refreshData();

            Notification::make()->title('Bukti pembayaran diterima!')->success()->send();
        }
    }

    public function cancelOrderAction(): Action
    {
        return Action::make('cancelOrder')
            ->label('Batalkan Tagihan')
            ->color('gray')
            ->size('sm')
            ->link()
            ->requiresConfirmation()
            ->modalHeading('Batalkan Tagihan')
            ->modalDescription('Apakah Anda yakin ingin membatalkan tagihan ini? Tindakan ini tidak dapat dikembalikan.')
            ->modalSubmitActionLabel('Ya, Batalkan')
            ->action(function () {
                if ($this->pendingTransaction) {
                    $this->pendingTransaction->update(['status' => 'cancelled']);
                    $this->pendingTransaction = null;
                    $this->proofAttachment = null;
                    
                    $this->refreshData();
                    
                    Notification::make()->title('Tagihan dibatalkan.')->info()->send();
                }
            });
    }

    public function confirmWaAction(): Action
    {
        return Action::make('confirmWa')
            ->label('Konfirmasi via WhatsApp')
            ->color('success')
            ->icon('heroicon-m-chat-bubble-left-right')
            ->action(function () {
                if (!$this->pendingTransaction) return;

                $trx = $this->pendingTransaction;
                $planName = $trx->plan_type; 
                $schoolName = $this->tenant->name;
                
                $adminWa = env('ADMIN_WA_NUMBER', '6281234567890');
                
                $message = "Halo Admin SIBK-digi,%0A%0A";
                $message .= "Saya *{$schoolName}* telah melakukan pembayaran paket langganan SIBK-digi *{$planName}*.%0A";
                $message .= "Order ID: *{$trx->order_id}*%0A";
                $message .= "Mohon segera konfirmasi, terima kasih.";

                $waLink = "https://wa.me/{$adminWa}?text={$message}";

                $this->dispatch('open-wa-link', url: $waLink);
            });
    }
}