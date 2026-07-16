<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateGeneralSettingsRequest;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class GeneralSettingsController extends Controller
{
    public function __construct(
        private readonly SettingService $settings,
    ) {}

    public function edit(): Response
    {
        return Inertia::render('admin/settings/general', [
            'settings' => $this->settings->generalSettings(),
        ]);
    }

    public function update(UpdateGeneralSettingsRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $this->settings->setMany([
            'social_facebook_url' => (string) ($data['social_facebook_url'] ?? ''),
            'social_x_url' => (string) ($data['social_x_url'] ?? ''),
            'social_instagram_url' => (string) ($data['social_instagram_url'] ?? ''),
            'social_youtube_url' => (string) ($data['social_youtube_url'] ?? ''),
            'social_telegram_url' => (string) ($data['social_telegram_url'] ?? ''),
        ]);

        return redirect()
            ->route('admin.settings.general.edit')
            ->with('success', 'Общие настройки сохранены.');
    }
}
