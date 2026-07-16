@extends('layouts.public')

@section('title', $title.' - '.config('app.name', 'Pultop'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/bank-rating.css') }}">
@endpush

@section('content')
<div class="page-title content-left solid-bg page-title-responsive-enabled">
    <div class="wf-wrap">
        <div class="page-title-head hgroup"><h1>{{ $title }}</h1></div>
        <div class="page-title-breadcrumbs">
            <div class="assistive-text">You are here:</div>
            <ol class="breadcrumbs text-small" itemscope itemtype="https://schema.org/BreadcrumbList">
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a itemprop="item" href="{{ url('/') }}" title="Home"><span itemprop="name">Home</span></a>
                    <meta itemprop="position" content="1" />
                </li>
                <li class="current" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <span itemprop="name">{{ $title }}</span>
                    <meta itemprop="position" content="2" />
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
                @if ($snapshot)
                    @php
                        $asOfLabel = $snapshot->as_of_date
                            ? $snapshot->as_of_date->locale('ru')->translatedFormat('j F Y')
                            : null;
                    @endphp
                    <table class="bank-rating-table">
                        <tbody>
                            <tr>
                                <td colspan="6" class="bank-rating-title">
                                    Сведения об основных показателях коммерческих банков
                                    @if ($asOfLabel)
                                        <br>по состоянию на {{ $asOfLabel }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6" class="bank-rating-unit">
                                    <pre>Единица измерения: {{ $snapshot->unit }}</pre>
                                </td>
                            </tr>
                            <tr>
                                <th rowspan="2" class="bank-rating-pos">№</th>
                                <th rowspan="2" class="bank-rating-name">Наименование банка</th>
                                <th class="bank-rating-metric-head">Актив</th>
                                <th class="bank-rating-metric-head">Кредит</th>
                                <th class="bank-rating-metric-head">Капитал</th>
                                <th class="bank-rating-metric-head">Депозит</th>
                            </tr>
                            <tr>
                                <th class="bank-rating-metric-head">сумма</th>
                                <th class="bank-rating-metric-head">сумма</th>
                                <th class="bank-rating-metric-head">сумма</th>
                                <th class="bank-rating-metric-head">сумма</th>
                            </tr>
                            @foreach ($snapshot->rows as $row)
                                <tr @class([
                                    'bank-rating-row-total' => $row->row_type === \App\Models\BankRatingRow::TYPE_TOTAL,
                                    'bank-rating-row-group' => $row->row_type === \App\Models\BankRatingRow::TYPE_GROUP,
                                    'bank-rating-row-bank' => $row->row_type === \App\Models\BankRatingRow::TYPE_BANK,
                                ])>
                                    @if ($row->row_type === \App\Models\BankRatingRow::TYPE_BANK)
                                        <td class="bank-rating-pos">{{ $row->position }}</td>
                                        <td class="bank-rating-name">{{ $row->name }}</td>
                                    @else
                                        <td colspan="2" class="bank-rating-name">{{ $row->name }}</td>
                                    @endif
                                    <td class="bank-rating-num">{{ $row->formatMetric($row->assets) }}</td>
                                    <td class="bank-rating-num">{{ $row->formatMetric($row->loans) }}</td>
                                    <td class="bank-rating-num">{{ $row->formatMetric($row->capital) }}</td>
                                    <td class="bank-rating-num">{{ $row->formatMetric($row->deposits) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="bank-rating-empty">Данные рейтинга пока не загружены.</p>
                @endif
            </div>

            <x-public.sidebar />
        </div>
    </div>
</div>
@endsection
