<?php

namespace App\Models;

use App\Enums\ParseRunStatus;
use Illuminate\Database\Eloquent\Model;

class GoldParseRun extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'status',
        'ok_count',
        'fail_count',
        'error_summary',
        'details',
        'started_at',
        'finished_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ParseRunStatus::class,
            'ok_count' => 'integer',
            'fail_count' => 'integer',
            'details' => 'array',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }
}
