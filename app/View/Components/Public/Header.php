<?php

namespace App\View\Components\Public;

use App\Services\SettingService;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Header extends Component
{
    /**
     * @var list<array{key: string, url: string, class: string, title: string}>
     */
    public array $socialLinks;

    public function __construct(SettingService $settings)
    {
        $this->socialLinks = $settings->socialLinks();
    }

    public function render(): View
    {
        return view('components.public.header');
    }
}
