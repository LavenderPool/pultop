<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoldPrice extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'weight_grams',
        'sell_price',
        'buyback_good',
        'buyback_damaged',
        'diff',
        'priced_on',
        'fetched_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'weight_grams' => 'integer',
            'sell_price' => 'decimal:2',
            'buyback_good' => 'decimal:2',
            'buyback_damaged' => 'decimal:2',
            'diff' => 'decimal:2',
            'priced_on' => 'date',
            'fetched_at' => 'datetime',
        ];
    }
}
