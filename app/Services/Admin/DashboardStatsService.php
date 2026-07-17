<?php

namespace App\Services\Admin;

use App\Models\Article;
use App\Models\GoldParseRun;
use App\Models\RateParseRun;
use App\Services\BankService;
use App\Services\CardService;
use App\Services\CreditService;
use App\Services\DepositService;

class DashboardStatsService
{
    public function __construct(
        private readonly BankService $banks,
        private readonly CreditService $credits,
        private readonly DepositService $deposits,
        private readonly CardService $cards,
    ) {}

    /**
     * @return array{
     *     banks_active: int,
     *     credits_active: int,
     *     deposits_active: int,
     *     cards_active: int,
     *     articles_published: int,
     *     articles_draft: int,
     *     rates_parse: array{status: string, status_label: string, ok_count: int, fail_count: int, finished_at: string|null}|null,
     *     gold_parse: array{status: string, status_label: string, ok_count: int, fail_count: int, finished_at: string|null}|null
     * }
     */
    public function stats(): array
    {
        return [
            'banks_active' => $this->banks->activeCount(),
            'credits_active' => $this->credits->activeCount(),
            'deposits_active' => $this->deposits->activeCount(),
            'cards_active' => $this->cards->activeCount(),
            'articles_published' => Article::query()->published()->count(),
            'articles_draft' => Article::query()->where('is_published', false)->count(),
            'rates_parse' => $this->mapParseRun(
                RateParseRun::query()->latest('id')->first(),
            ),
            'gold_parse' => $this->mapParseRun(
                GoldParseRun::query()->latest('id')->first(),
            ),
        ];
    }

    /**
     * @return array{status: string, status_label: string, ok_count: int, fail_count: int, finished_at: string|null}|null
     */
    private function mapParseRun(RateParseRun|GoldParseRun|null $run): ?array
    {
        if ($run === null) {
            return null;
        }

        return [
            'status' => $run->status->value,
            'status_label' => $run->status->label(),
            'ok_count' => $run->ok_count,
            'fail_count' => $run->fail_count,
            'finished_at' => $run->finished_at?->format('d.m.Y H:i'),
        ];
    }
}
