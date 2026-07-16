<?php

namespace App\Services;

use App\Models\Bank;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BankService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data, ?UploadedFile $logo = null): Bank
    {
        return DB::transaction(function () use ($data, $logo) {
            $data = $this->normalize($data);

            if ($logo !== null) {
                $data['logo_path'] = $logo->store('banks', 'public');
            }

            return Bank::query()->create($data);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Bank $bank, array $data, ?UploadedFile $logo = null): Bank
    {
        return DB::transaction(function () use ($bank, $data, $logo) {
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
    }

    public function delete(Bank $bank): void
    {
        DB::transaction(function () use ($bank): void {
            if ($bank->logo_path) {
                Storage::disk('public')->delete($bank->logo_path);
            }

            $bank->delete();
        });
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
