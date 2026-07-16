@extends('layouts.public')

@section('title', $title.' - '.config('app.name', 'Pultop'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/kurs/currency-selector.css') }}">
@endpush

@section('content')
<div class="page-title content-left solid-bg page-title-responsive-enabled">
			<div class="wf-wrap">

				<div class="page-title-head hgroup"><h1>{{ $title }}</h1></div><div class="page-title-breadcrumbs"><div class="assistive-text">You are here:</div><ol class="breadcrumbs text-small" itemscope itemtype="https://schema.org/BreadcrumbList"><li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><a itemprop="item" href="{{ url('/') }}" title="Home"><span itemprop="name">Home</span></a><meta itemprop="position" content="1" /></li><li class="current" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><span itemprop="name">Курс валют в банках Узбекистана</span><meta itemprop="position" content="2" /></li></ol></div>			</div>
		</div>
<div id="main" class="sidebar-right sidebar-divider-vertical">
<div class="main-gradient"></div>
<div class="wf-wrap">
<div class="wf-container-main">
<div id="content" class="content" role="main">

		<div class="wpb-content-wrapper"><div class="vc_row wpb_row vc_row-fluid"><div class="wpb_column vc_column_container vc_col-sm-12"><div class="vc_column-inner"><div class="wpb_wrapper">
	<div class="wpb_text_column wpb_content_element " >
		<div class="wpb_wrapper">
			<p>Только у нас всегда самый точный и актуальный курс валют (доллар США, евро, рубль, тенге) в банках Узбекистана. Мы постоянно отслеживаем изменение курсов доллара США, Евро и российского рубля к узбекскому суму и оперативно обновляем информацию. Выгодный курс обмена казахского тенге в кассах банков вы так же найдете на этой странице.</p>
<p>Узнавайте, сравнивайте, продавайте и приобретайте доллары, евро, рубли и тенге в Узбекистане по лучшему курсу на сегодняшний день. Адреса филиалов банков, а так же дислокация банкоматов по Узбекистану на персональных страницах <a href="{{ route('banks.index') }}">банков</a>.</p>

		</div>
	</div>
<div class="shortcode-banner shortcode-banner-link" style="min-height: 150px;background-image: url(https://pultop.uz/wp-content/uploads/2025/10/ipak-100-w-11.jpg)" onclick="window.open('https://redirect.appmetrica.yandex.com/serve/1109429524078014604?source=pultop');"><div class="shortcode-banner-bg wf-table" style="padding: 0px;min-height: 150px"><div class="shortcode-banner-inside wf-table text-small" style="border: solid 0px transparent;outline: solid 0px;height: 150px"><div></div></div></div></div><div class="vc_empty_space"   style="height: 32px"><span class="vc_empty_space_inner"></span></div>
	<div class="wpb_raw_code wpb_content_element wpb_raw_html" id="currency-selector">
		<div class="wpb_wrapper">
			


<div class="currency-selector">
    <div class="currency-header">
        <h3>Выбор валюты</h3>
    </div>
    
    <div class="currency-options">
@foreach ($currencies as $item)
        <a class="currency-item" href="{{ $item['url'] }}" data-currency="{{ $item['code_upper'] }}">
            <div class="currency-main">
                <div class="currency-info">
                    <span class="currency-flag">{{ $item['flag'] }}</span>
                    <span class="currency-name">{{ $item['name_ru'] }} ({{ $item['code_upper'] }})</span>
                </div>
                <div class="currency-arrow">
                    <span>Далее</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
                </div>
            </div>
        </a>
@endforeach
</div>
        </div>
    </div>
</div>


		</div>
	</div>
</div></div></div></div><div class="vc_row wpb_row vc_row-fluid"><div class="wpb_column vc_column_container vc_col-sm-12"><div class="vc_column-inner"><div class="wpb_wrapper"><div class="vc_btn3-container vc_btn3-center" ><a class="vc_general vc_btn3 vc_btn3-size-lg vc_btn3-shape-rounded vc_btn3-style-modern vc_btn3-icon-left vc_btn3-color-primary" href="https://t.me/joinchat/AAAAAFFzyIDtRo3-eDr2Iw" title="" target="_blank"><i class="vc_btn3-icon "></i> ПОДПИШИТЕСЬ НА КАНАЛ В ТЕЛЕГРАМ</a></div>
	<div class="wpb_text_column wpb_content_element " >
		<div class="wpb_wrapper">
			<p>Удобно отслеживайте курсы валют в банках Узбекистана на <a href="https://t.me/pul_top_uzb">нашем канале</a> в Телеграм, а так же установив наше <a href="https://play.google.com/store/apps/details?id=uz.pultop.PULTOP">мобильное приложение</a>.</p>
<h2>Всегда актуальный курс валют в банках Узбекистана только на Pultop.Uz!</h2>

		</div>
	</div>
</div></div></div></div>
</div>
	</div><!-- #content -->

	<x-public.sidebar />

</div>
</div>
</div>
@endsection
