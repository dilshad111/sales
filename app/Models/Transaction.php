<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Transaction extends Model implements Auditable
{
    use AuditableTrait;

    protected $fillable = [
        'financial_year_id',
        'transaction_number',
        'date',
        'type',
        'payment_mode',
        'bank_id',
        'reference_type',
        'reference_id',
        'narration',
        'cheque_number',
        'bank_name',
        'total_amount',
        'created_by',
        'updated_by'
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function getFormattedTypeAttribute()
    {
        return match($this->type) {
            'PV' => 'Payment Voucher',
            'RV' => 'Receive Voucher',
            'JV' => 'Journal Voucher',
            'CPV' => 'Cash Payment Voucher',
            'BPV' => 'Bank Payment Voucher',
            'third_party_payment' => 'Third Party Payment',
            'bill_payment' => 'Bill Payment',
            default => str_replace('_', ' ', ucfirst($this->type))
        };
    }

    public function created_by_user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updated_by_user()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function amount_in_words()
    {
        $number = (float) $this->total_amount;
        $words = $this->convertNumberToWords(floor($number));
        
        $decimal = round($number - floor($number), 2) * 100;
        $paise = "";
        if ($decimal > 0) {
            $paise = " and " . $this->convertNumberToWords($decimal) . " Paise";
        }

        $companySetting = \Illuminate\Support\Facades\Cache::get('company_setting');
        $currency = $companySetting ? ($companySetting->currency_symbol ?? 'Rupees') : 'Rupees';
        
        return $currency . " " . ucwords($words) . $paise . " Only";
    }

    private function convertNumberToWords($number)
    {
        $dictionary  = [
            0                   => 'zero',
            1                   => 'one',
            2                   => 'two',
            3                   => 'three',
            4                   => 'four',
            5                   => 'five',
            6                   => 'six',
            7                   => 'seven',
            8                   => 'eight',
            9                   => 'nine',
            10                  => 'ten',
            11                  => 'eleven',
            12                  => 'twelve',
            13                  => 'thirteen',
            14                  => 'fourteen',
            15                  => 'fifteen',
            16                  => 'sixteen',
            17                  => 'seventeen',
            18                  => 'eighteen',
            19                  => 'nineteen',
            20                  => 'twenty',
            30                  => 'thirty',
            40                  => 'forty',
            50                  => 'fifty',
            60                  => 'sixty',
            70                  => 'seventy',
            80                  => 'eighty',
            90                  => 'ninety',
            100                 => 'hundred',
            1000                => 'thousand',
            1000000             => 'million',
            1000000000          => 'billion',
            1000000000000       => 'trillion'
        ];

        if (!is_numeric($number)) return false;

        if ($number < 0) return 'negative ' . $this->convertNumberToWords(abs($number));

        $string = $fraction = null;

        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }

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
                    $string .= ' and ' . $this->convertNumberToWords($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = $this->convertNumberToWords($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? ' and ' : ', ';
                    $string .= $this->convertNumberToWords($remainder);
                }
                break;
        }

        return $string;
    }

    protected $casts = [
        'date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
                $type = $transaction->type ?: 'JV'; 
                $date = $transaction->date ?: now();
                
                $setting = \App\Models\CompanySetting::first();
                $basePrefix = match($type) {
                    'PV' => $setting->pv_prefix ?? 'PV',
                    'RV' => $setting->rv_prefix ?? 'RV',
                    'JV' => $setting->jv_prefix ?? 'JV',
                    default => $type
                };

                $prefix = $basePrefix . '-' . $date->format('y') . $date->format('m') . '-';
                
                $lastTxn = static::where('transaction_number', 'like', $prefix . '%')
                    ->orderBy('transaction_number', 'desc')
                    ->first();
                
                $nextNumber = 1;
                if ($lastTxn) {
                    $parts = explode('-', $lastTxn->transaction_number);
                    $lastSerial = end($parts);
                    $nextNumber = (int) $lastSerial + 1;
                }
                
                $transaction->transaction_number = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        });
    }

    public function entries()
    {
        return $this->hasMany(TransactionEntry::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }
}
