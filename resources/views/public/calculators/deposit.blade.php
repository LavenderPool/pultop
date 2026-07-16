@extends('layouts.public')

@section('title', $title.' - '.config('app.name', 'Pultop'))

@section('content')
    <x-public.calculators.page-shell :title="$title" :intro="$intro">
        <x-public.calculators.deposit />
    </x-public.calculators.page-shell>
@endsection

@push('scripts')
    <x-public.calculators.scripts />
@endpush
