<?php

namespace App\Models;

use App\Enums\RatePlace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankRate extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'bank_id',
        'currency_id',
        'place',
        'buy',
        'sell',
        'fetched_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'place' => RatePlace::class,
            'buy' => 'decimal:4',
            'sell' => 'decimal:4',
            'fetched_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Bank, $this>
     */
    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    /**
     * @return BelongsTo<Currency, $this>
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}
