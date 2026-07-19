<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Services\CardService;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CardController extends Controller
{
    public function __construct(
        private readonly CardService $cards,
        private readonly SeoService $seo,
    ) {}

    public function index(Request $request): View
    {
        $filters = [
            'bank_id' => $request->integer('bank_id') ?: null,
            'currency' => $request->string('currency')->toString() ?: null,
            'payment_system' => $request->string('payment_system')->toString() ?: null,
            'card_type' => $request->string('card_type')->toString() ?: null,
        ];

        if (($filters['currency'] ?? null) === 'all' || ($filters['currency'] ?? null) === '') {
            $filters['currency'] = null;
        }
        if (($filters['payment_system'] ?? null) === 'all' || ($filters['payment_system'] ?? null) === '') {
            $filters['payment_system'] = null;
        }
        if (($filters['card_type'] ?? null) === 'all' || ($filters['card_type'] ?? null) === '') {
            $filters['card_type'] = null;
        }

        $options = $this->cards->filterOptions();
        $seo = $this->seo->resolve('cards.index', 'Карты');

        return view('public.cards.index', [
            'cards' => $this->cards->listActive($filters),
            'title' => $seo['title'],
            'h1' => $seo['h1'],
            'metaDescription' => $seo['metaDescription'],
            'metaKeywords' => $seo['metaKeywords'],
            'banks' => $options['banks'],
            'cardTypes' => $options['card_types'],
            'paymentSystems' => $options['payment_systems'],
            'currencies' => $options['currencies'],
            'filters' => $filters,
        ]);
    }

    public function show(Card $card): View
    {
        if (! $card->is_active) {
            abort(404);
        }

        $card->load(['bank', 'conditions']);

        return view('public.cards.show', [
            'card' => $card,
            'title' => $card->title,
            'otherCards' => $this->cards->otherCardsOfBank($card),
        ]);
    }
}
