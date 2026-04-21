<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Bill extends Model implements Auditable
{
    use AuditableTrait;

    protected $fillable = ['customer_id', 'bill_number', 'bill_date', 'total', 'discount', 'tax', 'tax_percent', 'remarks'];

    public function amount_in_words()
    {
        $number = (float) $this->total;
        $whole = floor($number);
        $decimal = round($number - $whole, 2) * 100;
        
        $words = $this->convertNumberToWords($whole);
        
        $paisa = "";
        if ($decimal > 0) {
            $paisa = " and " . $this->convertNumberToWords($decimal) . " Paisa";
        }
        
        return "Rupees " . ucwords($words) . $paisa . " Only";
    }

    private function convertNumberToWords($number, $isFinal = true)
    {
        $dictionary = [
            0 => 'zero', 1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four', 5 => 'five',
            6 => 'six', 7 => 'seven', 8 => 'eight', 9 => 'nine', 10 => 'ten',
            11 => 'eleven', 12 => 'twelve', 13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
            16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen', 19 => 'nineteen', 20 => 'twenty',
            30 => 'thirty', 40 => 'forty', 50 => 'fifty', 60 => 'sixty', 70 => 'seventy',
            80 => 'eighty', 90 => 'ninety', 100 => 'hundred', 1000 => 'thousand',
            1000000 => 'million', 1000000000 => 'billion'
        ];

        if (!is_numeric($number)) return false;
        if ($number < 0) return 'negative ' . $this->convertNumberToWords(abs($number), $isFinal);

        $string = null;

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens   = ((int) ($number / 10)) * 10;
                $units  = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= '-' . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds  = (int) ($number / 100);
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    // Only add 'and' in the hundred's part if it's the final sequence of the whole number
                    $string .= ($isFinal ? ' and ' : ' ') . $this->convertNumberToWords($remainder, $isFinal);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                
                // For the million/thousand parts, we pass false so they don't use 'and'
                $string = $this->convertNumberToWords($numBaseUnits, false) . ' ' . $dictionary[$baseUnit];
                
                if ($remainder) {
                    $string .= $remainder < 100 ? ' and ' : ' ';
                    $string .= $this->convertNumberToWords($remainder, $isFinal);
                }
                break;
        }

        return $string;
    }

    protected $casts = [
        'bill_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($bill) {
            if (empty($bill->bill_number)) {
                $setting = \App\Models\CompanySetting::first();
                $prefix = $setting ? ($setting->bill_prefix ?? 'INV') : 'INV';
                
                $lastBill = static::where('bill_number', 'like', $prefix . '%')->orderBy('id', 'desc')->first();
                $nextNumber = 1;
                if ($lastBill) {
                    $lastNumStr = str_replace($prefix, '', $lastBill->bill_number);
                    $nextNumber = (int) $lastNumStr + 1;
                }
                
                $bill->bill_number = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function billItems()
    {
        return $this->hasMany(BillItem::class);
    }

    public function payments()
    {
        return $this->hasMany(PaymentSettlement::class);
    }

    public function commissionDetails()
    {
        return $this->hasMany(CommissionDetail::class);
    }

    public function billExpenses()
    {
        return $this->hasMany(BillExpense::class);
    }

    public function deliveryChallans()
    {
        return $this->hasMany(DeliveryChallan::class);
    }
}
