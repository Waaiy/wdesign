@php
    $fmt = fn ($v) => number_format((float) $v, 2, ',', '.').' ₺';
    $url = route('product.show', $product['slug']);
@endphp
<article class="product-card">
    <a href="{{ $url }}" class="product-card__media" tabindex="-1" aria-hidden="true">
        @if (! empty($product['cover']))
            <img src="{{ $product['cover'] }}" alt="{{ $product['title'] }}" loading="lazy" decoding="async" width="800" height="800">
        @else
            <span class="product-card__ph"></span>
        @endif
        <span class="product-card__flags">
            @if (! empty($product['discount']))<span class="flag flag--sale">-%{{ $product['discount'] }}</span>@endif
            @if (! empty($product['badge']))<span class="flag flag--brand">{{ $product['badge'] }}</span>@endif
        </span>
    </a>

    <div class="product-card__body">
        @if (! empty($product['category']))
            <span class="product-card__cat">{{ $product['category']['name'] }}</span>
        @endif
        <h3 class="product-card__title"><a href="{{ $url }}">{{ $product['title'] }}</a></h3>

        @if (! empty($product['rating']))
            <span class="stars" style="--pct: {{ ($product['rating'] / 5) * 100 }}%" aria-label="{{ $product['rating'] }} / 5 puan"></span>
        @endif

        @if (! empty($product['price']))
            <div class="product-card__price">
                @if (! empty($product['old_price']))
                    <del>{{ $fmt($product['old_price']) }}</del>
                @endif
                <strong>{{ $fmt($product['price']) }}</strong>
            </div>
        @endif

        <a href="{{ $url }}" class="btn btn-primary btn-sm product-card__cta">İncele</a>
    </div>
</article>
