<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Deposits\DepositQueryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepositApiController extends Controller
{
    public function __construct(
        private readonly DepositQueryService $query,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $filters = [
            'bank_id' => $request->integer('bank_id') ?: null,
            'currency' => filled($request->input('currency')) ? (string) $request->input('currency') : null,
            'srok' => filled($request->input('srok')) ? (string) $request->input('srok') : 'all',
            'summa' => filled($request->input('summa')) ? (string) $request->input('summa') : null,
            'is_online' => $request->boolean('is_online'),
        ];

        $paginator = $this->query->paginate($filters);
        $offset = ($paginator->currentPage() - 1) * $paginator->perPage();

        $html = view('public.deposits.partials.cards', [
            'deposits' => $paginator->getCollection(),
            'startIndex' => $offset,
        ])->render();

        return response()->json([
            'html' => $html,
            'page' => $paginator->currentPage(),
            'has_more' => $paginator->hasMorePages(),
            'total' => $paginator->total(),
        ]);
    }
}
