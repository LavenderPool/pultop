<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class CalculatorController extends Controller
{
    public function credit(): View
    {
        return view('public.calculators.credit', [
            'title' => 'Кредитный калькулятор',
        ]);
    }

    public function deposit(): View
    {
        return view('public.calculators.deposit', [
            'title' => 'Калькулятор Вкладов',
            'intro' => 'Рассчитайте ваш доход по вкладу (депозиту) в банках Узбекистана.',
        ]);
    }

    public function mortgage(): View
    {
        return view('public.calculators.mortgage', [
            'title' => 'Калькулятор Ипотеки',
        ]);
    }

    public function autoloan(): View
    {
        return view('public.calculators.autoloan', [
            'title' => 'Калькулятор Автокредита',
            'intro' => 'Произведите точный расчет автокредита с нашим калькулятором.',
        ]);
    }

    public function vat(): View
    {
        return view('public.calculators.vat', [
            'title' => 'Калькулятор НДС',
        ]);
    }

    public function monthly(): View
    {
        return view('public.calculators.monthly', [
            'title' => 'Расчет ежемесячного платежа по кредиту',
        ]);
    }
}
