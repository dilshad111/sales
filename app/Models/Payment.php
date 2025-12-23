<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Payment extends Model implements Auditable
{
    use AuditableTrait;

    protected $fillable = ['customer_id', 'bill_id', 'payment_party_id', 'amount', 'payment_date', 'mode', 'remarks'];

    protected $casts = [
        'payment_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function paymentParty()
    {
        return $this->belongsTo(PaymentParty::class);
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
}
