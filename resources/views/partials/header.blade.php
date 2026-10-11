@php
    $menu = $waaiy->menu();
    $ctaLabel = $waaiy->translation('header.cta', 'Teklif Al');
@endphp

<header class="site-headers">
    <div class="site-header" data-variant="1">@include('partials.headers.header-1')</div>
    <div class="site-header" data-variant="2">@include('partials.headers.header-2')</div>
    <div class="site-header" data-variant="3">@include('partials.headers.header-3')</div>
</header>

<aside class="mobile-drawer" id="mobile-drawer" aria-hidden="true" aria-label="Mobil menü">
    <div class="mobile-drawer__top">
        <a href="{{ \App\Support\Locales::url('/') }}" class="brand">
            <span class="brand-mark" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 2 7l10 5 10-5-10-5Z"/><path d="m2 17 10 5 10-5"/><path d="m2 12 10 5 10-5"/></svg>
            </span>
            <span class="brand-name">{{ $siteName }}</span>
        </a>
        <button class="menu-toggle" data-menu-close aria-label="Menüyü kapat">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 6l12 12M18 6 6 18"/></svg>
        </button>
    </div>

    <nav class="mobile-nav" aria-label="Mobil ana menü">
        @foreach ($menu as $item)
            <a href="{{ \App\Support\Locales::url($item['url']) }}" class="mobile-nav__link">{{ $item['label'] }}</a>
            @foreach ($item['children'] as $child)
                <a href="{{ \App\Support\Locales::url($child['url']) }}" class="mobile-nav__link mobile-nav__link--sub">{{ $child['label'] }}</a>
            @endforeach
        @endforeach
    </nav>

    <div class="mobile-drawer__foot">
        @include('partials.headers.lang')
        <a href="{{ route('contact') }}" class="btn btn-primary">{{ $ctaLabel }}</a>
    </div>
</aside>
<div class="drawer-backdrop" data-menu-close></div>
