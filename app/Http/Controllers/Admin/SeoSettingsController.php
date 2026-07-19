<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSeoSettingsRequest;
use App\Services\SeoService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SeoSettingsController extends Controller
{
    public function __construct(
        private readonly SeoService $seo,
    ) {}

    public function edit(): Response
    {
        return Inertia::render('admin/settings/seo', [
            'pages' => $this->seo->pagesForAdmin(),
        ]);
    }

    public function update(UpdateSeoSettingsRequest $request): RedirectResponse
    {
        /** @var list<array{key: string, title?: ?string, description?: ?string, keywords?: ?string, h1?: ?string}> $pages */
        $pages = $request->validated('pages');

        $this->seo->updateMany($pages);

        return redirect()
            ->route('admin.settings.seo.edit')
            ->with('success', 'SEO-настройки сохранены.');
    }
}
