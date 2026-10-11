@extends('layouts.app')

@php
    $fmt = fn ($v) => number_format((float) $v, 2, ',', '.').' ₺';
    $gallery = ! empty($product['gallery']) ? $product['gallery'] : array_filter([$product['cover']]);
    $gallery = array_values($gallery);
@endphp

@section('content')
    <section class="product-hero">
        <div class="container-x">
            @include('partials.breadcrumbs', ['seo' => $seo])

            <div class="pd">
                {{-- GALERİ --}}
                <div class="pd__gallery">
                    <div class="pd__main">
                        @if ($gallery !== [])
                            <img id="pd-main" src="{{ $gallery[0] }}" alt="{{ $product['title'] }}" width="1000" height="1000" fetchpriority="high">
                        @else
                            <span class="pd__ph"></span>
                        @endif
                        <span class="product-card__flags">
                            @if (! empty($product['discount']))<span class="flag flag--sale">-%{{ $product['discount'] }}</span>@endif
                            @if (! empty($product['badge']))<span class="flag flag--brand">{{ $product['badge'] }}</span>@endif
                        </span>
                    </div>

                    @if (count($gallery) > 1)
                        <div class="pd__thumbs" data-gallery-thumbs>
                            @foreach ($gallery as $i => $img)
                                <button type="button" class="pd__thumb {{ $i === 0 ? 'is-active' : '' }}"
                                        data-src="{{ $img }}" aria-label="Görsel {{ $i + 1 }}">
                                    <img src="{{ $img }}" alt="{{ $product['title'] }} görsel {{ $i + 1 }}" loading="lazy" width="200" height="200">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- SATIN ALMA KUTUSU --}}
                <div class="pd__info">
                    @if (! empty($product['category']))
                        <a href="{{ route('product.category', ['slug' => $product['category']['slug']]) }}" class="chip chip--brand">{{ $product['category']['name'] }}</a>
                    @endif

                    <h1 class="pd__title">{{ $product['title'] }}</h1>

                    <div class="pd__meta">
                        @if (! empty($product['rating']))
                            <span class="stars" style="--pct: {{ ($product['rating'] / 5) * 100 }}%"></span>
                            <strong>{{ number_format($product['rating'], 1, ',', '.') }}</strong>
                            <span class="pd__reviews">({{ $product['reviews'] }} değerlendirme)</span>
                        @endif
                        @if (! empty($product['sku']))<span class="pd__sku">SKU: {{ $product['sku'] }}</span>@endif
                    </div>

                    <p class="pd__excerpt">{{ $product['excerpt'] }}</p>

                    @if (! empty($product['price']))
                        <div class="pd__price">
                            @if (! empty($product['old_price']))
                                <del>{{ $fmt($product['old_price']) }}</del>
                            @endif
                            <strong>{{ $fmt($product['price']) }}</strong>
                            @if (! empty($product['discount']))
                                <span class="pd__discount">-%{{ $product['discount'] }}</span>
                            @endif
                        </div>
                    @else
                        <div class="pd__price pd__price--ask">
                            <strong>Fiyat için iletişime geçin</strong>
                        </div>
                    @endif

                    <p class="pd__stock {{ ($product['stock'] ?? 0) > 0 ? 'is-in' : 'is-out' }}">
                        {{ ($product['stock'] ?? 0) > 0 ? 'Stokta · '.$product['stock'].' adet' : 'Stokta yok' }}
                    </p>

                    @foreach (($product['variants'] ?? []) as $variant)
                        <div class="pd__variant" data-variant>
                            <span class="pd__variant-name">{{ $variant['name'] }}</span>
                            <div class="pd__options">
                                @foreach (($variant['options'] ?? []) as $oi => $option)
                                    <button type="button" class="pd__option {{ $oi === 0 ? 'is-active' : '' }}" data-option>{{ $option }}</button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    <div class="pd__actions">
                        <div class="qty" data-qty>
                            <button type="button" data-qty-dec aria-label="Adedi azalt">−</button>
                            <input type="number" value="1" min="1" max="99" data-qty-input aria-label="Adet">
                            <button type="button" data-qty-inc aria-label="Adedi arttır">+</button>
                        </div>
                        <button type="button" class="btn btn-primary btn-lg pd__add">Sepete Ekle</button>
                        <button type="button" class="btn btn-ghost btn-lg pd__fav" aria-label="Favorilere ekle">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20s-7-4.5-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.5-7 10-7 10Z"/></svg>
                        </button>
                    </div>

                    <ul class="pd__trust">
                        <li><span>🚚</span> Aynı gün kargo</li>
                        <li><span>↩️</span> 14 gün iade</li>
                        <li><span>🛡️</span> 2 yıl garanti</li>
                        <li><span>🔒</span> Güvenli ödeme</li>
                    </ul>

                    @if (! empty($product['specs']))
                        <ul class="pd__specs">
                            @foreach ($product['specs'] as $spec)
                                <li><span>{{ $spec['label'] }}</span><strong>{{ $spec['value'] }}</strong></li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- SEKMELER + SSS --}}
    <section class="section">
        <div class="container-x pd-detail">
            @if (! empty($product['tabs']))
                <div class="pd-tabs" data-tabs>
                    <div class="pd-tabs__nav" role="tablist">
                        @foreach ($product['tabs'] as $i => $tab)
                            <button type="button" role="tab" class="pd-tabs__btn {{ $i === 0 ? 'is-active' : '' }}"
                                    data-tab-btn="{{ $i }}">{{ $tab['title'] }}</button>
                        @endforeach
                    </div>
                    <div class="pd-tabs__panels">
                        @foreach ($product['tabs'] as $i => $tab)
                            <div class="pd-tabs__panel {{ $i === 0 ? 'is-active' : '' }}" data-tab-panel="{{ $i }}">
                                <div class="prose">{!! $tab['content'] !!}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if (! empty($product['accordions']))
                <div class="accordions">
                    <h2 class="block-title">Sık Sorulan Sorular</h2>
                    @foreach ($product['accordions'] as $acc)
                        <details class="accordion">
                            <summary>{{ $acc['title'] }}</summary>
                            <div class="accordion__body">{!! $acc['content'] !!}</div>
                        </details>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- BENZER ÜRÜNLER --}}
    @if (! empty($related))
        <section class="section section--alt">
            <div class="container-x">
                <header class="section-head section-head--row">
                    <div>
                        <p class="eyebrow"><span class="eyebrow__dot"></span>Benzer ürünler</p>
                        <h2 class="section-title">Bunlar da hoşunuza gidebilir</h2>
                    </div>
                    <a href="{{ route('product.index') }}" class="btn btn-ghost btn-sm">Tüm ürünler →</a>
                </header>
                @include('partials.product.grid', ['products' => $related])
            </div>
        </section>
    @endif
@endsection
