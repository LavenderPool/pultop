@extends('layouts.public')

@section('title', $title.' - '.config('app.name', 'Pultop'))

@section('content')
    <x-public.calculators.page-shell :title="$title">
        <x-public.calculators.loan
            heading="Рассчет Ипотеки"
            amount-label="Стоимость жилья:"
            credit="250000000"
            firstpay="1000"
            percent="17"
            :slider="['min' => 150000000, 'max' => 800000000, 'value' => 250000000]"
            button-color="#dd9933"
            input-border="#dd9933"
            :show-reset-bg="false"
        />
    </x-public.calculators.page-shell>
@endsection

@push('scripts')
    <x-public.calculators.scripts />
@endpush
