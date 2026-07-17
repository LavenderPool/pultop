<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditRateRow extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'credit_id',
        'rate',
        'term',
        'note',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'credit_id' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Credit, $this>
     */
    public function credit(): BelongsTo
    {
        return $this->belongsTo(Credit::class);
    }
}
