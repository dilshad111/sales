<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Bill extends Model implements Auditable
{
    use AuditableTrait;

    protected $fillable = ['bill_number', 'customer_id', 'bill_date', 'total', 'discount', 'tax'];
    protected $casts = [
        'bill_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($bill) {
            if (empty($bill->bill_number)) {
                $lastBill = static::orderBy('id', 'desc')->first();
                $nextNumber = $lastBill ? ((int) substr($lastBill->bill_number, 4)) + 1 : 1;
                $bill->bill_number = 'BILL' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function billItems()
    {
        return $this->hasMany(BillItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function commissionDetails()
    {
        return $this->hasMany(CommissionDetail::class);
    }
}
