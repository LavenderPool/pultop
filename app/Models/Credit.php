<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Credit extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'bank_id',
        'title',
        'slug',
        'currency',
        'rate_display',
        'term_display',
        'amount_display',
        'term_min_months',
        'term_max_months',
        'amount_min',
        'amount_max',
        'down_payment',
        'special_conditions',
        'apply_url',
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
            'bank_id' => 'integer',
            'term_min_months' => 'integer',
            'term_max_months' => 'integer',
            'amount_min' => 'integer',
            'amount_max' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @param  Builder<Credit>  $query
     * @return Builder<Credit>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<Credit>  $query
     * @return Builder<Credit>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('title');
    }

    /**
     * @return BelongsTo<Bank, $this>
     */
    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    /**
     * @return BelongsToMany<CreditType, $this>
     */
    public function types(): BelongsToMany
    {
        return $this->belongsToMany(CreditType::class);
    }

    /**
     * @return HasMany<CreditRateRow, $this>
     */
    public function rateRows(): HasMany
    {
        return $this->hasMany(CreditRateRow::class)->orderBy('sort_order');
    }

    /**
     * @return HasMany<CreditCondition, $this>
     */
    public function conditions(): HasMany
    {
        return $this->hasMany(CreditCondition::class)->orderBy('sort_order');
    }
}
