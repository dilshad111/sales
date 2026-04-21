<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_closed',
        'closed_at',
        'closed_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_closed' => 'boolean',
        'closed_at' => 'datetime',
    ];

    /**
     * Get transactions belonging to this financial year.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the user who closed the financial year.
     */
    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    /**
     * Check if a date falls within this financial year.
     */
    public function coversDate($date): bool
    {
        $date = \Carbon\Carbon::parse($date);
        return $date->between($this->start_date, $this->end_date);
    }

    /**
     * Get the active financial year for a given date.
     */
    public static function forDate($date)
    {
        $date = \Carbon\Carbon::parse($date);
        return self::where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->first();
    }
}
