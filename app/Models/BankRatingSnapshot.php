<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankRatingSnapshot extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'as_of_date',
        'unit',
        'source_url',
        'parsed_at',
        'is_current',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'as_of_date' => 'date',
            'parsed_at' => 'datetime',
            'is_current' => 'boolean',
        ];
    }

    /**
     * @return HasMany<BankRatingRow, $this>
     */
    public function rows(): HasMany
    {
        return $this->hasMany(BankRatingRow::class, 'snapshot_id')->orderBy('sort_order');
    }

    /**
     * @param  Builder<BankRatingSnapshot>  $query
     * @return Builder<BankRatingSnapshot>
     */
    public function scopeCurrent(Builder $query): Builder
    {
        return $query->where('is_current', true);
    }
}
