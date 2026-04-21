<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Account;

$accounts = Account::where('name', 'like', '%Abul Rehman Jali Maker%')->get();
foreach ($accounts as $a) {
    echo "ID: {$a->id}, Name: {$a->name}, PP_ID: {$a->payment_party_id}, Type: {$a->type}\n";
}
