<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1ebbf0">
    <title>@yield('title', config('app.name', 'Pultop'))</title>
    @if (!empty($metaDescription))
        <meta name="description" content="{{ $metaDescription }}">
    @endif
    @if (!empty($metaKeywords))
        <meta name="keywords" content="{{ $metaKeywords }}">
    @endif
    @stack('head')

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon/favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/cropped-logo-180x180.png') }}">

    {{-- Fonts / icons --}}
    <link rel="stylesheet" href="{{ asset('css/fonts-roboto.css') }}">
    <link rel="stylesheet" href="{{ asset('fonts/icomoon-the7-font/icomoon-the7-font.min.css') }}">
    <link rel="stylesheet" href="{{ asset('fonts/FontAwesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('fonts/FontAwesome/back-compat.min.css') }}">
    <link rel="stylesheet" href="{{ asset('fonts/smile_fonts/Defaults/Defaults.css') }}">
    <link rel="stylesheet" href="{{ asset('fonts/smile_fonts/icomoon-icomoonfree-16x16/icomoon-icomoonfree-16x16.css') }}">

    {{-- Plugins (order close to live homepage) --}}
    <link rel="stylesheet" href="{{ asset('css/plugins/cf7-styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/plugins/style-admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/plugins/pul_style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/plugins/jquery.ui.css') }}">
    <link rel="stylesheet" href="{{ asset('css/plugins/wp-calc-jquery.ui.css') }}">
    <link rel="stylesheet" href="{{ asset('css/plugins/wp-calc-finance.css') }}">
    <link rel="stylesheet" href="{{ asset('css/plugins/js_composer.min.css') }}">

    {{-- The7 core --}}
    <link rel="stylesheet" href="{{ asset('css/min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom-scrollbar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/wpbakery.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/plugins/post-type.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vars.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('css/media.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mega-menu.css') }}">
    <link rel="stylesheet" href="{{ asset('css/the7-elements-albums-portfolio.css') }}">
    <link rel="stylesheet" href="{{ asset('css/post-type-dynamic.css') }}">
    <link rel="stylesheet" href="{{ asset('css/child-style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/plugins/ultimate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/plugins/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/plugins/best_rate.css') }}">
    <link rel="stylesheet" href="{{ asset('css/plugins/style-posts.css') }}">

    {{-- Site-specific inline (from WP Customizer / The7) --}}
    <link rel="stylesheet" href="{{ asset('css/inline/dt-main-inline-css.css') }}">
    <link rel="stylesheet" href="{{ asset('css/inline/the7-custom-inline-css.css') }}">
    <link rel="stylesheet" href="{{ asset('css/inline/wp-custom-css.css') }}">

    {{-- Sidebar widgets (loaded in head; component push runs too late) --}}
    <link rel="stylesheet" href="{{ asset('css/sidebar-news.css') }}">
    <link rel="stylesheet" href="{{ asset('css/gold-widget.css') }}">

    @stack('styles')
</head>
<body
    id="the7-body"
    class="@yield('body_class') page page-template-default dt-responsive-on right-mobile-menu-close-icon ouside-menu-close-icon mobile-hamburger-close-bg-enable mobile-hamburger-close-bg-hover-enable fade-medium-mobile-menu-close-icon fade-medium-menu-close-icon accent-gradient srcset-enabled btn-flat custom-btn-color custom-btn-hover-color phantom-sticky phantom-shadow-decoration phantom-main-logo-on sticky-mobile-header top-header first-switch-logo-left first-switch-menu-right second-switch-logo-left second-switch-menu-right right-mobile-menu layzr-loading-on no-avatars popup-message-style the7-ver-11.14.0.1 dt-fa-compatibility wpb-js-composer js-comp-ver-7.5 vc_responsive"
>
    <div id="page">
        <x-public.header />

        @yield('content')

        <x-public.footer />
    </div>

    <a href="#" class="scroll-top off">
        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 16 16" style="enable-background:new 0 0 16 16;" xml:space="preserve">
            <path d="M11.7,6.3l-3-3C8.5,3.1,8.3,3,8,3c0,0,0,0,0,0C7.7,3,7.5,3.1,7.3,3.3l-3,3c-0.4,0.4-0.4,1,0,1.4c0.4,0.4,1,0.4,1.4,0L7,6.4
    V12c0,0.6,0.4,1,1,1s1-0.4,1-1V6.4l1.3,1.3c0.4,0.4,1,0.4,1.4,0C11.9,7.5,12,7.3,12,7S11.9,6.5,11.7,6.3z"></path>
        </svg>
        <span class="screen-reader-text">Наверх</span>
    </a>

    <script src="{{ asset('js/public.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
