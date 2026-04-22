<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryChallan extends Model
{
    protected $fillable = ['challan_number', 'customer_id', 'challan_date', 'total', 'status', 'bill_id', 'remarks', 'vehicle_number', 'sales_order_id'];

    protected $casts = [
        'challan_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($challan) {
            if (empty($challan->challan_number)) {
                $setting = \App\Models\CompanySetting::first();
                $prefix = $setting ? ($setting->challan_prefix ?? 'DC') : 'DC';
                
                $lastChallan = static::where('challan_number', 'like', $prefix . '%')->orderBy('id', 'desc')->first();
                $nextNumber = 1;
                if ($lastChallan) {
                    $lastNumStr = str_replace($prefix, '', $lastChallan->challan_number);
                    $nextNumber = (int) $lastNumStr + 1;
                }
                
                $challan->challan_number = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(DeliveryChallanItem::class);
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
}
