<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class PurchaseDeliveryChallan extends Model implements Auditable
{
    use AuditableTrait;

    protected $fillable = [
        'challan_number', 'supplier_dc_number', 'supplier_id', 'date', 'vehicle_number', 
        'total_amount', 'status', 'remarks', 'created_by'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseDeliveryChallanItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
