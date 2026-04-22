<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryChallanItem extends Model
{
    protected $fillable = ['delivery_challan_id', 'item_id', 'sales_order_item_id', 'quantity', 'bundles', 'price', 'total', 'delivery_date', 'remarks'];

    protected $casts = [
        'delivery_date' => 'date',
    ];

    public function deliveryChallan()
    {
        return $this->belongsTo(DeliveryChallan::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
