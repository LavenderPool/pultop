<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CbuRate extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'currency_id',
        'rate',
        'diff',
        'rate_date',
        'fetched_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rate' => 'decimal:4',
            'diff' => 'decimal:4',
            'rate_date' => 'date',
            'fetched_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Currency, $this>
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}
