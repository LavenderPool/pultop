@extends('layouts.public')

@section('title', $title)

@section('content')
    <x-public.calculators.page-shell :title="$h1" :intro="$intro">
        <x-public.calculators.deposit />
    </x-public.calculators.page-shell>
@endsection

@push('scripts')
    <x-public.calculators.scripts />
@endpush
