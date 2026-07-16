<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Currency extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'name_ru',
        'flag',
        'is_active',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    /**
     * @param  Builder<Currency>  $query
     * @return Builder<Currency>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<Currency>  $query
     * @return Builder<Currency>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('code');
    }

    /**
     * @return HasMany<BankRate, $this>
     */
    public function bankRates(): HasMany
    {
        return $this->hasMany(BankRate::class);
    }

    /**
     * @return HasOne<CbuRate, $this>
     */
    public function cbuRate(): HasOne
    {
        return $this->hasOne(CbuRate::class);
    }

    /**
     * @return HasMany<CbuRateHistory, $this>
     */
    public function cbuRateHistories(): HasMany
    {
        return $this->hasMany(CbuRateHistory::class);
    }
}
