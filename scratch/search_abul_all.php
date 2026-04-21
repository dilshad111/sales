<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Account;

echo "--- USERS ---\n";
$users = User::where('name', 'like', '%Abul%')->get();
foreach ($users as $u) {
    echo "ID: {$u->id}, Name: [{$u->name}]\n";
}

echo "\n--- ACCOUNTS ---\n";
$accounts = Account::where('name', 'like', '%Abul%')->get();
foreach ($accounts as $a) {
    echo "ID: {$a->id}, Name: [{$a->name}], UserID: {$a->user_id}, PP_ID: {$a->payment_party_id}\n";
}
