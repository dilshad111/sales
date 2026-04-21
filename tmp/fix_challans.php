<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\DeliveryChallan;

$updatedCount = DeliveryChallan::where('status', 'billed')
    ->where(function($query) {
        $query->whereNull('bill_id')
              ->orWhereNotExists(function ($query) {
                  $query->select(Illuminate\Support\Facades\DB::raw(1))
                        ->from('bills')
                        ->whereColumn('bills.id', 'delivery_challans.bill_id');
              });
    })
    ->update(['status' => 'pending', 'bill_id' => null]);

echo "Updated $updatedCount delivery challans back to pending status.";
?>
