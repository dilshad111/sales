<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentParty extends Model
{
    protected $fillable = ['name', 'phone', 'email', 'address', 'opening_balance', 'status'];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function account()
    {
        return $this->hasOne(Account::class, 'payment_party_id');
    }
}
