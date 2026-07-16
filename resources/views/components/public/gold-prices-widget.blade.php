@props([
    'prices' => [],
    'pricedOn' => null,
])

<section class="widget the7-gold-price-widget">
    <div class="widget-title">Цены на золото</div>
    <div class="the7-gold-prices">
        @if ($pricedOn)
            <div class="gold-update-time">Обновлено: {{ $pricedOn }}</div>
        @endif

        <div class="gold-prices-grid">
            @forelse ($prices as $price)
                <div class="gold-item">
                    <div class="gold-weight">{{ $price['weight_label'] }}</div>
                    <div class="gold-price">{{ $price['sell_price_formatted'] }}</div>
                    @if ($price['diff_formatted'] !== null)
                        <div class="gold-change {{ $price['diff_positive'] ? 'gold-up' : 'gold-down' }}">
                            {{ $price['diff_formatted'] }}
                        </div>
                    @else
                        <div class="gold-change gold-neutral">—</div>
                    @endif
                </div>
            @empty
                <p style="padding:8px 0;color:#888;font-size:14px;">Цены на золото пока не загружены.</p>
            @endforelse
        </div>

        @if ($prices !== [])
            <div>
                <a class="dt-btn dt-btn-m dt-btn-submit" href="{{ route('gold.show') }}" style="color: #fff !important;">Статистика</a>
            </div>
        @endif
    </div>
</section>
