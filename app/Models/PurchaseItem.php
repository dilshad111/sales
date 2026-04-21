<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class PurchaseItem extends Model implements Auditable
{
    use AuditableTrait;

    protected $fillable = ['supplier_id', 'name', 'unit', 'purchase_price', 'status'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
