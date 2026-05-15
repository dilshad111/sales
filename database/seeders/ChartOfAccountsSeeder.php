<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChartOfAccountsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Assets (1000)
        $assets = Account::firstOrCreate(['name' => 'Assets'], [
            'code' => '1000',
            'is_group' => true,
            'type' => 'Asset',
            'category' => 'general'
        ]);

        // 1.1 Fixed Assets (1100)
        $fixedAssets = Account::firstOrCreate(['name' => 'Fixed Assets', 'parent_id' => $assets->id], [
            'code' => '1100',
            'is_group' => true,
            'type' => 'Asset',
            'category' => 'general'
        ]);

        $fixedAssetItems = [
            ['name' => 'Plant & Machinery', 'code' => '1110'],
            ['name' => 'Vehicles', 'code' => '1120'],
            ['name' => 'Office Equipment', 'code' => '1130'],
            ['name' => 'Furniture & Fixtures', 'code' => '1140'],
            ['name' => 'Buildings', 'code' => '1150'],
            ['name' => 'Land', 'code' => '1160'],
        ];

        foreach ($fixedAssetItems as $item) {
            Account::firstOrCreate(['name' => $item['name'], 'parent_id' => $fixedAssets->id], [
                'code' => $item['code'],
                'is_group' => false,
                'type' => 'Asset',
                'category' => 'general'
            ]);
        }

        // 1.2 Current Assets (1200)
        $currentAssets = Account::firstOrCreate(['name' => 'Current Assets', 'parent_id' => $assets->id], [
            'code' => '1200',
            'is_group' => true,
            'type' => 'Asset',
            'category' => 'general'
        ]);

        Account::firstOrCreate(['name' => 'Cash in Hand', 'parent_id' => $currentAssets->id], [
            'code' => '1210',
            'is_group' => false,
            'type' => 'Asset',
            'category' => 'general'
        ]);

        Account::firstOrCreate(['name' => 'Petty Cash', 'parent_id' => $currentAssets->id], [
            'code' => '1211',
            'is_group' => false,
            'type' => 'Asset',
            'category' => 'general'
        ]);

        $bank = Account::firstOrCreate(['name' => 'Bank Accounts', 'parent_id' => $currentAssets->id], [
            'code' => '1220',
            'is_group' => true,
            'type' => 'Asset',
            'category' => 'general'
        ]);

        $ar = Account::firstOrCreate(['name' => 'Accounts Receivable', 'parent_id' => $currentAssets->id], [
            'code' => '1230',
            'is_group' => true,
            'type' => 'Asset',
            'category' => 'general'
        ]);

        Account::firstOrCreate(['name' => 'Inventory / Stock', 'parent_id' => $currentAssets->id], [
            'code' => '1240',
            'is_group' => false,
            'type' => 'Asset',
            'category' => 'general'
        ]);

        Account::firstOrCreate(['name' => 'Advance Tax / WHT', 'parent_id' => $currentAssets->id], [
            'code' => '1250',
            'is_group' => false,
            'type' => 'Asset',
            'category' => 'general'
        ]);

        Account::firstOrCreate(['name' => 'Security Deposits', 'parent_id' => $currentAssets->id], [
            'code' => '1260',
            'is_group' => false,
            'type' => 'Asset',
            'category' => 'general'
        ]);

        // 2. Liabilities (2000)
        $liabilities = Account::firstOrCreate(['name' => 'Liabilities'], [
            'code' => '2000',
            'is_group' => true,
            'type' => 'Liability',
            'category' => 'general'
        ]);

        $currentLiabilities = Account::firstOrCreate(['name' => 'Current Liabilities', 'parent_id' => $liabilities->id], [
            'code' => '2100',
            'is_group' => true,
            'type' => 'Liability',
            'category' => 'general'
        ]);

        $tradeCreditors = Account::firstOrCreate(['name' => 'Trade Creditors', 'parent_id' => $currentLiabilities->id], [
            'code' => '2120',
            'is_group' => true,
            'type' => 'Liability',
            'category' => 'general'
        ]);

        Account::firstOrCreate(['name' => 'Accrued Expenses', 'parent_id' => $currentLiabilities->id], [
            'code' => '2130',
            'is_group' => false,
            'type' => 'Liability',
            'category' => 'general'
        ]);

        Account::firstOrCreate(['name' => 'Sales Tax Payable', 'parent_id' => $currentLiabilities->id], [
            'code' => '2140',
            'is_group' => false,
            'type' => 'Liability',
            'category' => 'general'
        ]);

        // 3. Equity (3000)
        $equity = Account::firstOrCreate(['name' => 'Equity'], [
            'code' => '3000',
            'is_group' => true,
            'type' => 'Equity',
            'category' => 'general'
        ]);

        Account::firstOrCreate(['name' => 'Capital Account', 'parent_id' => $equity->id], [
            'code' => '3100',
            'is_group' => false,
            'type' => 'Equity',
            'category' => 'general'
        ]);

        Account::firstOrCreate(['name' => 'Drawings', 'parent_id' => $equity->id], [
            'code' => '3200',
            'is_group' => false,
            'type' => 'Equity',
            'category' => 'general'
        ]);

        Account::firstOrCreate(['name' => 'Retained Earnings', 'parent_id' => $equity->id], [
            'code' => '3300',
            'is_group' => false,
            'type' => 'Equity',
            'category' => 'general'
        ]);

        // 4. Income (4000)
        $income = Account::firstOrCreate(['name' => 'Income'], [
            'code' => '4000',
            'is_group' => true,
            'type' => 'Income',
            'category' => 'general'
        ]);

        Account::firstOrCreate(['name' => 'Sales Revenue', 'parent_id' => $income->id], [
            'code' => '4100',
            'is_group' => false,
            'type' => 'Income',
            'category' => 'general'
        ]);

        Account::firstOrCreate(['name' => 'Service Income', 'parent_id' => $income->id], [
            'code' => '4200',
            'is_group' => false,
            'type' => 'Income',
            'category' => 'general'
        ]);

        Account::firstOrCreate(['name' => 'Other Income', 'parent_id' => $income->id], [
            'code' => '4900',
            'is_group' => false,
            'type' => 'Income',
            'category' => 'general'
        ]);

        // 5. Expenses (5000)
        $expenses = Account::firstOrCreate(['name' => 'Expenses'], [
            'code' => '5000',
            'is_group' => true,
            'type' => 'Expense',
            'category' => 'general'
        ]);

        // 5.1 Direct Expenses / COGS (5100)
        $directExpenses = Account::firstOrCreate(['name' => 'Direct Expenses / COGS', 'parent_id' => $expenses->id], [
            'code' => '5100',
            'is_group' => true,
            'type' => 'Expense',
            'category' => 'general'
        ]);

        Account::firstOrCreate(['name' => 'Purchases', 'parent_id' => $directExpenses->id], [
            'code' => '5110',
            'is_group' => false,
            'type' => 'Expense',
            'category' => 'general'
        ]);

        Account::firstOrCreate(['name' => 'Carriage Inwards', 'parent_id' => $directExpenses->id], [
            'code' => '5120',
            'is_group' => false,
            'type' => 'Expense',
            'category' => 'general'
        ]);

        Account::firstOrCreate(['name' => 'Factory Wages', 'parent_id' => $directExpenses->id], [
            'code' => '5130',
            'is_group' => false,
            'type' => 'Expense',
            'category' => 'general'
        ]);

        // 5.2 Administrative Expenses (5200)
        $adminExpenses = Account::firstOrCreate(['name' => 'Administrative Expenses', 'parent_id' => $expenses->id], [
            'code' => '5200',
            'is_group' => true,
            'type' => 'Expense',
            'category' => 'general'
        ]);

        $adminItems = [
            ['name' => 'Salaries & Benefits', 'code' => '5210'],
            ['name' => 'Rent, Rates & Taxes', 'code' => '5220'],
            ['name' => 'Electricity Expense', 'code' => '5230'],
            ['name' => 'Gas & Water Charges', 'code' => '5231'],
            ['name' => 'Printing & Stationery', 'code' => '5240'],
            ['name' => 'Phone & Internet', 'code' => '5250'],
            ['name' => 'Repair & Maintenance', 'code' => '5260'],
            ['name' => 'Legal & Professional', 'code' => '5270'],
            ['name' => 'Traveling & Conveyance', 'code' => '5280'],
            ['name' => 'Office Supplies', 'code' => '5290'],
        ];

        foreach ($adminItems as $item) {
            Account::firstOrCreate(['name' => $item['name'], 'parent_id' => $adminExpenses->id], [
                'code' => $item['code'],
                'is_group' => false,
                'type' => 'Expense',
                'category' => 'general'
            ]);
        }

        // 5.3 Selling & Distribution (5300)
        $sellingExpenses = Account::firstOrCreate(['name' => 'Selling & Distribution', 'parent_id' => $expenses->id], [
            'code' => '5300',
            'is_group' => true,
            'type' => 'Expense',
            'category' => 'general'
        ]);

        Account::firstOrCreate(['name' => 'Advertisement & Marketing', 'parent_id' => $sellingExpenses->id], [
            'code' => '5310',
            'is_group' => false,
            'type' => 'Expense',
            'category' => 'general'
        ]);

        Account::firstOrCreate(['name' => 'Carriage Outwards', 'parent_id' => $sellingExpenses->id], [
            'code' => '5320',
            'is_group' => false,
            'type' => 'Expense',
            'category' => 'general'
        ]);

        // 5.4 Finance Cost (5400)
        $financeExpenses = Account::firstOrCreate(['name' => 'Finance Cost', 'parent_id' => $expenses->id], [
            'code' => '5400',
            'is_group' => true,
            'type' => 'Expense',
            'category' => 'general'
        ]);

        Account::firstOrCreate(['name' => 'Bank Charges', 'parent_id' => $financeExpenses->id], [
            'code' => '5410',
            'is_group' => false,
            'type' => 'Expense',
            'category' => 'general'
        ]);

        // Migrate existing accounts
        // Customers -> Accounts Receivable
        Account::where('category', 'customer')->update([
            'parent_id' => $ar->id,
            'type' => 'Asset',
            'is_group' => false
        ]);

        // Suppliers -> Trade Creditors
        Account::where('category', 'supplier')->update([
            'parent_id' => $tradeCreditors->id,
            'type' => 'Liability',
            'is_group' => false
        ]);

        // Final fallback for any other accounts
        Account::whereNull('type')->update([
            'type' => 'Expense',
            'parent_id' => $expenses->id,
            'is_group' => false
        ]);
    }
}
