@foreach ($deposits as $index => $deposit)
    <div class="item-content credit v-anim-fadein" style="--delay: {{ ($startIndex + $index) * 50 }}ms">
        <div class="bank-logo">
            @if ($deposit->bank?->logoUrl())
                <img class="bank-logo-img" src="{{ $deposit->bank->logoUrl() }}"
                    alt="Logo {{ $deposit->bank->name }}"
                    title="{{ $deposit->bank->name }}">
            @endif
            @if ($deposit->bank)
                <a class="item-name-bank" href="{{ route('banks.show', $deposit->bank) }}">
                    {{ $deposit->bank->name }}
                </a>
            @endif
        </div>

        <div class="item-data">
            @if ($deposit->rate_display)
                <div class="item-rate">{{ $deposit->rate_display }}</div>
            @endif
            <a href="{{ route('deposits.show', $deposit) }}" style="text-decoration: none;" class="item-name">
                <span>{{ $deposit->title }}</span>
            </a>
        </div>

        <div class="item-params">
            <div style="width: 25%;">
                <div>Срок вклада </div>
                <div><strong>{{ $deposit->term_display ?: '—' }}</strong></div>
            </div>
            <div style="flex: 1;">
                <div>Сумма вклада </div>
                <div><strong>{{ $deposit->amount_display ?: '—' }}</strong></div>
            </div>
        </div>

        <div class="btn-more">
            <a class="link" href="{{ route('deposits.show', $deposit) }}">Подробнее</a>
        </div>
    </div>
@endforeach
