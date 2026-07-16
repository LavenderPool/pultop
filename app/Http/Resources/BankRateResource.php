<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin array<string, mixed>
 */
class BankRateResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var array<string, mixed> $data */
        $data = $this->resource;

        return [
            'bank_id' => $data['bank_id'],
            'bank_name' => $data['bank_name'],
            'bank_slug' => $data['bank_slug'],
            'logo_url' => $data['logo_url'],
            'website' => $data['website'],
            'rate' => $data['rate'],
            'buy' => $data['buy'],
            'sell' => $data['sell'],
            'operation' => $data['operation'],
            'place' => $data['place'],
            'fetched_at' => $data['fetched_at'],
            'fetched_at_iso' => $data['fetched_at_iso'],
        ];
    }
}
