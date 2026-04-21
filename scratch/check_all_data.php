<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Account;
use App\Models\PaymentParty;

echo "--- ACCOUNTS ---\n";
$accounts = Account::all();
foreach ($accounts as $a) {
    echo "ID: {$a->id}, Name: [{$a->name}], PP_ID: {$a->payment_party_id}, Type: {$a->type}\n";
}

echo "\n--- PAYMENT PARTIES ---\n";
$pps = PaymentParty::all();
foreach ($pps as $pp) {
    echo "ID: {$pp->id}, Name: [{$pp->name}]\n";
}
