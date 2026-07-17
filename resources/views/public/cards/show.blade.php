@extends('layouts.public')

@section('title', $title.' - '.config('app.name', 'Pultop'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/banks.css') }}">
@endpush

@section('content')
<div class="page-title content-left solid-bg page-title-responsive-enabled">
    <div class="wf-wrap">
        <div class="page-title-head hgroup"><h1 class="entry-title">{{ $card->title }}</h1></div>
        <div class="page-title-breadcrumbs">
            <div class="assistive-text">You are here:</div>
            <ol class="breadcrumbs text-small" itemscope itemtype="https://schema.org/BreadcrumbList">
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a itemprop="item" href="{{ url('/') }}" title="Home"><span itemprop="name">Home</span></a>
                    <meta itemprop="position" content="1" />
                </li>
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a itemprop="item" href="{{ route('cards.index') }}" title="Карты"><span itemprop="name">Карты</span></a>
                    <meta itemprop="position" content="2" />
                </li>
                <li class="current" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <span itemprop="name">{{ \Illuminate\Support\Str::limit($card->title, 40) }}</span>
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
                <div class="card-header">
                    <div>
                        @if ($card->bank)
                            <h2 style="margin-bottom: 0.25rem;">
                                <a href="{{ route('banks.show', $card->bank) }}">{{ $card->bank->name }}</a>
                            </h2>
                        @endif
                        <p>
                            карта: <strong>{{ $card->title }}</strong>
                        </p>
                    </div>
                    @if ($card->bank?->logoUrl())
                        <div>
                            <img src="{{ $card->bank->logoUrl() }}" width="50" height="50"
                                alt="лого банка: {{ $card->bank->name }}">
                        </div>
                    @endif
                </div>

                @if ($card->imageUrl())
                    <div class="card-image" style="margin: 1rem 0;">
                        <img src="{{ $card->imageUrl() }}" alt="{{ $card->title }}" style="max-width: 320px; height: auto;">
                    </div>
                @endif

                <div class="info-conditions">
                    <div class="base">
                        <h2>Базовые условия</h2>
                        <div>
                            <ul class="item-detail" style="margin-left: 0;">
                                @foreach ($card->conditions as $condition)
                                    <li class="item-detail-item">
                                        <span class="item-detail-item-label">{{ $condition->label }}</span>
                                        <span class="item-detail-item-value">
                                            <span class="item-detail-item-text fw-bold">{{ $condition->value }}</span>
                                            @if ($condition->note)
                                                <div><small>{{ $condition->note }}</small></div>
                                            @endif
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @if ($card->special_conditions)
                        <div class="special" style="overflow: auto;">
                            <h2>Особые условия</h2>
                            <div>{!! $card->special_conditions !!}</div>
                        </div>
                    @endif
                </div>

                <div class="info-footer">
                    @if ($card->bank)
                        <div class="info-btn">
                            <a class="info-link-btn" href="{{ route('banks.show', $card->bank) }}">Карточка банка </a>
                        </div>
                    @endif
                    @if ($card->apply_url)
                        <div class="info-btn">
                            <a class="info-link-btn" href="{{ $card->apply_url }}" target="_blank" rel="noopener noreferrer">
                                Открыть карту на сайте банка
                            </a>
                        </div>
                    @endif
                </div>

                @if ($otherCards->isNotEmpty())
                    <div class="info-more">
                        <h3>Другие карты банка</h3>
                        <table>
                            <tbody>
                                <tr>
                                    <th>Название</th>
                                    <th>Валюта</th>
                                    <th>Система</th>
                                    <th>Тип</th>
                                </tr>
                                @foreach ($otherCards as $other)
                                    <tr>
                                        <td>
                                            <a href="{{ route('cards.show', $other) }}">{{ $other->title }}</a>
                                        </td>
                                        <td>{{ strtoupper((string) $other->currency) === 'SUM' ? 'UZS' : strtoupper((string) $other->currency) }}</td>
                                        <td>{{ $other->payment_system }}</td>
                                        <td>{{ $other->card_type?->label() }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <x-public.sidebar />
        </div>
    </div>
</div>
@endsection
