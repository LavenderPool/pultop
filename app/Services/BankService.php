<?php

namespace App\Services;

use App\Models\Bank;
use App\Models\BankRatingSnapshot;
use App\Models\Card;
use App\Models\Credit;
use App\Models\Deposit;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BankService
{
    public function __construct(
        private readonly PublicCacheService $cache,
    ) {}

    public function activeCount(): int
    {
        return $this->cache->remember(
            PublicCacheService::GROUP_BANKS,
            'active_count',
            fn () => Bank::query()->active()->count(),
        );
    }

    /**
     * @return Collection<int, Bank>
     */
    public function listActive(): Collection
    {
        return $this->cache->remember(
            PublicCacheService::GROUP_BANKS,
            'list_active',
            fn () => Bank::query()
                ->active()
                ->ordered()
                ->get(),
        );
    }

    /**
     * @return Collection<int, Bank>
     */
    public function listActiveOptions(): Collection
    {
        return $this->cache->remember(
            PublicCacheService::GROUP_BANKS,
            'list_active_options',
            fn () => Bank::query()
                ->active()
                ->ordered()
                ->get(['id', 'name', 'slug']),
        );
    }

    public function currentRatingSnapshot(): ?BankRatingSnapshot
    {
        return $this->cache->remember(
            PublicCacheService::GROUP_BANKS,
            'current_rating',
            fn () => BankRatingSnapshot::query()
                ->current()
                ->with('rows')
                ->first(),
        );
    }

    /**
     * @return array{
     *     credits: Collection<int, Credit>,
     *     cards: Collection<int, Card>,
     *     deposits: Collection<int, Deposit>,
     * }
     */
    public function productsForBank(Bank $bank, int $limit = 10): array
    {
        return [
            'credits' => Credit::query()
                ->active()
                ->ordered()
                ->where('bank_id', $bank->id)
                ->limit($limit)
                ->get(),
            'cards' => Card::query()
                ->active()
                ->ordered()
                ->where('bank_id', $bank->id)
                ->limit($limit)
                ->get(),
            'deposits' => Deposit::query()
                ->active()
                ->ordered()
                ->where('bank_id', $bank->id)
                ->limit($limit)
                ->get(),
        ];
    }

    /**
     * Map of mb_strtolower(name|alias) => Bank for matching rating row names.
     *
     * @return array<string, Bank>
     */
    public function activeBanksByNormalizedName(): array
    {
        $banks = $this->listActive();
        $map = [];
        $bySlug = [];

        foreach ($banks as $bank) {
            $bySlug[$bank->slug] = $bank;
            $key = mb_strtolower(trim((string) $bank->name));
            if ($key !== '') {
                $map[$key] = $bank;
            }
        }

        /** @var array<string, string> $aliases */
        $aliases = config('banks.rating_aliases', []);
        foreach ($aliases as $alias => $slug) {
            $bank = $bySlug[$slug] ?? null;
            if ($bank === null) {
                continue;
            }
            $key = mb_strtolower(trim((string) $alias));
            if ($key !== '') {
                $map[$key] = $bank;
            }
        }

        return $map;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data, ?UploadedFile $logo = null): Bank
    {
        $bank = DB::transaction(function () use ($data, $logo) {
            $data = $this->normalize($data);

            if ($logo !== null) {
                $data['logo_path'] = $logo->store('banks', 'public');
            }

            return Bank::query()->create($data);
        });

        $this->cache->forgetGroup(PublicCacheService::GROUP_BANKS);

        return $bank;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Bank $bank, array $data, ?UploadedFile $logo = null): Bank
    {
        $bank = DB::transaction(function () use ($bank, $data, $logo) {
            $data = $this->normalize($data);

            if ($logo !== null) {
                if ($bank->logo_path) {
                    Storage::disk('public')->delete($bank->logo_path);
                }
                $data['logo_path'] = $logo->store('banks', 'public');
            }

            $bank->update($data);

            return $bank->fresh();
        });

        $this->cache->forgetGroup(PublicCacheService::GROUP_BANKS);

        return $bank;
    }

    public function delete(Bank $bank): void
    {
        DB::transaction(function () use ($bank): void {
            if ($bank->logo_path) {
                Storage::disk('public')->delete($bank->logo_path);
            }

            $bank->delete();
        });

        $this->cache->forgetGroup(PublicCacheService::GROUP_BANKS);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalize(array $data): array
    {
        if (empty($data['slug']) && ! empty($data['name'])) {
            $data['slug'] = Str::slug((string) $data['name']);
        }

        $data['is_active'] = filter_var($data['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['parser_code'] = filled($data['parser_code'] ?? null) ? (string) $data['parser_code'] : null;
        $data['rates_url'] = filled($data['rates_url'] ?? null) ? (string) $data['rates_url'] : null;
        $data['website'] = filled($data['website'] ?? null) ? (string) $data['website'] : null;
        $data['address'] = filled($data['address'] ?? null) ? (string) $data['address'] : null;
        $data['description'] = filled($data['description'] ?? null) ? (string) $data['description'] : null;
        $data['license'] = filled($data['license'] ?? null) ? (string) $data['license'] : null;
        $data['mfo'] = filled($data['mfo'] ?? null) ? (string) $data['mfo'] : null;
        $data['inn'] = filled($data['inn'] ?? null) ? (string) $data['inn'] : null;

        return $data;
    }
}
