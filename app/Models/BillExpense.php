<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillExpense extends Model
{
    protected $fillable = ['bill_id', 'description', 'amount'];

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
}
