@extends('layouts.public')

@section('title', $title.' - '.config('app.name', 'Pultop'))

@section('content')
    <x-public.calculators.page-shell :title="$title">
        <x-public.calculators.vat />
    </x-public.calculators.page-shell>
@endsection

@push('scripts')
    <x-public.calculators.scripts />
@endpush
