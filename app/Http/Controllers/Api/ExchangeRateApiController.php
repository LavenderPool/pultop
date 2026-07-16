<?php

namespace App\Http\Controllers\Api;

use App\Enums\RateOperation;
use App\Enums\RatePlace;
use App\Http\Controllers\Controller;
use App\Http\Resources\BankRateResource;
use App\Services\Rates\ExchangeRateQueryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExchangeRateApiController extends Controller
{
    public function __construct(
        private readonly ExchangeRateQueryService $rates,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'currency' => ['required', 'string', 'size:3'],
            'operation' => ['nullable', 'string', 'in:buy,sell'],
            'place' => ['nullable', 'string', 'in:cash,atm,app'],
        ]);

        $currency = $this->rates->findCurrency($validated['currency']);

        if ($currency === null) {
            return response()->json([
                'message' => 'Валюта не найдена',
            ], 404);
        }

        $operation = RateOperation::from($validated['operation'] ?? RateOperation::Buy->value);
        $place = RatePlace::from($validated['place'] ?? RatePlace::Cash->value);

        $items = $this->rates->bankRatesFor($currency, $operation, $place);

        return response()->json([
            'currency' => [
                'code' => $currency->code,
                'name_ru' => $currency->name_ru,
                'flag' => $currency->flag,
                'cbu_rate' => $currency->cbuRate?->rate,
                'cbu_diff' => $currency->cbuRate?->diff,
            ],
            'operation' => $operation->value,
            'place' => $place->value,
            'best' => $this->rates->bestRates($currency),
            'data' => BankRateResource::collection(collect($items))->resolve(),
        ]);
    }
}
