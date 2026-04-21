<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Customer extends Model implements Auditable
{
    use AuditableTrait;

    protected $fillable = ['name', 'phone', 'email', 'address', 'status', 'type', 'opening_balance'];

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function account()
    {
        return $this->hasOne(Account::class, 'customer_id');
    }

    public function deliveryChallans()
    {
        return $this->hasMany(DeliveryChallan::class);
    }
}
