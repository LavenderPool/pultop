<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\SeoService;
use Illuminate\View\View;

class CalculatorController extends Controller
{
    public function __construct(
        private readonly SeoService $seo,
    ) {}

    public function credit(): View
    {
        return $this->render('public.calculators.credit', 'calculators.credit', 'Кредитный калькулятор');
    }

    public function deposit(): View
    {
        return $this->render(
            'public.calculators.deposit',
            'calculators.deposit',
            'Калькулятор Вкладов',
            ['intro' => 'Рассчитайте ваш доход по вкладу (депозиту) в банках Узбекистана.'],
        );
    }

    public function mortgage(): View
    {
        return $this->render('public.calculators.mortgage', 'calculators.mortgage', 'Калькулятор Ипотеки');
    }

    public function autoloan(): View
    {
        return $this->render(
            'public.calculators.autoloan',
            'calculators.autoloan',
            'Калькулятор Автокредита',
            ['intro' => 'Произведите точный расчет автокредита с нашим калькулятором.'],
        );
    }

    public function vat(): View
    {
        return $this->render('public.calculators.vat', 'calculators.vat', 'Калькулятор НДС');
    }

    public function monthly(): View
    {
        return $this->render(
            'public.calculators.monthly',
            'calculators.monthly',
            'Расчет ежемесячного платежа по кредиту',
        );
    }

    /**
     * @param  array<string, mixed>  $extra
     */
    private function render(string $view, string $seoKey, string $fallbackTitle, array $extra = []): View
    {
        $seo = $this->seo->resolve($seoKey, $fallbackTitle);

        return view($view, array_merge($extra, [
            'title' => $seo['title'],
            'h1' => $seo['h1'],
            'metaDescription' => $seo['metaDescription'],
            'metaKeywords' => $seo['metaKeywords'],
        ]));
    }
}
