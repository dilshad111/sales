<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Transaction;
use App\Models\TransactionEntry;
use App\Models\Account;
use Illuminate\Support\Facades\DB;

try {
    DB::beginTransaction();
    $acc1 = Account::where('is_group', false)->first();
    $acc2 = Account::where('is_group', false)->skip(1)->first();
    
    if (!$acc1 || !$acc2) {
        echo "Not enough accounts\n";
        exit;
    }

    $txn = Transaction::create([
        'date' => '2026-04-18',
        'type' => 'PV',
        'narration' => 'DEBUG PV',
        'total_amount' => 500,
        'created_by' => 2 // Valid user ID from my check
    ]);
    
    TransactionEntry::create([
        'transaction_id' => $txn->id,
        'account_id' => $acc1->id,
        'debit' => 500,
        'credit' => 0
    ]);
    
    TransactionEntry::create([
        'transaction_id' => $txn->id,
        'account_id' => $acc2->id,
        'debit' => 0,
        'credit' => 500
    ]);
    
    DB::commit();
    echo "Saved PV with ID: " . $txn->id . " and number: " . $txn->transaction_number . "\n";
} catch (\Exception $e) {
    DB::rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}
