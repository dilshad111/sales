<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Supplier extends Model implements Auditable
{
    use AuditableTrait;

    protected $fillable = ['name', 'phone', 'email', 'address', 'status'];

    public function account()
    {
        return $this->hasOne(Account::class, 'supplier_id');
    }
}
