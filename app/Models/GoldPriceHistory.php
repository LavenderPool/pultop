<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoldPriceHistory extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'weight_grams',
        'price',
        'diff',
        'price_date',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'weight_grams' => 'integer',
            'price' => 'decimal:2',
            'diff' => 'decimal:2',
            'price_date' => 'date',
        ];
    }
}
