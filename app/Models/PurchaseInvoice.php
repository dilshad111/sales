<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseInvoice extends Model
{
    protected $fillable = [
        'invoice_number', 'supplier_invoice_number', 'date', 'posting_date', 'supplier_id', 'agent_id', 
        'commission_percentage', 'gross_amount', 'tax_percentage', 'tax_amount', 'commission_amount', 
        'net_amount', 'status', 'notes', 'created_by'
    ];

    protected $casts = [
        'date' => 'date',
        'posting_date' => 'date',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseInvoiceItem::class);
    }

    public function recoveries(): HasMany
    {
        return $this->hasMany(Recovery::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
