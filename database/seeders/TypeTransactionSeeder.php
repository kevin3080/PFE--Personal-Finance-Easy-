<?php

namespace Database\Seeders;

use App\Models\TransactionType;
use Illuminate\Database\Seeder;

class TypeTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TransactionType::create([
            'transaction_name' => 'expense',
        ]);
        TransactionType::create([
            'transaction_name' => 'income',
        ]);
    }
}
