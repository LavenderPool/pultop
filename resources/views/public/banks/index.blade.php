@extends('layouts.public')

@section('title', $title)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/banks.css') }}">
@endpush

@section('content')
<div class="page-title content-left solid-bg page-title-responsive-enabled">
    <div class="wf-wrap">
        <div class="page-title-head hgroup"><h1 class="entry-title">{{ $h1 }}</h1></div>
        <div class="page-title-breadcrumbs">
            <div class="assistive-text">You are here:</div>
            <ol class="breadcrumbs text-small" itemscope itemtype="https://schema.org/BreadcrumbList">
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a itemprop="item" href="{{ url('/') }}" title="Home"><span itemprop="name">Home</span></a>
                    <meta itemprop="position" content="1" />
                </li>
                <li class="current" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <span itemprop="name">Банки</span>
                    <meta itemprop="position" content="2" />
                </li>
            </ol>
        </div>
    </div>
</div>

<div id="main" class="sidebar-none sidebar-divider-vertical">
    <div class="main-gradient"></div>
    <div class="wf-wrap">
        <div class="wf-container-main">
            <div id="content" class="content" role="main">
                <section id="content-data" class="items-list banks" items-type="bank">
                    @forelse ($banks as $index => $bank)
                        <div class="item-content credit v-anim-fadein" style="--delay: {{ $index * 50 }}ms; gap: 25px;">
                            <div class="bank-image" style="width: 150px;">
                                @if ($bank->logoUrl())
                                    <img src="{{ $bank->logoUrl() }}" alt="{{ $bank->name }}" width="150">
                                @endif
                            </div>
                            <div style="flex: 1;">
                                <h3 style="font-weight: bold; margin-bottom: 0;">{{ $bank->name }}</h3>
                                @if ($bank->address)
                                    <p style="color: #a5a5a5;">{{ $bank->address }}</p>
                                @endif
                            </div>
                            <div class="btn-more" style="text-align: center;">
                                <a class="link" href="{{ route('banks.show', $bank) }}">Детали</a>
                            </div>
                        </div>
                    @empty
                        <p>Банки пока не добавлены.</p>
                    @endforelse
                </section>
            </div>
        </div>
    </div>
</div>
@endsection
