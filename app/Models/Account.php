<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Account extends Model implements Auditable
{
    use AuditableTrait;

    protected $fillable = [
        'name', 
        'code',
        'parent_id',
        'is_group',
        'type', // Asset, Liability, Income, Expense
        'category', // customer, supplier, general
        'customer_id', 
        'payment_party_id',
        'third_party_id',
        'external_party_id',
        'supplier_id',
        'agent_id',
        'user_id',
        'phone', 
        'email', 
        'address', 
        'opening_balance', 
        'status', 
        'created_by', 
        'updated_by'
    ];

    public function parent()
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    public function thirdParty()
    {
        return $this->belongsTo(ThirdParty::class);
    }

    public function created_by_user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updated_by_user()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function paymentParty()
    {
        return $this->belongsTo(PaymentParty::class);
    }

    public function externalParty()
    {
        return $this->belongsTo(ExternalParty::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function isLeaf()
    {
        return !$this->is_group;
    }

    public function entries()
    {
        return $this->hasMany(TransactionEntry::class);
    }

    public function getBalanceAttribute()
    {
        return $this->getBalanceAtDate(now());
    }

    public function getBalanceAtDate($date)
    {
        if ($this->is_group) {
            $balance = 0;
            foreach ($this->children as $child) {
                $balance += $child->getBalanceAtDate($date);
            }
            return $balance;
        }

        $sums = $this->entries()
            ->whereHas('transaction', function($q) use ($date) {
                $q->where('date', '<=', $date);
            })
            ->selectRaw('SUM(debit) as debits, SUM(credit) as credits')
            ->first();

        $net = ($sums->debits ?? 0) - ($sums->credits ?? 0);
        return ($this->opening_balance ?? 0) + $net;
    }
}
