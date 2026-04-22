<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrderItem extends Model
{
    protected $fillable = ['sales_order_id', 'item_id', 'quantity', 'unit_price', 'total_price', 'delivery_date', 'remarks'];

    protected $casts = [
        'delivery_date' => 'date',
    ];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function challanItems()
    {
        return $this->hasMany(DeliveryChallanItem::class, 'sales_order_item_id');
    }

    public function getDeliveredQuantityAttribute()
    {
        return (int) $this->challanItems()->sum('quantity');
    }

    public function getRemainingQuantityAttribute()
    {
        return max(0, $this->quantity - $this->delivered_quantity);
    }
}
