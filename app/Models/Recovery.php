<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recovery extends Model
{
    protected $fillable = [
        'recovery_number', 'purchase_invoice_id', 'agent_id', 
        'amount', 'commission_deducted', 'net_amount_transfered', 
        'director_account_id', 'date', 'notes', 'created_by'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(PurchaseInvoice::class, 'purchase_invoice_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }

    public function directorAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'director_account_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
