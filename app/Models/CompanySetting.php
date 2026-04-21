<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    protected $fillable = [
        'name', 'address', 'phone', 'email', 'website', 'logo_path', 'tax_number', 
        'currency_symbol', 'bill_prefix', 'challan_prefix', 'pv_prefix', 'rv_prefix', 'jv_prefix', 'other_details'
    ];
}
