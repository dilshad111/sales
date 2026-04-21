<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Item extends Model implements Auditable
{
    use AuditableTrait;

    protected $fillable = ['code', 'customer_id', 'name', 'uom', 'price'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            if (empty($item->code)) {
                $lastItem = static::orderBy('id', 'desc')->first();
                $nextNumber = $lastItem ? ((int) substr($lastItem->code, 3)) + 1 : 1;
                $item->code = 'CTN' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
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
}
