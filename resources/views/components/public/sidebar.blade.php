<aside id="sidebar" class="sidebar">
    <div class="sidebar-content widget-divider-off">
        <section class="widget the7-recent-articles-enhanced-widget">
            <div class="widget-title">Валютные новости</div>
            <div class="the7-articles-list">
                @foreach ($news as $article)
                    <article class="the7-article-item">
                        <div class="the7-article-thumb">
                            <a href="{{ $article['url'] }}">
                                <img
                                    width="150"
                                    height="84"
                                    src="{{ $article['image'] }}"
                                    class="the7-article-image"
                                    alt="{{ $article['alt'] }}"
                                    loading="lazy"
                                    decoding="async"
                                >
                            </a>
                        </div>
                        <div class="the7-article-content">
                            <h4 class="the7-article-title">
                                <a href="{{ $article['url'] }}">{{ $article['title'] }}</a>
                            </h4>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        @if ($showGold)
            <x-public.gold-prices-widget :prices="$goldPrices" :priced-on="$goldPricedOn" />
        @endif
    </div>
</aside>
