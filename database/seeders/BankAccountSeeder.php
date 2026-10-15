<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use Illuminate\Database\Seeder;

class BankAccountSeeder extends Seeder
{
    public function run(): void
    {        
        BankAccount::create([
            'bank_name' => 'Bank BRI',
            'account_number' => '362101025849531',
            'account_holder' => 'Randi Iskandar',
            'is_active' => true,
        ]);

        BankAccount::create([
            'bank_name' => 'Bank Jago',
            'account_number' => '104972036197',
            'account_holder' => 'Randi Iskandar',
            'is_active' => true,
        ]);

        BankAccount::create([
            'bank_name' => 'Bank Dana',
            'account_number' => '081311665601',
            'account_holder' => 'Randi Iskandar',
            'is_active' => true,
        ]);
    }
}