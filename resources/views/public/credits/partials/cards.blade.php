@foreach ($credits as $index => $credit)
    <div class="item-content credit v-anim-fadein" style="--delay: {{ ($startIndex + $index) * 50 }}ms">
        <div class="bank-logo">
            @if ($credit->bank?->logoUrl())
                <img class="bank-logo-img" src="{{ $credit->bank->logoUrl() }}"
                    alt="Logo {{ $credit->bank->name }}"
                    title="{{ $credit->bank->name }}">
            @endif
            @if ($credit->bank)
                <a class="item-name-bank" href="{{ route('banks.show', $credit->bank) }}">
                    {{ $credit->bank->name }}
                </a>
            @endif
        </div>

        <div class="item-data">
            @if ($credit->rate_display)
                <div class="item-rate">{{ $credit->rate_display }}</div>
            @endif
            <a href="{{ route('credits.show', $credit) }}" style="text-decoration: none;" class="item-name">
                <span>{{ $credit->title }}</span>
            </a>
        </div>

        <div class="item-params">
            <div style="width: 25%;">
                <div>Срок кредита </div>
                <div><strong>{{ $credit->term_display ?: '—' }}</strong></div>
            </div>
            <div style="flex: 1;">
                <div>Сумма кредита </div>
                <div><strong>{{ $credit->amount_display ?: '—' }}</strong></div>
                @if ($credit->down_payment)
                    <div>
                        <div style="font-size: 0.9rem; color: #797979;">
                            первоначальный взнос {{ $credit->down_payment }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="btn-more">
            <a class="link" href="{{ route('credits.show', $credit) }}">Подробнее</a>
        </div>
    </div>
@endforeach
