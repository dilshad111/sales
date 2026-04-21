<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\DeliveryChallan;

$dc = DeliveryChallan::with('items.item', 'customer')->where('challan_number', 'DC0001')->first();
if ($dc) {
    echo "DC: {$dc->challan_number} (Customer: {$dc->customer->name}, Status: {$dc->status})\n";
    echo "Total: {$dc->total}\n";
    foreach ($dc->items as $item) {
        $priceInItem = optional($item->item)->price;
        echo "- {$item->item->name}: Qty {$item->quantity}, ItemPrice {$priceInItem}, ItemStoredPrice {$item->price}, LineTotal {$item->total}\n";
    }
} else {
    echo "No DCs found.";
}
?>
