<?php

namespace App\Http\Controllers\Front;

use App\Enums\RateOperation;
use App\Enums\RatePlace;
use App\Http\Controllers\Controller;
use App\Services\Rates\ExchangeRateQueryService;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExchangeRateController extends Controller
{
    public function __construct(
        private readonly ExchangeRateQueryService $rates,
        private readonly SeoService $seo,
    ) {}

    public function index(): View
    {
        $currencies = $this->rates->activeCurrencies()->map(fn ($currency) => [
            'code' => strtolower($currency->code),
            'code_upper' => $currency->code,
            'name_ru' => $currency->name_ru,
            'flag' => $currency->flag,
            'cbu_rate' => $currency->cbuRate?->rate,
            'cbu_diff' => $currency->cbuRate?->diff,
            'url' => route('exchange-rates.show', ['currency' => strtolower($currency->code)]),
        ]);

        $seo = $this->seo->resolve('exchange-rates.index', 'Курс валют в банках Узбекистана');

        return view('public.exchange-rates.index', [
            'currencies' => $currencies,
            'title' => $seo['title'],
            'h1' => $seo['h1'],
            'metaDescription' => $seo['metaDescription'],
            'metaKeywords' => $seo['metaKeywords'],
        ]);
    }

    public function show(Request $request, string $currency): View
    {
        $model = $this->rates->findCurrency($currency);

        if ($model === null) {
            abort(404);
        }

        $operation = RateOperation::tryFrom($request->query('operation', 'buy')) ?? RateOperation::Buy;
        $place = RatePlace::tryFrom($request->query('place', 'cash')) ?? RatePlace::Cash;

        $currencies = $this->rates->activeCurrencies()->map(fn ($item) => [
            'code' => strtolower($item->code),
            'code_upper' => $item->code,
            'name_ru' => $item->name_ru,
            'flag' => $item->flag,
            'cbu_rate' => $item->cbuRate?->rate,
            'cbu_diff' => $item->cbuRate?->diff,
            'url' => route('exchange-rates.show', ['currency' => strtolower($item->code)]),
            'is_current' => strcasecmp($item->code, $model->code) === 0,
        ]);

        $seo = $this->seo->resolve(
            'exchange-rates.show.'.strtolower($model->code),
            $model->name_ru,
        );

        return view('public.exchange-rates.show', [
            'currency' => [
                'code' => strtolower($model->code),
                'code_upper' => $model->code,
                'name_ru' => $model->name_ru,
                'flag' => $model->flag,
                'cbu_rate' => $model->cbuRate?->rate,
                'cbu_diff' => $model->cbuRate?->diff,
                'cbu_date' => $model->cbuRate?->rate_date?->format('d.m.Y'),
            ],
            'currencies' => $currencies,
            'operation' => $operation->value,
            'place' => $place->value,
            'best' => $this->rates->bestRates($model),
            'rates' => $this->rates->bankRatesFor($model, $operation, $place),
            'history' => $this->rates->cbuHistory($model, 30),
            'places' => collect(RatePlace::cases())->map(fn (RatePlace $p) => [
                'value' => $p->value,
                'label' => $p->label(),
            ]),
            'operations' => collect(RateOperation::cases())->map(fn (RateOperation $o) => [
                'value' => $o->value,
                'label' => $o->label(),
            ]),
            'apiUrl' => route('api.rates'),
            'title' => $seo['title'],
            'h1' => $seo['h1'],
            'metaDescription' => $seo['metaDescription'],
            'metaKeywords' => $seo['metaKeywords'],
        ]);
    }
}
