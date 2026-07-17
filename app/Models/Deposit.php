<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Deposit extends Model
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
        'early_termination',
        'partial_withdrawal',
        'capitalization',
        'is_online',
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
            'early_termination' => 'boolean',
            'partial_withdrawal' => 'boolean',
            'capitalization' => 'boolean',
            'is_online' => 'boolean',
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
     * @param  Builder<Deposit>  $query
     * @return Builder<Deposit>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<Deposit>  $query
     * @return Builder<Deposit>
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
     * @return HasMany<DepositRate, $this>
     */
    public function rates(): HasMany
    {
        return $this->hasMany(DepositRate::class)->orderBy('sort_order');
    }

    /**
     * @return HasMany<DepositCondition, $this>
     */
    public function conditions(): HasMany
    {
        return $this->hasMany(DepositCondition::class)->orderBy('sort_order');
    }
}
