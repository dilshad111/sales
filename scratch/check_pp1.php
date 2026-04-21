<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Account;

$accounts = Account::where('payment_party_id', 1)->get();
echo "Accounts for PP 1:\n";
foreach ($accounts as $a) {
    echo "ID: {$a->id}, Name: [{$a->name}], Type: {$a->type}\n";
}
