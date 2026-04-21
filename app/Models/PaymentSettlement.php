<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSettlement extends Model
{
    protected $fillable = ['payment_id', 'bill_id', 'amount'];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
}
