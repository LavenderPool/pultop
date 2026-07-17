<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditCondition extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'credit_id',
        'label',
        'value',
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
