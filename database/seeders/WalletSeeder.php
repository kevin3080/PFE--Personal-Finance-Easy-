<?php

namespace Database\Seeders;

use App\Models\AccountType;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;

class WalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user= User::create([
            'name' => 'John Doe',
            'email' => 'example@example.com',
            'password' => bcrypt('password'),
        ]);

        $cashWallet = AccountType::create([
            'type_account' => 'Cash',
            'balance' => 1000.00
        ]);

        $bankWallet = AccountType::create([
            'type_account' => 'Bank Account',
            'balance' => 1000.00
        ]);

        Wallet::create([
            'user_id' => $user->id,
            'account_type_id' => $cashWallet->id
        ]);

        Wallet::create([
            'user_id' => $user->id,
            'account_type_id' => $bankWallet->id
        ]);
    }
}
