<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseDeliveryChallanItem extends Model
{
    protected $fillable = [
        'purchase_delivery_challan_id', 'purchase_item_id', 'quantity', 
        'unit', 'rate', 'amount', 'tax_percent', 'tax_amount', 'total_amount'
    ];

    public function challan(): BelongsTo
    {
        return $this->belongsTo(PurchaseDeliveryChallan::class, 'purchase_delivery_challan_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(PurchaseItem::class, 'purchase_item_id');
    }
}
