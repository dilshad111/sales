<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartonCosting extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'user_id',
        'fefco_code',
        'ply',
        'length',
        'width',
        'height',
        'wastage_rate',
        'overhead_rate',
        'profit_rate',
        'sheet_width',
        'sheet_length',
        'sheet_width_m',
        'sheet_length_m',
        'sheet_area',
        'total_paper_cost',
        'wastage_amount',
        'cost_after_wastage',
        'overhead_amount',
        'cost_before_profit',
        'profit_amount',
        'final_carton_cost',
        'layers',
    ];

    protected $casts = [
        'layers' => 'array',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
