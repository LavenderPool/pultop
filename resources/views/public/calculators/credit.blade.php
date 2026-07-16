@extends('layouts.public')

@section('title', $title.' - '.config('app.name', 'Pultop'))

@section('content')
    <x-public.calculators.page-shell :title="$title">
        <x-public.calculators.loan
            heading="Быстрый расчет кредита"
            amount-label="Сумма кредита:"
            credit="10000"
            firstpay="1000"
            percent="10"
            button-color="#ffffff"
        />
    </x-public.calculators.page-shell>
@endsection

@push('scripts')
    <x-public.calculators.scripts />
@endpush
