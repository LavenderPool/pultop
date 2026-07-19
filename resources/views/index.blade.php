@extends('layouts.public')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/articles.css') }}">
@endpush

@section('title', $title)

@section('body_class', 'home')

@section('content')
    <div id="main" class="sidebar-none sidebar-divider-vertical">
    <div class="wf-wrap">
            <div id="content" class="content" role="main">

                <div class="wpb-content-wrapper">
                    @if (!empty($h1))
                        <h1 class="assistive-text">{{ $h1 }}</h1>
                    @endif
                    <div
                        class="vc_row wpb_row vc_row-fluid wpb_animate_when_almost_visible wpb_bottom-to-top bottom-to-top wpb_start_animation animated">
                        <div class="wpb_column vc_column_container vc_col-sm-12">
                            <div class="vc_column-inner">
                                <div class="wpb_wrapper">
                                    <div class="wpb_raw_code wpb_content_element wpb_raw_html">
                                        <div class="wpb_wrapper">
                                            <div class="main-panel">

                                                <a class="main-link" href="{{ route('credits.all') }}">
                                                    <div class="main-body">
                                                        <div>
                                                            <div class="main-title">Подбор кредита</div>
                                                            <div class="main-subtitle">
                                                                <div>
                                                                    Зачем переплачивать по кредиту?
                                                                    <div></div>
                                                                    Выберите свой вариант
                                                                    <div></div>
                                                                    с минимальной процентной ставкой.
                                                                </div>
                                                            </div>
                                                            <div class="main-count">{{ $creditsCount }} предложений в базе</div>

                                                        </div>
                                                        <div class="main-btn">Подобрать кредит</div>
                                                    </div>
                                                    <div class="main-bg">
                                                        <picture class="main-bg-img">
                                                            <img decoding="async"
                                                                src="https://pultop.uz/wp-content/plugins/pultopuz-shortcode/img/credit.jpg"
                                                                alt="" style="width: 100%; height: 100%;">
                                                        </picture>
                                                    </div>
                                                </a>
                                                <a class="main-link" href="{{ route('deposits.index') }}">
                                                    <div class="main-body">
                                                        <div>
                                                            <div class="main-title">Подбор вклада</div>
                                                            <div class="main-subtitle">
                                                                <div>
                                                                    Хотите увеличить свой капитал?
                                                                    <div></div>
                                                                    Подберите лучший вариант
                                                                    <div></div>
                                                                    накопления денежных средств.
                                                                </div>
                                                            </div>
                                                            <div class="main-count">{{ $depositsCount }} предложений в базе</div>

                                                        </div>
                                                        <div class="main-btn">Подобрать вклад</div>
                                                    </div>
                                                    <div class="main-bg">
                                                        <picture class="main-bg-img">
                                                            <img decoding="async"
                                                                src="https://pultop.uz/wp-content/plugins/pultopuz-shortcode/img/deposit.jpg"
                                                                alt="" style="width: 100%; height: 100%;">
                                                        </picture>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="vc_row wpb_row vc_row-fluid wpb_animate_when_almost_visible wpb_bottom-to-top bottom-to-top wpb_start_animation animated">
                        <div class="wpb_column vc_column_container vc_col-sm-12">
                            <div class="vc_column-inner">
                                <div class="wpb_wrapper">
                                    <div id="ultimate-heading-78216a5949dd1352a"
                                        class="uvc-heading ult-adjust-bottom-margin ultimate-heading-78216a5949dd1352a uvc-9836 "
                                        data-hspacer="no_spacer" data-halign="center" style="text-align:center">
                                        <div class="uvc-heading-spacer no_spacer" style="top"></div>
                                        <div class="uvc-main-heading ult-responsive"
                                            data-ultimate-target=".uvc-heading.ultimate-heading-78216a5949dd1352a h2"
                                            data-responsive-json-new="{&quot;font-size&quot;:&quot;desktop:26px;&quot;,&quot;line-height&quot;:&quot;&quot;}">
                                            <h2 style="--font-weight:theme;">PULTOP.UZ - ВАШ ПРОВОДНИК НА РЫНКЕ ФИНАНСОВЫХ
                                                УСЛУГ</h2>
                                        </div>
                                    </div>
                                    <div class="wpb_raw_code wpb_content_element wpb_raw_html">
                                        <div class="wpb_wrapper">
                                            <div class="capability">
                                                <div class="capability-panel">
                                                    <div class="capability-content">
                                                        <div class="capability_row">
                                                            <a class="capability-link" href="{{ route('deposits.index') }}">
                                                                <div class="capability-icon">
                                                                    <svg stroke="currentColor" fill="currentColor"
                                                                        stroke-width="0" version="1.1" viewBox="0 0 16 16"
                                                                        height="1em" width="1em"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path
                                                                            d="M14 8h-2.5l-3.5 3.5-3.5-3.5h-2.5l-2 4v1h16v-1l-2-4zM0 14h16v1h-16v-1zM9 5v-4h-2v4h-3.5l4.5 4.5 4.5-4.5h-3.5z">
                                                                        </path>
                                                                    </svg>
                                                                </div>
                                                                <div class="capability-title">Вклады</div>
                                                            </a>
                                                        </div>
                                                        <div class="capability_row">
                                                            <a class="capability-link" href="{{ route('cards.index') }}">
                                                                <div class="capability-icon">
                                                                    <svg stroke="currentColor" fill="currentColor"
                                                                        stroke-width="0" version="1.1" viewBox="0 0 16 16"
                                                                        height="1em" width="1em"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path
                                                                            d="M14.5 2h-13c-0.825 0-1.5 0.675-1.5 1.5v9c0 0.825 0.675 1.5 1.5 1.5h13c0.825 0 1.5-0.675 1.5-1.5v-9c0-0.825-0.675-1.5-1.5-1.5zM1.5 3h13c0.271 0 0.5 0.229 0.5 0.5v1.5h-14v-1.5c0-0.271 0.229-0.5 0.5-0.5zM14.5 13h-13c-0.271 0-0.5-0.229-0.5-0.5v-4.5h14v4.5c0 0.271-0.229 0.5-0.5 0.5zM2 10h1v2h-1zM4 10h1v2h-1zM6 10h1v2h-1z">
                                                                        </path>
                                                                    </svg>
                                                                </div>
                                                                <div class="capability-title">Карты</div>
                                                            </a>
                                                        </div>
                                                        <div class="capability_row">
                                                            <a class="capability-link" href="{{ route('credits.alias.potrebitelskie-krediti') }}">
                                                                <div class="capability-icon">
                                                                    <svg stroke="currentColor" fill="currentColor"
                                                                        stroke-width="0" viewBox="0 0 640 512" height="1em"
                                                                        width="1em" xmlns="http://www.w3.org/2000/svg">
                                                                        <path
                                                                            d="M592 0H48A48 48 0 0 0 0 48v320a48 48 0 0 0 48 48h240v32H112a16 16 0 0 0-16 16v32a16 16 0 0 0 16 16h416a16 16 0 0 0 16-16v-32a16 16 0 0 0-16-16H352v-32h240a48 48 0 0 0 48-48V48a48 48 0 0 0-48-48zm-16 352H64V64h512z">
                                                                        </path>
                                                                    </svg>
                                                                </div>
                                                                <div class="capability-title">Потребительские кредиты</div>
                                                            </a>
                                                        </div>
                                                        <div class="capability_row">
                                                            <a class="capability-link" href="{{ route('credits.alias.avtokredity-v-uzbekistane') }}">
                                                                <div class="capability-icon">
                                                                    <svg stroke="currentColor" fill="currentColor"
                                                                        stroke-width="0" viewBox="0 0 512 512" height="1em"
                                                                        width="1em" xmlns="http://www.w3.org/2000/svg">
                                                                        <path
                                                                            d="M499.99 176h-59.87l-16.64-41.6C406.38 91.63 365.57 64 319.5 64h-127c-46.06 0-86.88 27.63-103.99 70.4L71.87 176H12.01C4.2 176-1.53 183.34.37 190.91l6 24C7.7 220.25 12.5 224 18.01 224h20.07C24.65 235.73 16 252.78 16 272v48c0 16.12 6.16 30.67 16 41.93V416c0 17.67 14.33 32 32 32h32c17.67 0 32-14.33 32-32v-32h256v32c0 17.67 14.33 32 32 32h32c17.67 0 32-14.33 32-32v-54.07c9.84-11.25 16-25.8 16-41.93v-48c0-19.22-8.65-36.27-22.07-48H494c5.51 0 10.31-3.75 11.64-9.09l6-24c1.89-7.57-3.84-14.91-11.65-14.91zm-352.06-17.83c7.29-18.22 24.94-30.17 44.57-30.17h127c19.63 0 37.28 11.95 44.57 30.17L384 208H128l19.93-49.83zM96 319.8c-19.2 0-32-12.76-32-31.9S76.8 256 96 256s48 28.71 48 47.85-28.8 15.95-48 15.95zm320 0c-19.2 0-48 3.19-48-15.95S396.8 256 416 256s32 12.76 32 31.9-12.8 31.9-32 31.9z">
                                                                        </path>
                                                                    </svg>
                                                                </div>
                                                                <div class="capability-title">Автокредиты</div>
                                                            </a>
                                                        </div>
                                                        <div class="capability_row">
                                                            <a class="capability-link"
                                                                href="{{ route('credits.alias.ipotechnye-kredity-v-uzbekistane') }}">
                                                                <div class="capability-icon">
                                                                    <svg stroke="currentColor" fill="currentColor"
                                                                        stroke-width="0" viewBox="0 0 448 512" height="1em"
                                                                        width="1em" xmlns="http://www.w3.org/2000/svg">
                                                                        <path
                                                                            d="M436 480h-20V24c0-13.255-10.745-24-24-24H56C42.745 0 32 10.745 32 24v456H12c-6.627 0-12 5.373-12 12v20h448v-20c0-6.627-5.373-12-12-12zM128 76c0-6.627 5.373-12 12-12h40c6.627 0 12 5.373 12 12v40c0 6.627-5.373 12-12 12h-40c-6.627 0-12-5.373-12-12V76zm0 96c0-6.627 5.373-12 12-12h40c6.627 0 12 5.373 12 12v40c0 6.627-5.373 12-12 12h-40c-6.627 0-12-5.373-12-12v-40zm52 148h-40c-6.627 0-12-5.373-12-12v-40c0-6.627 5.373-12 12-12h40c6.627 0 12 5.373 12 12v40c0 6.627-5.373 12-12 12zm76 160h-64v-84c0-6.627 5.373-12 12-12h40c6.627 0 12 5.373 12 12v84zm64-172c0 6.627-5.373 12-12 12h-40c-6.627 0-12-5.373-12-12v-40c0-6.627 5.373-12 12-12h40c6.627 0 12 5.373 12 12v40zm0-96c0 6.627-5.373 12-12 12h-40c-6.627 0-12-5.373-12-12v-40c0-6.627 5.373-12 12-12h40c6.627 0 12 5.373 12 12v40zm0-96c0 6.627-5.373 12-12 12h-40c-6.627 0-12-5.373-12-12V76c0-6.627 5.373-12 12-12h40c6.627 0 12 5.373 12 12v40z">
                                                                        </path>
                                                                    </svg>
                                                                </div>
                                                                <div class="capability-title">Ипотека</div>
                                                            </a>
                                                        </div>
                                                        <div class="capability_row">
                                                            <a class="capability-link"
                                                                href="{{ route('credits.alias.bankovskie-mikrozajmy-v-uzbekistane') }}">
                                                                <div class="capability-icon">
                                                                    <svg stroke="currentColor" fill="currentColor"
                                                                        stroke-width="0" viewBox="0 0 640 512" height="1em"
                                                                        width="1em" xmlns="http://www.w3.org/2000/svg">
                                                                        <path
                                                                            d="M496 224c-79.59 0-144 64.41-144 144s64.41 144 144 144 144-64.41 144-144-64.41-144-144-144zm64 150.29c0 5.34-4.37 9.71-9.71 9.71h-60.57c-5.34 0-9.71-4.37-9.71-9.71v-76.57c0-5.34 4.37-9.71 9.71-9.71h12.57c5.34 0 9.71 4.37 9.71 9.71V352h38.29c5.34 0 9.71 4.37 9.71 9.71v12.58zM496 192c5.4 0 10.72.33 16 .81V144c0-25.6-22.4-48-48-48h-80V48c0-25.6-22.4-48-48-48H176c-25.6 0-48 22.4-48 48v48H48c-25.6 0-48 22.4-48 48v80h395.12c28.6-20.09 63.35-32 100.88-32zM320 96H192V64h128v32zm6.82 224H208c-8.84 0-16-7.16-16-16v-48H0v144c0 25.6 22.4 48 48 48h291.43C327.1 423.96 320 396.82 320 368c0-16.66 2.48-32.72 6.82-48z">
                                                                        </path>
                                                                    </svg>
                                                                </div>
                                                                <div class="capability-title">Микрозаймы</div>
                                                            </a>
                                                        </div>
                                                        <div class="capability_row">
                                                            <a class="capability-link" href="{{ route('credits.alias.overdraft-v-uzbekistane') }}">
                                                                <div class="capability-icon">
                                                                    <svg stroke="currentColor" fill="currentColor"
                                                                        stroke-width="0" version="1.1" viewBox="0 0 16 16"
                                                                        height="1em" width="1em"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path
                                                                            d="M4.5 0l4 4-8.5 8.5 3.5 3.5 8.5-8.5 4 4v-11.5h-11.5z">
                                                                        </path>
                                                                    </svg>
                                                                </div>
                                                                <div class="capability-title">Овердрафт</div>
                                                            </a>
                                                        </div>
                                                        <div class="capability_row">
                                                            <a class="capability-link"
                                                                href="{{ route('credits.alias.kredity-nachinayushhim-biznesmenam-v-uzbekistane') }}">
                                                                <div class="capability-icon">
                                                                    <svg stroke="currentColor" fill="currentColor"
                                                                        stroke-width="0" version="1.1" viewBox="0 0 16 16"
                                                                        height="1em" width="1em"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path
                                                                            d="M15.802 2.102c-1.73-1.311-4.393-2.094-7.124-2.094-3.377 0-6.129 1.179-7.549 3.235-0.667 0.965-1.036 2.109-1.097 3.398-0.054 1.148 0.139 2.418 0.573 3.784 1.482-4.444 5.622-7.923 10.395-7.923 0 0-4.466 1.175-7.274 4.816-0.002 0.002-0.039 0.048-0.103 0.136-0.564 0.754-1.055 1.612-1.423 2.583-0.623 1.482-1.2 3.515-1.2 5.965h2c0 0-0.304-1.91 0.224-4.106 0.873 0.118 1.654 0.177 2.357 0.177 1.839 0 3.146-0.398 4.115-1.252 0.868-0.765 1.347-1.794 1.854-2.882 0.774-1.663 1.651-3.547 4.198-5.002 0.146-0.083 0.24-0.234 0.251-0.402s-0.063-0.329-0.197-0.431z">
                                                                        </path>
                                                                    </svg>
                                                                </div>
                                                                <div class="capability-title">Кредиты предпринимателям</div>
                                                            </a>
                                                        </div>
                                                        <div class="capability_row">
                                                            <a class="capability-link"
                                                                href="{{ route('credits.alias.obrazovatelnye-kredity-v-uzbekistane') }}">
                                                                <div class="capability-icon">
                                                                    <svg stroke="currentColor" fill="currentColor"
                                                                        stroke-width="0" viewBox="0 0 640 512" height="1em"
                                                                        width="1em" xmlns="http://www.w3.org/2000/svg">
                                                                        <path
                                                                            d="M622.34 153.2L343.4 67.5c-15.2-4.67-31.6-4.67-46.79 0L17.66 153.2c-23.54 7.23-23.54 38.36 0 45.59l48.63 14.94c-10.67 13.19-17.23 29.28-17.88 46.9C38.78 266.15 32 276.11 32 288c0 10.78 5.68 19.85 13.86 25.65L20.33 428.53C18.11 438.52 25.71 448 35.94 448h56.11c10.24 0 17.84-9.48 15.62-19.47L82.14 313.65C90.32 307.85 96 298.78 96 288c0-11.57-6.47-21.25-15.66-26.87.76-15.02 8.44-28.3 20.69-36.72L296.6 284.5c9.06 2.78 26.44 6.25 46.79 0l278.95-85.7c23.55-7.24 23.55-38.36 0-45.6zM352.79 315.09c-28.53 8.76-52.84 3.92-65.59 0l-145.02-44.55L128 384c0 35.35 85.96 64 192 64s192-28.65 192-64l-14.18-113.47-145.03 44.56z">
                                                                        </path>
                                                                    </svg>
                                                                </div>
                                                                <div class="capability-title">Кредиты на образование</div>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="vc_row wpb_row vc_row-fluid wpb_animate_when_almost_visible wpb_bottom-to-top bottom-to-top vc_custom_1767904905083 ult-vc-hide-row vc_row-has-fill wpb_start_animation animated"
                        data-rtl="false" style="position: relative;" data-row-effect-mobile-disable="true">
                        <div class="upb_row_bg" data-bg-override="ex-full"></div>
                        <div class="wpb_column vc_column_container vc_col-sm-12">
                            <div class="vc_column-inner vc_custom_1554841835687">
                                <div class="wpb_wrapper">
                                    <div class="vc_row wpb_row vc_inner vc_row-fluid">
                                        <div class="wpb_column vc_column_container vc_col-sm-12">
                                            <div class="vc_column-inner">
                                                <div class="wpb_wrapper">
                                                    <div class="wpb_text_column wpb_content_element ">
                                                        <div class="wpb_wrapper">

                                                            <div
                                                                class="section-br"
                                                                data-homepage-rates
                                                                data-rates-by-place='@json($homepageRatesByPlace ?? [])'
                                                            >
                                                                <div class="br-content">

                                                                    <div class="br-main">
                                                                        <div class="br-main-header">
                                                                            <h4>
                                                                                <span class="header-title-1">Лучшие курсы
                                                                                    валют в банках Узбекистана</span>
                                                                                <span class="header-title-2">Лучшие курсы
                                                                                    валют </span>
                                                                                <span class="header-title-3">Курсы
                                                                                    валют</span>
                                                                            </h4>
                                                                            <div class="header-place-btns">

                                                                                <div place="0" data-place="cash" data-tooltip="В офисе банка"
                                                                                    class="place-btn active">
                                                                                    <svg stroke="currentColor"
                                                                                        fill="currentColor" stroke-width="0"
                                                                                        viewBox="0 0 512 512" height="1em"
                                                                                        width="1em"
                                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                                        <path
                                                                                            d="M496 128v16a8 8 0 0 1-8 8h-24v12c0 6.627-5.373 12-12 12H60c-6.627 0-12-5.373-12-12v-12H24a8 8 0 0 1-8-8v-16a8 8 0 0 1 4.941-7.392l232-88a7.996 7.996 0 0 1 6.118 0l232 88A8 8 0 0 1 496 128zm-24 304H40c-13.255 0-24 10.745-24 24v16a8 8 0 0 0 8 8h464a8 8 0 0 0 8-8v-16c0-13.255-10.745-24-24-24zM96 192v192H60c-6.627 0-12 5.373-12 12v20h416v-20c0-6.627-5.373-12-12-12h-36V192h-64v192h-64V192h-64v192h-64V192H96z">
                                                                                        </path>
                                                                                    </svg>
                                                                                </div>

                                                                                <div place="1" data-place="atm" data-tooltip="В банкомате"
                                                                                    class="place-btn">

                                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                                        width="1em" height="1em"
                                                                                        viewBox="0 0 1 1"
                                                                                        fill="currentColor">
                                                                                        <path
                                                                                            d="M0.859378 0.437754L0.808343 0.232434C0.799397 0.196614 0.761727 0.174114 0.724348 0.182954C0.691473 0.190694 0.675492 0.212634 0.669016 0.228074C0.661358 0.246294 0.627175 0.328854 0.619184 0.345914C0.616382 0.351834 0.610467 0.354954 0.609263 0.355474C0.602477 0.358374 0.534546 0.385994 0.534546 0.385994V0.386114C0.527788 0.389117 0.52206 0.393918 0.518037 0.399952C0.514015 0.405986 0.511865 0.413002 0.511841 0.420174C0.511841 0.441034 0.529565 0.457954 0.55142 0.457954C0.556131 0.457954 0.560614 0.457014 0.564828 0.455574V0.455714L0.655007 0.419594C0.667377 0.414034 0.673416 0.406994 0.67956 0.396734C0.682133 0.392414 0.689958 0.374334 0.696329 0.359494L0.750748 0.578794L0.750686 0.918594C0.750582 0.952354 0.774782 0.979874 0.810148 0.979994C0.845556 0.980114 0.871146 0.952854 0.871375 0.919034L0.871727 0.517974C0.871686 0.499114 0.863903 0.454294 0.859378 0.437754Z">
                                                                                        </path>
                                                                                        <path
                                                                                            d="M0.662041 0.172819C0.706229 0.172819 0.74205 0.138614 0.74205 0.0964194C0.74205 0.0542249 0.706229 0.0200195 0.662041 0.0200195C0.617853 0.0200195 0.582031 0.0542249 0.582031 0.0964194C0.582031 0.138614 0.617853 0.172819 0.662041 0.172819Z">
                                                                                        </path>
                                                                                        <path
                                                                                            d="M0.355649 0.742017V0.827616C0.366586 0.825887 0.376535 0.820481 0.38373 0.812357C0.390911 0.804217 0.394502 0.794697 0.394502 0.783737C0.394502 0.773937 0.391492 0.765477 0.385474 0.758357C0.379372 0.751317 0.36943 0.745877 0.355649 0.742017V0.742017ZM0.300774 0.609079C0.295377 0.616079 0.292658 0.623839 0.292658 0.632319C0.292658 0.640118 0.295128 0.647278 0.300006 0.653918C0.304945 0.660598 0.312396 0.665998 0.322296 0.670078V0.594139C0.313657 0.596789 0.306119 0.602022 0.300774 0.609079V0.609079ZM0.511849 0.5074H0.511932L0.387923 0.267662L0.387902 0.267882C0.383268 0.258663 0.376028 0.250891 0.367015 0.24546C0.358001 0.240029 0.34758 0.23716 0.336949 0.237183H0.222653C0.191458 0.237163 0.166138 0.261323 0.166138 0.291122V0.979975H0.518262V0.53444C0.518231 0.52508 0.516041 0.515843 0.511849 0.5074V0.5074ZM0.425675 0.842776C0.408864 0.860916 0.385536 0.872036 0.355711 0.876116V0.917176H0.322358V0.877216C0.295834 0.874156 0.274353 0.864696 0.257749 0.848916C0.241228 0.833096 0.230664 0.810797 0.226077 0.781977L0.286058 0.775797C0.288157 0.786804 0.292882 0.797191 0.29986 0.806137C0.306564 0.814617 0.314077 0.820757 0.322338 0.824557V0.732597C0.292326 0.724398 0.270264 0.711978 0.256296 0.695438C0.242245 0.678838 0.23523 0.658698 0.23523 0.634979C0.23523 0.610979 0.243138 0.590819 0.259057 0.574479C0.274913 0.558179 0.296 0.548759 0.322358 0.546299V0.52456H0.355711V0.546299C0.380036 0.549059 0.399441 0.556999 0.413845 0.570079C0.428207 0.583139 0.437402 0.600719 0.441407 0.622659L0.383274 0.629899C0.379724 0.612659 0.370551 0.600919 0.355711 0.594779V0.680578C0.392468 0.690118 0.417519 0.702438 0.430864 0.717558C0.444168 0.732718 0.450851 0.752137 0.450851 0.775837C0.450851 0.802317 0.442466 0.824616 0.425675 0.842776V0.842776Z">
                                                                                        </path>
                                                                                    </svg>

                                                                                </div>

                                                                                <div place="2" data-place="app" data-tooltip="В приложении"
                                                                                    class="place-btn">
                                                                                    <svg stroke="currentColor"
                                                                                        fill="currentColor" stroke-width="0"
                                                                                        viewBox="0 0 320 512" height="1em"
                                                                                        width="1em"
                                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                                        <path
                                                                                            d="M272 0H48C21.5 0 0 21.5 0 48v416c0 26.5 21.5 48 48 48h224c26.5 0 48-21.5 48-48V48c0-26.5-21.5-48-48-48zM160 480c-17.7 0-32-14.3-32-32s14.3-32 32-32 32 14.3 32 32-14.3 32-32 32zm112-108c0 6.6-5.4 12-12 12H60c-6.6 0-12-5.4-12-12V60c0-6.6 5.4-12 12-12h200c6.6 0 12 5.4 12 12v312z">
                                                                                        </path>
                                                                                    </svg>
                                                                                </div>

                                                                            </div>
                                                                        </div>

                                                                        <div data-homepage-rates-list>
                                                                            @include('components.public.homepage-rate-rows', ['rates' => $homepageRates ?? []])
                                                                        </div>
                                                                    </div>

                                                                    <div class="br-right">
                                                                        <h3 class="br-right-title">Хотите выгодно приобрести
                                                                            валюту или продать?</h3>
                                                                        <div class="br-right-subtitle">
                                                                            Мы ежедневно обновляем курсы валют всех банков
                                                                            Узбекистана, подбирая для вас самые выгодные
                                                                            предложения.
                                                                        </div>
                                                                        <div class="br-right-btns">
                                                                            <a class="br-right-btn-1"
                                                                                href="{{ route('exchange-rates.index') }}">Сравнить
                                                                                курсы</a>
                                                                            <a class="br-right-btn-2"
                                                                                href="{{ $telegramUrl }}"
                                                                                target="_blank">Канал в телеграм</a>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="vc_row wpb_row vc_inner vc_row-fluid">
                                        <div class="wpb_column vc_column_container vc_col-sm-12">
                                            <div class="vc_column-inner">
                                                <div class="wpb_wrapper">
                                                    <div class="vc_empty_space" style="height: 32px"><span
                                                            class="vc_empty_space_inner"></span></div>
                                                    <div id="ultimate-heading-28586a5949dd143e3"
                                                        class="uvc-heading ult-adjust-bottom-margin ultimate-heading-28586a5949dd143e3 uvc-8514 accent-border-color"
                                                        data-hspacer="line_only" data-halign="center"
                                                        style="text-align:center">
                                                        <div class="uvc-main-heading ult-responsive"
                                                            data-ultimate-target=".uvc-heading.ultimate-heading-28586a5949dd143e3 h2"
                                                            data-responsive-json-new="{&quot;font-size&quot;:&quot;desktop:40px;&quot;,&quot;line-height&quot;:&quot;desktop:46px;&quot;}">
                                                            <h2 style="font-weight:bold;">ЗОЛОТО В СЛИТКАХ</h2>
                                                        </div>
                                                        <div class="uvc-heading-spacer line_only" style="topheight:5px;">
                                                            <span class="uvc-headings-line"
                                                                style="border-style: solid; border-bottom-width: 5px; width: 200px; margin: 0px auto;"></span>
                                                        </div>
                                                    </div>
                                                    <div class="vc_empty_space" style="height: 16px"><span
                                                            class="vc_empty_space_inner"></span></div>
                                                    <div class="wpb_text_column wpb_content_element ">
                                                        <div class="wpb_wrapper">

                                                            <div class="wpb_text_column wpb_content_element ">
                                                                <div class="wpb_wrapper">
                                                                    @if (! empty($goldPricedOn))
                                                                        <p style="text-align: center;"><span
                                                                                style="color: #999999; font-family: helvetica, arial, sans-serif;"><span
                                                                                    class="item-title">Дата обновления:
                                                                                    {{ $goldPricedOn }}</span></span></p>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <a href="{{ route('gold.show') }}"
                                                                    class="gold-table-link"
                                                                    style="text-decoration: none; color: inherit; display: block;">
                                                                    <table class="gold-table">
                                                                        <thead>
                                                                            <tr>
                                                                                <th rowspan="2">
                                                                                    <div>Вес золота</div>
                                                                                    <div
                                                                                        style="font-size: 11px; opacity: 0.9; margin-top: 5px;">
                                                                                        в граммах</div>
                                                                                </th>
                                                                                <th rowspan="2">
                                                                                    <div>Цена продажи</div>
                                                                                    <div
                                                                                        style="font-size: 11px; opacity: 0.9; margin-top: 5px;">
                                                                                        розничная цена</div>
                                                                                </th>
                                                                                <th colspan="2">
                                                                                    Цена обратного выкупа
                                                                                </th>
                                                                            </tr>
                                                                            <tr class="sub-header">
                                                                                <th>
                                                                                    <div>Неповреждённая упаковка</div>
                                                                                    <div
                                                                                        style="font-size: 10px; opacity: 0.8; margin-top: 3px;">
                                                                                        оригинальное состояние</div>
                                                                                </th>
                                                                                <th>
                                                                                    <div>Повреждённая упаковка</div>
                                                                                    <div
                                                                                        style="font-size: 10px; opacity: 0.8; margin-top: 3px;">
                                                                                        или после экспертизы</div>
                                                                                </th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @forelse (($goldPrices ?? []) as $goldRow)
                                                                                <tr>
                                                                                    <td><span class="weight">{{ $goldRow['weight_label_long'] }}</span></td>
                                                                                    <td class="sale-price">
                                                                                        <div class="price">{{ $goldRow['sell_price_formatted'] }}</div>
                                                                                        @if ($goldRow['diff_formatted'] !== null)
                                                                                            <span class="price-badge {{ $goldRow['diff_positive'] ? 'green' : 'red' }}">{{ $goldRow['diff_formatted'] }}</span>
                                                                                        @endif
                                                                                    </td>
                                                                                    <td class="buyback-good">
                                                                                        <div class="price">{{ $goldRow['buyback_good_formatted'] }}</div>
                                                                                    </td>
                                                                                    <td class="buyback-damaged">
                                                                                        <div class="price">{{ $goldRow['buyback_damaged_formatted'] }}</div>
                                                                                    </td>
                                                                                </tr>
                                                                            @empty
                                                                                <tr>
                                                                                    <td colspan="4" style="padding: 16px; color: #888;">Цены на золото пока не загружены.</td>
                                                                                </tr>
                                                                            @endforelse
                                                                        </tbody>
                                                                    </table>
                                                                </a>
                                                            </div>

                                                        </div>
                                                    </div>

                                                    <div class="wpb_text_column wpb_content_element ">
                                                        <div class="wpb_wrapper">

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- Row Backgrounds -->
                    <div
                        class="vc_row wpb_row vc_row-fluid wpb_animate_when_almost_visible wpb_bottom-to-top bottom-to-top wpb_start_animation animated">
                        <div class="wpb_column vc_column_container vc_col-sm-12">
                            <div class="vc_column-inner">
                                <div class="wpb_wrapper">
                                    <div class="wpb_raw_code wpb_content_element wpb_raw_html">
                                        <div class="wpb_wrapper">
                                            <div>
                                                <style>
                                                    /* Основные стили */
                                                    .features-section {
                                                        padding: 80px 20px;
                                                        background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
                                                        position: relative;
                                                        overflow: hidden;
                                                    }

                                                    .features-section::before {
                                                        content: '';
                                                        position: absolute;
                                                        top: 0;
                                                        left: 0;
                                                        right: 0;
                                                        height: 1px;
                                                        background: linear-gradient(90deg, transparent, #e2e8f0, transparent);
                                                    }

                                                    .container {
                                                        max-width: 1200px;
                                                        margin: 0 auto;
                                                        position: relative;
                                                        z-index: 1;
                                                    }

                                                    .features-grid {
                                                        display: grid;
                                                        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                                                        gap: 40px;
                                                        margin-top: 20px;
                                                    }

                                                    /* Карточка фичи */
                                                    .feature-card {
                                                        background: white;
                                                        border-radius: 24px;
                                                        padding: 40px 30px;
                                                        text-align: center;
                                                        position: relative;
                                                        overflow: hidden;
                                                        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.06);
                                                        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                                                        border: 1px solid #f1f5f9;
                                                        display: flex;
                                                        flex-direction: column;
                                                        align-items: center;
                                                    }

                                                    .feature-card:hover {
                                                        transform: translateY(-10px);
                                                        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12);
                                                        border-color: #e0e7ff;
                                                    }

                                                    .feature-card::before {
                                                        content: '';
                                                        position: absolute;
                                                        top: 0;
                                                        left: 0;
                                                        right: 0;
                                                        height: 4px;
                                                        background: linear-gradient(90deg, #3b82f6, #8b5cf6, #ec4899);
                                                        opacity: 0;
                                                        transition: opacity 0.3s ease;
                                                    }

                                                    .feature-card:hover::before {
                                                        opacity: 1;
                                                    }

                                                    /* Иконка */
                                                    .feature-icon {
                                                        width: 80px;
                                                        height: 80px;
                                                        display: flex;
                                                        align-items: center;
                                                        justify-content: center;
                                                        margin-bottom: 30px;
                                                        color: #3b82f6;
                                                        transition: all 0.3s ease;
                                                    }

                                                    .feature-card:hover .feature-icon {
                                                        color: #1d4ed8;
                                                        transform: scale(1.1) rotate(5deg);
                                                    }

                                                    .feature-icon svg {
                                                        width: 100%;
                                                        height: 100%;
                                                    }

                                                    /* Заголовок */
                                                    .feature-title {
                                                        font-size: 22px;
                                                        font-weight: 700;
                                                        color: #1e293b;
                                                        margin-bottom: 20px;
                                                        line-height: 1.3;
                                                        font-family: 'Inter', -apple-system, sans-serif;
                                                        letter-spacing: 0.5px;
                                                        text-transform: uppercase;
                                                        position: relative;
                                                        display: inline-block;
                                                    }

                                                    .feature-title::after {
                                                        content: '';
                                                        position: absolute;
                                                        bottom: -8px;
                                                        left: 50%;
                                                        transform: translateX(-50%);
                                                        width: 40px;
                                                        height: 3px;
                                                        background: linear-gradient(90deg, #3b82f6, #8b5cf6);
                                                        border-radius: 2px;
                                                        opacity: 0.7;
                                                        transition: width 0.3s ease;
                                                    }

                                                    .feature-card:hover .feature-title::after {
                                                        width: 60px;
                                                    }

                                                    /* Текст */
                                                    .feature-text {
                                                        font-size: 16px;
                                                        line-height: 1.6;
                                                        color: #64748b;
                                                        margin-bottom: 30px;
                                                        max-width: 320px;
                                                        flex-grow: 1;
                                                        font-weight: 400;
                                                    }

                                                    /* Декоративный элемент */
                                                    .feature-decoration {
                                                        width: 100px;
                                                        height: 30px;
                                                        margin-top: 20px;
                                                        color: #cbd5e1;
                                                        transition: all 0.3s ease;
                                                    }

                                                    .feature-card:hover .feature-decoration {
                                                        color: #94a3b8;
                                                        transform: scale(1.1);
                                                    }

                                                    .feature-decoration svg {
                                                        width: 100%;
                                                        height: 100%;
                                                    }

                                                    /* Цветовые акценты для каждой карточки */
                                                    .feature-card:nth-child(1) .feature-icon {
                                                        color: #3b82f6;
                                                    }

                                                    .feature-card:nth-child(1):hover {
                                                        border-color: #dbeafe;
                                                        background: linear-gradient(180deg, #ffffff 0%, #eff6ff 100%);
                                                    }

                                                    .feature-card:nth-child(1) .feature-title::after {
                                                        background: linear-gradient(90deg, #3b82f6, #60a5fa);
                                                    }

                                                    .feature-card:nth-child(2) .feature-icon {
                                                        color: #10b981;
                                                    }

                                                    .feature-card:nth-child(2):hover {
                                                        border-color: #d1fae5;
                                                        background: linear-gradient(180deg, #ffffff 0%, #f0fdf4 100%);
                                                    }

                                                    .feature-card:nth-child(2) .feature-title::after {
                                                        background: linear-gradient(90deg, #10b981, #34d399);
                                                    }

                                                    .feature-card:nth-child(3) .feature-icon {
                                                        color: #8b5cf6;
                                                    }

                                                    .feature-card:nth-child(3):hover {
                                                        border-color: #ede9fe;
                                                        background: linear-gradient(180deg, #ffffff 0%, #faf5ff 100%);
                                                    }

                                                    .feature-card:nth-child(3) .feature-title::after {
                                                        background: linear-gradient(90deg, #8b5cf6, #a78bfa);
                                                    }

                                                    /* Адаптивность */
                                                    @media (max-width: 768px) {
                                                        .features-section {
                                                            padding: 60px 20px;
                                                        }

                                                        .features-grid {
                                                            grid-template-columns: 1fr;
                                                            gap: 30px;
                                                            max-width: 400px;
                                                            margin: 0 auto;
                                                        }

                                                        .feature-card {
                                                            padding: 30px 20px;
                                                        }

                                                        .feature-title {
                                                            font-size: 20px;
                                                        }

                                                        .feature-text {
                                                            font-size: 15px;
                                                        }
                                                    }

                                                    /* Анимации */
                                                    @keyframes float {

                                                        0%,
                                                        100% {
                                                            transform: translateY(0);
                                                        }

                                                        50% {
                                                            transform: translateY(-5px);
                                                        }
                                                    }

                                                    .feature-icon {
                                                        animation: float 3s ease-in-out infinite;
                                                    }

                                                    .feature-card:nth-child(2) .feature-icon {
                                                        animation-delay: 0.2s;
                                                    }

                                                    .feature-card:nth-child(3) .feature-icon {
                                                        animation-delay: 0.4s;
                                                    }

                                                    /* Эффект свечения при наведении */
                                                    .feature-card::after {
                                                        content: '';
                                                        position: absolute;
                                                        top: 0;
                                                        left: 0;
                                                        right: 0;
                                                        bottom: 0;
                                                        border-radius: 24px;
                                                        background: radial-gradient(circle at 50% 0%, rgba(59, 130, 246, 0.1), transparent 70%);
                                                        opacity: 0;
                                                        transition: opacity 0.3s ease;
                                                        pointer-events: none;
                                                    }

                                                    .feature-card:hover::after {
                                                        opacity: 1;
                                                    }
                                                </style>
                                                <div class="features-section">
                                                    <div class="container">
                                                        <div class="features-grid">

                                                            <!-- Блок 1 -->
                                                            <div class="feature-card">
                                                                <div class="feature-icon">
                                                                    <svg viewBox="0 0 64 64" fill="none"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path
                                                                            d="M32 56C45.2548 56 56 45.2548 56 32C56 18.7452 45.2548 8 32 8C18.7452 8 8 18.7452 8 32C8 45.2548 18.7452 56 32 56Z"
                                                                            stroke="currentColor" stroke-width="3"
                                                                            stroke-linecap="round" stroke-linejoin="round">
                                                                        </path>
                                                                        <path d="M32 20V32L40 36" stroke="currentColor"
                                                                            stroke-width="3" stroke-linecap="round"
                                                                            stroke-linejoin="round"></path>
                                                                        <path
                                                                            d="M32 40C33.1046 40 34 39.1046 34 38C34 36.8954 33.1046 36 32 36C30.8954 36 30 36.8954 30 38C30 39.1046 30.8954 40 32 40Z"
                                                                            fill="currentColor"></path>
                                                                        <path d="M24 48H40" stroke="currentColor"
                                                                            stroke-width="3" stroke-linecap="round"></path>
                                                                        <path d="M20 16L44 20" stroke="currentColor"
                                                                            stroke-width="3" stroke-linecap="round"></path>
                                                                    </svg>
                                                                </div>
                                                                <h3 class="feature-title">АКТУАЛЬНАЯ ИНФОРМАЦИЯ</h3>
                                                                <p class="feature-text">Мы ежедневно собираем и обновляем
                                                                    предложения и информацию более {{ $organizationsCount }} финансовых
                                                                    организаций</p>
                                                                <div class="feature-decoration">
                                                                    <svg viewBox="0 0 100 20" fill="none"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M0 10L100 10" stroke="currentColor"
                                                                            stroke-width="2" stroke-dasharray="5 5"></path>
                                                                        <circle cx="10" cy="10" r="3" fill="currentColor">
                                                                        </circle>
                                                                        <circle cx="90" cy="10" r="3" fill="currentColor">
                                                                        </circle>
                                                                    </svg>
                                                                </div>
                                                            </div>

                                                            <!-- Блок 2 -->
                                                            <div class="feature-card">
                                                                <div class="feature-icon">
                                                                    <svg viewBox="0 0 64 64" fill="none"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M48 24L32 40L24 32" stroke="currentColor"
                                                                            stroke-width="3" stroke-linecap="round"
                                                                            stroke-linejoin="round"></path>
                                                                        <path d="M16 40L32 24L40 32" stroke="currentColor"
                                                                            stroke-width="3" stroke-linecap="round"
                                                                            stroke-linejoin="round"></path>
                                                                        <path
                                                                            d="M56 32C56 45.2548 45.2548 56 32 56C18.7452 56 8 45.2548 8 32C8 18.7452 18.7452 8 32 8C45.2548 8 56 18.7452 56 32Z"
                                                                            stroke="currentColor" stroke-width="3"></path>
                                                                        <path d="M40 20L48 12" stroke="currentColor"
                                                                            stroke-width="3" stroke-linecap="round"></path>
                                                                        <path d="M24 44L16 52" stroke="currentColor"
                                                                            stroke-width="3" stroke-linecap="round"></path>
                                                                        <path d="M20 20L12 12" stroke="currentColor"
                                                                            stroke-width="3" stroke-linecap="round"></path>
                                                                        <path d="M44 44L52 52" stroke="currentColor"
                                                                            stroke-width="3" stroke-linecap="round"></path>
                                                                    </svg>
                                                                </div>
                                                                <h3 class="feature-title">ЭКОНОМИМ ВРЕМЯ</h3>
                                                                <p class="feature-text">У нас вы всегда найдете актуальную
                                                                    информацию, которая сэкономит ваше время и деньги</p>
                                                                <div class="feature-decoration">
                                                                    <svg viewBox="0 0 100 20" fill="none"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M0 10Q25 0 50 10T100 10"
                                                                            stroke="currentColor" stroke-width="2"
                                                                            fill="none"></path>
                                                                        <circle cx="50" cy="10" r="4" fill="currentColor">
                                                                        </circle>
                                                                    </svg>
                                                                </div>
                                                            </div>

                                                            <!-- Блок 3 -->
                                                            <div class="feature-card">
                                                                <div class="feature-icon">
                                                                    <svg viewBox="0 0 64 64" fill="none"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M16 16L32 8L48 16V40L32 48L16 40V16Z"
                                                                            stroke="currentColor" stroke-width="3"
                                                                            stroke-linecap="round" stroke-linejoin="round">
                                                                        </path>
                                                                        <path d="M16 16L32 24L48 16" stroke="currentColor"
                                                                            stroke-width="3" stroke-linecap="round"
                                                                            stroke-linejoin="round"></path>
                                                                        <path d="M32 24V48" stroke="currentColor"
                                                                            stroke-width="3" stroke-linecap="round"
                                                                            stroke-linejoin="round"></path>
                                                                        <path d="M24 32L40 32" stroke="currentColor"
                                                                            stroke-width="3" stroke-linecap="round"></path>
                                                                        <path d="M20 40L44 40" stroke="currentColor"
                                                                            stroke-width="3" stroke-linecap="round"></path>
                                                                        <path
                                                                            d="M28 28C29.1046 28 30 27.1046 30 26C30 24.8954 29.1046 24 28 24C26.8954 24 26 24.8954 26 26C26 27.1046 26.8954 28 28 28Z"
                                                                            fill="currentColor"></path>
                                                                        <path
                                                                            d="M36 28C37.1046 28 38 27.1046 38 26C38 24.8954 37.1046 24 36 24C34.8954 24 34 24.8954 34 26C34 27.1046 34.8954 28 36 28Z"
                                                                            fill="currentColor"></path>
                                                                        <path
                                                                            d="M32 36C33.1046 36 34 35.1046 34 34C34 32.8954 33.1046 32 32 32C30.8954 32 30 32.8954 30 34C30 35.1046 30.8954 36 32 36Z"
                                                                            fill="currentColor"></path>
                                                                    </svg>
                                                                </div>
                                                                <h3 class="feature-title">ЭФФЕКТИВНО И ТОЧНО</h3>
                                                                <p class="feature-text">Используйте полученную информацию с
                                                                    целью экономии и приумножения финансов</p>
                                                                <div class="feature-decoration">
                                                                    <svg viewBox="0 0 100 20" fill="none"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M0 10L40 10M60 10L100 10"
                                                                            stroke="currentColor" stroke-width="2"></path>
                                                                        <path d="M40 10L60 10" stroke="currentColor"
                                                                            stroke-width="4" stroke-linecap="round"></path>
                                                                        <path d="M50 5L55 10L50 15L45 10Z"
                                                                            fill="currentColor"></path>
                                                                    </svg>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div data-vc-full-width="true" data-vc-full-width-init="false"
                        class="vc_row wpb_row vc_row-fluid wpb_animate_when_almost_visible wpb_bottom-to-top bottom-to-top vc_custom_1767904954268 vc_row-has-fill wpb_start_animation animated">
                        <div class="wpb_column vc_column_container vc_col-sm-12">
                            <div class="vc_column-inner vc_custom_1548843355428">
                                <div class="wpb_wrapper">
                                    <div id="ultimate-heading-1946a5949dd14dfc"
                                        class="uvc-heading ult-adjust-bottom-margin ultimate-heading-1946a5949dd14dfc uvc-8888 accent-border-color"
                                        data-hspacer="line_only" data-halign="center" style="text-align:center">
                                        <div class="uvc-main-heading ult-responsive"
                                            data-ultimate-target=".uvc-heading.ultimate-heading-1946a5949dd14dfc h2"
                                            data-responsive-json-new="{&quot;font-size&quot;:&quot;desktop:40px;&quot;,&quot;line-height&quot;:&quot;desktop:46px;&quot;}">
                                            <h2 style="font-weight:bold;">ЗНАНИЯ - СИЛА</h2>
                                        </div>
                                        <div class="uvc-heading-spacer line_only" style="topheight:5px;"><span
                                                class="uvc-headings-line"
                                                style="border-style: solid; border-bottom-width: 5px; width: 200px; margin: 0px auto;"></span>
                                        </div>
                                    </div>
                                    <div class="vc_empty_space" style="height: 32px"><span
                                            class="vc_empty_space_inner"></span></div>
                                    <div class="wpb_text_column wpb_content_element ">
                                        <div class="wpb_wrapper">

                                            <div class="posts-сarousel" data-posts-carousel>
                                                <div class="posts-header">
                                                    @foreach ($articleTabs as $index => $tab)
                                                        <button
                                                            type="button"
                                                            class="posts-header-item{{ $index === 0 ? ' active' : '' }}"
                                                            data-posts-tab="{{ $tab['value'] }}"
                                                        >{{ $tab['label'] }}</button>
                                                    @endforeach
                                                </div>

                                                <div class="posts-item-bg">
                                                    <div class="posts-item-content">
                                                        @foreach ($articleTabs as $index => $tab)
                                                            <div
                                                                class="posts-panel{{ $index === 0 ? ' is-active' : '' }}"
                                                                data-posts-panel="{{ $tab['value'] }}"
                                                            >
                                                                <div class="posts-item-row">
                                                                    @forelse ($tab['articles'] as $article)
                                                                        <div class="post-item">
                                                                            <a href="{{ route('articles.show', $article) }}">
                                                                                <div class="post-img">
                                                                                    @if ($article->coverUrl())
                                                                                        <img
                                                                                            src="{{ $article->coverUrl() }}"
                                                                                            alt="{{ $article->title }}"
                                                                                            loading="lazy"
                                                                                        >
                                                                                    @endif
                                                                                </div>
                                                                                <div class="post-item-details">
                                                                                    <span class="post-date">
                                                                                        <span>
                                                                                            @if ($tab['show_date'] && $article->published_at)
                                                                                                {{ $article->published_at->format('d.m.Y') }}
                                                                                            @endif
                                                                                        </span>
                                                                                    </span>
                                                                                </div>
                                                                                <div class="post-title">{{ $article->title }}</div>
                                                                            </a>
                                                                        </div>
                                                                    @empty
                                                                        <p class="articles-empty">Материалов пока нет.</p>
                                                                    @endforelse
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="vc_row-full-width vc_clearfix"></div>
                </div>
            </div><!-- #content -->
    </div>
    </div>
@endsection