<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChartOfAccountsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Assets
        $assets = Account::firstOrCreate(['name' => 'Assets'], [
            'code' => '1000',
            'is_group' => true,
            'type' => 'Asset',
            'category' => 'general'
        ]);

        $currentAssets = Account::firstOrCreate(['name' => 'Current Assets', 'parent_id' => $assets->id], [
            'code' => '1100',
            'is_group' => true,
            'type' => 'Asset',
            'category' => 'general'
        ]);

        Account::where('name', 'Cash in Hand')->update([
            'code' => '1110',
            'parent_id' => $currentAssets->id,
            'is_group' => false,
            'type' => 'Asset',
            'category' => 'general'
        ]);

        $bank = Account::firstOrCreate(['name' => 'Bank Accounts', 'parent_id' => $currentAssets->id], [
            'code' => '1120',
            'is_group' => true,
            'type' => 'Asset',
            'category' => 'general'
        ]);

        $ar = Account::firstOrCreate(['name' => 'Accounts Receivable', 'parent_id' => $currentAssets->id], [
            'code' => '1200',
            'is_group' => true,
            'type' => 'Asset',
            'category' => 'general'
        ]);

        // 2. Liabilities
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

        $ap = Account::firstOrCreate(['name' => 'Accounts Payable', 'parent_id' => $currentLiabilities->id], [
            'code' => '2200',
            'is_group' => true,
            'type' => 'Liability',
            'category' => 'general'
        ]);

        $tradeCreditors = Account::firstOrCreate(['name' => 'Trade Creditors', 'parent_id' => $currentLiabilities->id], [
            'code' => '2210',
            'is_group' => true,
            'type' => 'Liability',
            'category' => 'general'
        ]);

        // 3. Income
        $income = Account::firstOrCreate(['name' => 'Income'], [
            'code' => '3000',
            'is_group' => true,
            'type' => 'Income',
            'category' => 'general'
        ]);

        Account::where('name', 'Sales Revenue')->update([
            'code' => '3100',
            'parent_id' => $income->id,
            'is_group' => false,
            'type' => 'Income',
            'category' => 'general'
        ]);

        // 4. Expenses
        $expenses = Account::firstOrCreate(['name' => 'Expenses'], [
            'code' => '4000',
            'is_group' => true,
            'type' => 'Expense',
            'category' => 'general'
        ]);

        $directExpenses = Account::firstOrCreate(['name' => 'Direct Expenses', 'parent_id' => $expenses->id], [
            'code' => '4100',
            'is_group' => true,
            'type' => 'Expense',
            'category' => 'general'
        ]);

        Account::where('name', 'General Expenses')->update([
            'code' => '4200',
            'parent_id' => $expenses->id,
            'is_group' => false,
            'type' => 'Expense',
            'category' => 'general'
        ]);

        // 5. Equity
        $equity = Account::firstOrCreate(['name' => 'Equity'], [
            'code' => '5000',
            'is_group' => true,
            'type' => 'Equity',
            'category' => 'general'
        ]);

        Account::firstOrCreate(['name' => 'Capital Account', 'parent_id' => $equity->id], [
            'code' => '5100',
            'is_group' => false,
            'type' => 'Equity',
            'category' => 'general'
        ]);

        // Migrate existing accounts
        // Customers -> Accounts Receivable
        Account::where('category', 'customer')->update([
            'parent_id' => $ar->id,
            'type' => 'Asset',
            'is_group' => false
        ]);

        // Suppliers -> Trade Creditors (Preferring Trade Creditors for suppliers)
        Account::where('category', 'supplier')->update([
            'parent_id' => $tradeCreditors->id,
            'type' => 'Liability',
            'is_group' => false
        ]);

        // Payment Parties (External Parties) -> Could be Liabilities or other Assets. 
        // For now, let's put them under a new group "Financial Partners" under Liabilities (or Assets depending on nature)
        // Usually they are liabilities if we owe them.
        $partners = Account::firstOrCreate(['name' => 'Special Partners', 'parent_id' => $currentLiabilities->id], [
            'code' => '2300',
            'is_group' => true,
            'type' => 'Liability',
            'category' => 'general'
        ]);

        Account::where('category', 'payment_party')->orWhere('category', 'external_party')->update([
            'parent_id' => $partners->id,
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
