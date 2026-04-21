<?php

define('LARAVEL_START', microtime(true));
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\Account;
use Illuminate\Support\Facades\DB;

// Function to generate and assign codes
function assignCodesToAccounts() {
    $types = [
        'Asset' => 1000,
        'Liability' => 2000,
        'Income' => 3000,
        'Expense' => 4000,
        'Equity' => 5000
    ];

    foreach ($types as $type => $base) {
        $roots = Account::where('type', $type)->whereNull('parent_id')->orderBy('id')->get();
        $counter = 0;
        foreach ($roots as $root) {
            if (empty($root->code) || !is_numeric($root->code)) {
                $newCode = $base + ($counter * 1000); // 1000, 2000...
                // Ensure uniqueness
                while (Account::where('code', $newCode)->exists()) {
                    $newCode += 1000;
                }
                $root->update(['code' => $newCode]);
                echo "Assigned $newCode to root account: {$root->name}\n";
            }
            $counter++;
            assignChildCodes($root);
        }
    }
}

function assignChildCodes($parent) {
    $children = $parent->children()->orderBy('id')->get();
    if ($children->isEmpty()) return;

    $parentCode = (int)$parent->code;
    
    // Determine step based on parent level
    // 1000 -> step 100
    // 1100 -> step 10
    // 1110 -> step 1
    if ($parentCode % 1000 === 0) { $step = 100; }
    elseif ($parentCode % 100 === 0) { $step = 10; }
    else { $step = 1; }

    $childCounter = 1;
    foreach ($children as $child) {
        if (empty($child->code) || !is_numeric($child->code)) {
            $newCode = $parentCode + ($childCounter * $step);
            // Ensure uniqueness
            while (Account::where('code', $newCode)->exists()) {
                $newCode += $step;
            }
            $child->update(['code' => $newCode]);
            echo "Assigned $newCode to child account: {$child->name}\n";
        }
        $childCounter++;
        assignChildCodes($child);
    }
}

// Run the script
DB::transaction(function() {
    assignCodesToAccounts();
});
