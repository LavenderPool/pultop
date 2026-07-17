<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\DashboardStatsService;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function __invoke(DashboardStatsService $stats): Response
    {
        return Inertia::render('admin/dashboard', [
            'stats' => $stats->stats(),
        ]);
    }
}
