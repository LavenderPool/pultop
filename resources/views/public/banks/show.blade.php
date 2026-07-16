@extends('layouts.public')

@section('title', $title.' - '.config('app.name', 'Pultop'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/banks.css') }}">
@endpush

@section('content')
<div class="page-title content-left solid-bg page-title-responsive-enabled">
    <div class="wf-wrap">
        <div class="page-title-head hgroup"><h1 class="entry-title">{{ $bank->name }}</h1></div>
        <div class="page-title-breadcrumbs">
            <div class="assistive-text">You are here:</div>
            <ol class="breadcrumbs text-small" itemscope itemtype="https://schema.org/BreadcrumbList">
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a itemprop="item" href="{{ url('/') }}" title="Home"><span itemprop="name">Home</span></a>
                    <meta itemprop="position" content="1" />
                </li>
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a itemprop="item" href="{{ route('banks.index') }}" title="Банки"><span itemprop="name">Банки</span></a>
                    <meta itemprop="position" content="2" />
                </li>
                <li class="current" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <span itemprop="name">{{ $bank->name }}</span>
                    <meta itemprop="position" content="3" />
                </li>
            </ol>
        </div>
    </div>
</div>

<div id="main" class="sidebar-right sidebar-divider-vertical">
    <div class="main-gradient"></div>
    <div class="wf-wrap">
        <div class="wf-container-main">
            <div id="content" class="content" role="main">
                <style>
                    .bank-header { text-align: end; }
                </style>

                <div class="bank-header">
                    <div class="small-bank-img">
                        @if ($bank->logoUrl())
                            <img src="{{ $bank->logoUrl() }}" alt="{{ $bank->name }}">
                        @endif
                    </div>
                </div>

                <h2>{{ $bank->name }}</h2>

                @if ($bank->description)
                    <p>{!! nl2br(e($bank->description)) !!}</p>
                @endif

                <div class="info-conditions">
                    <div class="base">
                        <h2>Основная информация о банке</h2>
                        <div>
                            <ul class="item-detail" style="margin-left: 0;">
                                @if ($bank->address)
                                    <li class="item-detail-item">
                                        <span class="item-detail-item-label">Адрес головного офиса</span>
                                        <span class="item-detail-item-value">
                                            <span class="item-detail-item-text fw-bold">{{ $bank->address }}</span>
                                        </span>
                                    </li>
                                @endif
                                @if ($bank->website)
                                    <li class="item-detail-item">
                                        <span class="item-detail-item-label">Официальный сайт</span>
                                        <span class="item-detail-item-value">
                                            <span class="item-detail-item-text fw-bold">
                                                <a href="{{ $bank->website }}" target="_blank" rel="noopener noreferrer">{{ $bank->website }}</a>
                                            </span>
                                        </span>
                                    </li>
                                @endif
                                @if ($bank->license)
                                    <li class="item-detail-item">
                                        <span class="item-detail-item-label">Лицензия</span>
                                        <span class="item-detail-item-value">
                                            <span class="item-detail-item-text fw-bold">{{ $bank->license }}</span>
                                        </span>
                                    </li>
                                @endif
                                @if ($bank->mfo)
                                    <li class="item-detail-item">
                                        <span class="item-detail-item-label">МФО</span>
                                        <span class="item-detail-item-value">
                                            <span class="item-detail-item-text fw-bold">{{ $bank->mfo }}</span>
                                        </span>
                                    </li>
                                @endif
                                @if ($bank->inn)
                                    <li class="item-detail-item">
                                        <span class="item-detail-item-label">ИНН</span>
                                        <span class="item-detail-item-value">
                                            <span class="item-detail-item-text fw-bold">{{ $bank->inn }}</span>
                                        </span>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <x-public.sidebar />
        </div>
    </div>
</div>
@endsection
