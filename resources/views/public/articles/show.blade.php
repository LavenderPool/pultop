@extends('layouts.public')

@section('title', $title.' - '.config('app.name', 'Pultop'))

@if (!empty($metaDescription))
    @push('head')
        <meta name="description" content="{{ $metaDescription }}">
    @endpush
@endif

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/articles.css') }}">
@endpush

@section('content')
<div class="page-title content-left solid-bg page-title-responsive-enabled">
    <div class="wf-wrap">
        <div class="page-title-head hgroup"><h1 class="entry-title">{{ $article->title }}</h1></div>
        <div class="page-title-breadcrumbs">
            <div class="assistive-text">You are here:</div>
            <ol class="breadcrumbs text-small" itemscope itemtype="https://schema.org/BreadcrumbList">
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a itemprop="item" href="{{ url('/') }}" title="Home"><span itemprop="name">Home</span></a>
                    <meta itemprop="position" content="1" />
                </li>
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a itemprop="item" href="{{ route('articles.category', $article->category->publicSlug()) }}" title="{{ $article->category->label() }}">
                        <span itemprop="name">{{ $article->category->label() }}</span>
                    </a>
                    <meta itemprop="position" content="2" />
                </li>
                <li class="current" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <span itemprop="name">{{ $article->title }}</span>
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
                <article class="article-single">
                    @if ($article->published_at)
                        <time class="article-single__date" datetime="{{ $article->published_at->toIso8601String() }}">
                            {{ $article->published_at->format('d.m.Y H:i') }}
                        </time>
                    @endif

                    @if ($article->coverUrl())
                        <div class="article-single__cover">
                            <img src="{{ $article->coverUrl() }}" alt="{{ $article->title }}">
                        </div>
                    @endif

                    <div class="article-single__body">
                        {!! $article->body !!}
                    </div>
                </article>
            </div>

            <x-public.sidebar />
        </div>
    </div>
</div>
@endsection
