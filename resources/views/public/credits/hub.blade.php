@extends('layouts.public')

@section('title', $title)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/banks.css') }}">
    <link rel="stylesheet" href="{{ asset('css/credits.css') }}">
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
                    <span itemprop="name">Кредиты</span>
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
                <div class="credits-intro">
                    <h3>Все банковские кредиты Узбекистана в одном месте.</h3>
                    <p>
                        Подбор кредита дело ответственное. У каждого банка свои условия и свои требования к заёмщику.
                        Чтобы изучить их все, раньше, Вы бы потратили много времени и трафика. Но теперь всё изменилось!
                        Мы собрали для вас, и удобно сгруппировали, все кредитные предложения наших банков.
                        Наша кредитная база регулярно обновляется, а механизмы подбора кредитов совершенствуются.
                    </p>
                    <p>
                        Кроме того, если у Вас возникли сложности с подбором кредита, то наши специалисты готовы
                        ответить на ваши вопросы и помочь подготовить заявку на кредит.
                        Всё что Вам нужно — это просто заполнить форму.
                    </p>
                </div>

                <div class="wpb_wrapper">
                    <div class="capability">
                        <div class="capability-panel">
                            <div class="capability-content">
                                @foreach ($types as $type)
                                    <div class="capability_row">
                                        <a class="capability-link" href="{{ route('credits.type', $type->slug) }}">
                                            <div class="capability-icon">
                                                <x-public.credit-type-icon :slug="$type->slug" />
                                            </div>
                                            <div class="capability-title">
                                                {{ $type->name }}
                                                <div style="font-weight: 100; color: #000000; text-transform: none;">
                                                    <small>
                                                        Всего предложений:
                                                        <strong>{{ $type->active_credits_count }}</strong>
                                                    </small>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                @if ($topCredits->isNotEmpty())
                    <h2 class="credits-top-title">TOP-10 АВТОКРЕДИТОВ</h2>
                    <section id="content-data" class="items-list credits" items-type="credit">
                        @include('public.credits.partials.cards', [
                            'credits' => $topCredits,
                            'startIndex' => 0,
                        ])
                    </section>
                @endif

                <p class="credits-disclaimer">
                    Информация о ставках и условиях взята из открытых источников.
                    Пожалуйста, уточняйте условия продуктов в отделениях банков.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
