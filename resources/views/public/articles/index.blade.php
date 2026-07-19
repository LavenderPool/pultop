@extends('layouts.public')

@section('title', $title)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/articles.css') }}">
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
                    <span itemprop="name">{{ $h1 }}</span>
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
                <nav class="articles-tabs" aria-label="Категории материалов">
                    <a
                        href="{{ route('articles.index') }}"
                        class="articles-tabs__item{{ $activeCategory === null ? ' is-active' : '' }}"
                    >Все</a>
                    @foreach ($categories as $category)
                        <a
                            href="{{ route('articles.category', $category->publicSlug()) }}"
                            class="articles-tabs__item{{ $activeCategory === $category ? ' is-active' : '' }}"
                        >{{ $category->label() }}</a>
                    @endforeach
                </nav>

                <div class="articles-grid">
                    @forelse ($articles as $article)
                        <article class="articles-card">
                            <a href="{{ route('articles.show', $article) }}" class="articles-card__link">
                                <div class="articles-card__media">
                                    @if ($article->coverUrl())
                                        <img src="{{ $article->coverUrl() }}" alt="{{ $article->title }}" loading="lazy">
                                    @else
                                        <div class="articles-card__placeholder" aria-hidden="true"></div>
                                    @endif
                                </div>
                                <div class="articles-card__body">
                                    @if ($article->published_at)
                                        <time class="articles-card__date" datetime="{{ $article->published_at->toIso8601String() }}">
                                            {{ $article->published_at->format('d.m.Y') }}
                                        </time>
                                    @endif
                                    <h2 class="articles-card__title">{{ $article->title }}</h2>
                                    @if ($article->excerpt)
                                        <p class="articles-card__excerpt">{{ $article->excerpt }}</p>
                                    @endif
                                </div>
                            </a>
                        </article>
                    @empty
                        <p class="articles-empty">Материалов пока нет.</p>
                    @endforelse
                </div>

                @if ($articles->hasPages())
                    <div class="articles-pagination">
                        {{ $articles->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
