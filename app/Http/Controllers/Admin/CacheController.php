<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PublicCacheService;
use Illuminate\Http\RedirectResponse;

class CacheController extends Controller
{
    public function clear(PublicCacheService $cache): RedirectResponse
    {
        $cache->flushAll();

        return back()->with('success', 'Кеш очищен.');
    }
}
