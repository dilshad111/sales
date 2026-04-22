<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    protected $fillable = ['so_number', 'po_number', 'po_date', 'so_date', 'customer_id', 'total_amount', 'tax_percent', 'tax_amount', 'grand_total', 'status', 'remarks'];

    protected $casts = [
        'po_date' => 'date',
        'so_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->so_number)) {
                $setting = \App\Models\CompanySetting::first();
                $prefix = 'SO# '; // Standardized format
                
                $lastOrder = static::where('so_number', 'like', $prefix . '%')->orderBy('id', 'desc')->first();
                $nextNumber = 1;
                if ($lastOrder) {
                    $lastNumStr = str_replace($prefix, '', $lastOrder->so_number);
                    $nextNumber = (int) $lastNumStr + 1;
                }
                
                $order->so_number = $prefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(SalesOrderItem::class);
    }
}
