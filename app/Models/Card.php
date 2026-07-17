<?php

namespace App\Models;

use App\Enums\CardType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Card extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'bank_id',
        'title',
        'slug',
        'currency',
        'payment_system',
        'card_type',
        'category',
        'issue_cost_display',
        'validity_display',
        'special_conditions',
        'apply_url',
        'image_path',
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
            'card_type' => CardType::class,
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function imageUrl(): ?string
    {
        if ($this->image_path === null || $this->image_path === '') {
            return null;
        }

        return Storage::disk('public')->url($this->image_path);
    }

    /**
     * @param  Builder<Card>  $query
     * @return Builder<Card>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<Card>  $query
     * @return Builder<Card>
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
     * @return HasMany<CardCondition, $this>
     */
    public function conditions(): HasMany
    {
        return $this->hasMany(CardCondition::class)->orderBy('sort_order');
    }
}
