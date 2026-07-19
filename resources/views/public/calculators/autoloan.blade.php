@extends('layouts.public')

@section('title', $title)

@section('content')
    <x-public.calculators.page-shell :title="$h1" :intro="$intro">
        <x-public.calculators.loan
            heading="Расчет кредита на приобретение автомобиля"
            amount-label="Цена автомобиля:"
            credit="50000000"
            firstpay="10000000"
            percent="30"
            :slider="['min' => 50000000, 'max' => 250000000, 'value' => 50000000]"
            button-color="#cecece"
            input-border="inherit"
        />
    </x-public.calculators.page-shell>
@endsection

@push('scripts')
    <x-public.calculators.scripts />
@endpush
