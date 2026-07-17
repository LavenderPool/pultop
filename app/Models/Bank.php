<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Bank extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'logo_path',
        'address',
        'description',
        'website',
        'license',
        'mfo',
        'inn',
        'parser_code',
        'rates_url',
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
        return 'slug';
    }

    public function logoUrl(): ?string
    {
        if ($this->logo_path === null || $this->logo_path === '') {
            return null;
        }

        return Storage::disk('public')->url($this->logo_path);
    }

    /**
     * @param  Builder<Bank>  $query
     * @return Builder<Bank>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<Bank>  $query
     * @return Builder<Bank>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * @param  Builder<Bank>  $query
     * @return Builder<Bank>
     */
    public function scopeWithParser(Builder $query): Builder
    {
        return $query->whereNotNull('parser_code')->where('parser_code', '!=', '');
    }

    /**
     * @return HasMany<Credit, $this>
     */
    public function credits(): HasMany
    {
        return $this->hasMany(Credit::class);
    }

    /**
     * @return HasMany<Deposit, $this>
     */
    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class);
    }

    /**
     * @return HasMany<Card, $this>
     */
    public function cards(): HasMany
    {
        return $this->hasMany(Card::class);
    }

    /**
     * @return HasMany<BankRate, $this>
     */
    public function rates(): HasMany
    {
        return $this->hasMany(BankRate::class);
    }

    /**
     * @return HasMany<BankRateHistory, $this>
     */
    public function rateHistories(): HasMany
    {
        return $this->hasMany(BankRateHistory::class);
    }
}
