<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankRatingRow extends Model
{
    public const TYPE_TOTAL = 'total';

    public const TYPE_GROUP = 'group';

    public const TYPE_BANK = 'bank';

    public const GROUP_STATE = 'state';

    public const GROUP_OTHER = 'other';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'snapshot_id',
        'sort_order',
        'row_type',
        'position',
        'name',
        'assets',
        'loans',
        'capital',
        'deposits',
        'group_key',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'position' => 'integer',
            'assets' => 'integer',
            'loans' => 'integer',
            'capital' => 'integer',
            'deposits' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<BankRatingSnapshot, $this>
     */
    public function snapshot(): BelongsTo
    {
        return $this->belongsTo(BankRatingSnapshot::class, 'snapshot_id');
    }

    public function isEmphasized(): bool
    {
        return in_array($this->row_type, [self::TYPE_TOTAL, self::TYPE_GROUP], true);
    }

    public function formatMetric(?int $value): string
    {
        if ($value === null) {
            return '';
        }

        return number_format($value, 0, ',', "\u{00A0}");
    }
}
