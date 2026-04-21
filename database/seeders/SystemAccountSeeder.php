<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

class SystemAccountSeeder extends Seeder
{
    public function run(): void
    {
        $systemAccounts = [
            [
                'name' => 'Cash in Hand',
                'type' => 'general',
                'status' => 'active',
            ],
            [
                'name' => 'Sales Revenue',
                'type' => 'general',
                'status' => 'active',
            ],
            [
                'name' => 'General Expenses',
                'type' => 'general',
                'status' => 'active',
            ]
        ];

        foreach ($systemAccounts as $account) {
            Account::firstOrCreate(['name' => $account['name']], $account);
        }
    }
}
