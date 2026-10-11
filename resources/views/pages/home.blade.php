@extends('layouts.app')

@section('content')
    {{-- HERO --}}
    <section class="hero">
        <div class="hero__bg" aria-hidden="true">
            <span class="orb orb--1"></span>
            <span class="orb orb--2"></span>
            <span class="orb orb--3"></span>
            <span class="grid-lines"></span>
        </div>

        <div class="container-x hero__inner">
            <div class="hero__copy">
                <p class="eyebrow" data-sector-text="hero.eyebrow">
                    <span class="eyebrow__dot"></span>{{ $t['hero']['eyebrow'] }}
                </p>
                <h1 class="hero__title" data-sector-text="hero.title">{{ $t['hero']['title'] }}</h1>
                <p class="hero__sub" data-sector-text="hero.subtitle">{{ $t['hero']['subtitle'] }}</p>

                <div class="hero-actions">
                    <a href="{{ route('contact') }}" class="btn btn-primary btn-lg" data-sector-text="hero.primary_cta">
                        {{ $t['hero']['primary_cta'] }}
                    </a>
                    <a href="{{ route('blog.index') }}" class="btn btn-ghost btn-lg" data-sector-text="hero.secondary_cta">
                        {{ $t['hero']['secondary_cta'] }}
                    </a>
                </div>

                <ul class="hero-trust">
                    @foreach ($t['trust'] as $trust)
                        <li><span class="dot-ok"></span> {{ $trust }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="hero__visual" aria-hidden="true">
                <div class="mock">
                    <div class="mock__top">
                        <span class="mock__dot"></span><span class="mock__dot"></span><span class="mock__dot"></span>
                        <span class="mock__url">panel.waaiy.dev</span>
                    </div>
                    <div class="mock__body">
                        <aside class="mock__side">
                            <span class="mock__line mock__line--active"></span>
                            <span class="mock__line"></span>
                            <span class="mock__line"></span>
                            <span class="mock__line mock__line--short"></span>
                        </aside>
                        <div class="mock__main">
                            <div class="mock__cards">
                                <span class="mock__card mock__card--brand"></span>
                                <span class="mock__card"></span>
                                <span class="mock__card"></span>
                            </div>
                            <span class="mock__block"></span>
                            <span class="mock__block mock__block--sm"></span>
                        </div>
                    </div>
                </div>

                <div class="float-chip float-chip--1">
                    <strong>+128%</strong>
                    <span>organik trafik</span>
                </div>
                <div class="float-chip float-chip--2">
                    <strong>0.9s</strong>
                    <span>LCP skoru</span>
                </div>
                <div class="float-chip float-chip--3">
                    <strong>3</strong>
                    <span>header varyasyonu</span>
                </div>
            </div>
        </div>
    </section>

    {{-- İSTATİSTİK BANDI --}}
    <section class="stats">
        <div class="container-x stats__inner">
            @foreach ($t['stats'] as $stat)
                <div class="stat"><strong>{{ $stat['value'] }}</strong><span>{{ $stat['label'] }}</span></div>
            @endforeach
        </div>
    </section>

    {{-- ÖZELLİKLER (sektöre göre değişir) --}}
    <section class="section" id="ozellikler">
        <div class="container-x">
            <header class="section-head">
                <p class="eyebrow"><span class="eyebrow__dot"></span>{{ $t['why']['eyebrow'] }}</p>
                <h2 class="section-title">{{ $t['why']['title'] }}</h2>
                <p class="section-sub">{{ $t['why']['sub'] }}</p>
            </header>

            <div class="features">
                @foreach ($t['features'] as $i => $feature)
                    <article class="feature reveal">
                        <span class="feature__icon">@include('partials.icon', ['name' => $feature['icon']])</span>
                        <h3 class="feature__title" data-sector-text="features.{{ $i }}.title">{{ $feature['title'] }}</h3>
                        <p class="feature__text" data-sector-text="features.{{ $i }}.text">{{ $feature['text'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- VARYASYON VİTRİNİ --}}
    <section class="section section--alt" id="varyasyonlar">
        <div class="container-x">
            <header class="section-head">
                <p class="eyebrow"><span class="eyebrow__dot"></span>{{ $t['variants']['eyebrow'] }}</p>
                <h2 class="section-title">{{ $t['variants']['title'] }}</h2>
                <p class="section-sub">{{ $t['variants']['sub'] }}</p>
            </header>

            <div class="variants">
                <div class="variant-card reveal">
                    <span class="variant-card__tag">Header</span>
                    <h3>1 · Klasik / 2 · Merkez / 3 · Yüzen</h3>
                    <p>Klasik kurumsal, merkez editöryel, yüzen ise modern bir izlenim bırakır.</p>
                    <div class="variant-preview">
                        <span class="vp vp--1"><i></i><b></b><i></i></span>
                        <span class="vp vp--2"><i></i><b></b><i></i></span>
                        <span class="vp vp--3"><b></b><i></i></span>
                    </div>
                </div>
                <div class="variant-card reveal">
                    <span class="variant-card__tag">Blog</span>
                    <h3>1 · Izgara / 2 · Liste / 3 · Dergi</h3>
                    <p>Aynı kartlar; ızgarada eşit, listede yatay, dergide öne çıkan odaklı dizilir.</p>
                    <div class="variant-preview">
                        <span class="vp vp--grid"><i></i><i></i><i></i></span>
                        <span class="vp vp--list"><b></b><i></i></span>
                        <span class="vp vp--mag"><b></b><i></i></span>
                    </div>
                </div>
                <div class="variant-card reveal">
                    <span class="variant-card__tag">Renk</span>
                    <h3>RGB ile sınırsız palet</h3>
                    <p>Kayan kutu üzerindeki seçicilerle ana ve vurgu renklerini anında değiştirin.</p>
                    <div class="variant-preview">
                        <span class="sw-line"><i style="background:#5b2ee6"></i><i style="background:#2563eb"></i><i style="background:#0d9488"></i><i style="background:#b45309"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ÜRÜNLER --}}
    @if (! empty($products))
        <section class="section" id="urunler">
            <div class="container-x">
                <header class="section-head section-head--row">
                    <div>
                        <p class="eyebrow"><span class="eyebrow__dot"></span>{{ $t['products']['eyebrow'] }}</p>
                        <h2 class="section-title">{{ $t['products']['title'] }}</h2>
                    </div>
                    <a href="{{ route('product.index') }}" class="btn btn-ghost btn-sm">{{ $t['products']['all'] }} →</a>
                </header>
                @include('partials.product.grid', ['products' => $products])
            </div>
        </section>
    @endif

    {{-- BLOG --}}
    <section class="section section--alt" id="blog">
        <div class="container-x">
            <header class="section-head section-head--row">
                <div>
                    <p class="eyebrow"><span class="eyebrow__dot"></span>{{ $t['blog']['eyebrow'] }}</p>
                    <h2 class="section-title">{{ $t['blog']['title'] }}</h2>
                </div>
                <a href="{{ route('blog.index') }}" class="btn btn-ghost btn-sm">{{ $t['blog']['all'] }} →</a>
            </header>

            @include('partials.blog.grid', ['posts' => $posts])
        </div>
    </section>

    {{-- CTA --}}
    <section class="cta">
        <div class="container-x cta__inner">
            <div>
                <h2 class="cta__title">{{ $t['cta']['title'] }}</h2>
                <p class="cta__sub">{{ $t['cta']['sub'] }}</p>
            </div>
            <div class="cta__actions">
                <a href="{{ config('waaiy.cms.url') }}" target="_blank" rel="noopener" class="btn btn-primary btn-lg">{{ $t['cta']['primary'] }}</a>
                <a href="{{ route('contact') }}" class="btn btn-ghost btn-lg">{{ $t['cta']['secondary'] }}</a>
            </div>
        </div>
    </section>
@endsection
