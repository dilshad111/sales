<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Agent extends Model implements Auditable
{
    use AuditableTrait;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'commission_percentage',
        'status'
    ];

    public function purchaseInvoices()
    {
        return $this->hasMany(PurchaseInvoice::class);
    }

    public function recoveries()
    {
        return $this->hasMany(Recovery::class);
    }

    public function account()
    {
        return $this->hasOne(Account::class, 'agent_id');
    }
}
