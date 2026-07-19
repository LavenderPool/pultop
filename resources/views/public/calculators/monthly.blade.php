@extends('layouts.public')

@section('title', $title)

@section('content')
    <x-public.calculators.page-shell :title="$h1">
        <x-public.calculators.monthly-payment />
    </x-public.calculators.page-shell>
@endsection

@push('scripts')
    <x-public.calculators.scripts />
@endpush