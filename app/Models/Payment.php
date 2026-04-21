<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Payment extends Model implements Auditable
{
    use AuditableTrait;

    protected $fillable = [
        'customer_id', 'payment_party_id', 'recipient_account_id', 
        'destination_type', 'amount', 'payment_date', 'mode', 'remarks'
    ];

    public function recipientAccount()
    {
        return $this->belongsTo(Account::class, 'recipient_account_id');
    }

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

    public function settlements()
    {
        return $this->hasMany(PaymentSettlement::class);
    }
}
