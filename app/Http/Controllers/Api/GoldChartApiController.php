<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Gold\GoldQueryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GoldChartApiController extends Controller
{
    public function __construct(
        private readonly GoldQueryService $gold,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $gold = (int) $request->query('gold', 0);
        $period = (int) $request->query('period', 7);

        if ($gold < 0 || $gold > 4) {
            $gold = 0;
        }

        if (! in_array($period, [7, 30, 90], true)) {
            $period = 7;
        }

        return response()->json($this->gold->chartPayload($gold, $period));
    }
}
