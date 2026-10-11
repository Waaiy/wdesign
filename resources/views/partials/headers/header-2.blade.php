@php
    $menu = $waaiy->menu();
    $half = (int) ceil(count($menu) / 2);
    $left = array_slice($menu, 0, $half);
    $right = array_slice($menu, $half);
    $ctaLabel = $waaiy->translation('header.cta', 'Teklif Al');
@endphp

{{-- Varyasyon 2 · Merkez: logo ortada, menü iki yana bölünmüş --}}
<div class="header-bar header-bar--center">
    <div class="container-x header-inner header-inner--center">
        <nav class="nav nav-split nav-split--left" aria-label="Ana menü (sol)">
            @foreach ($left as $item)
                <a href="{{ \App\Support\Locales::url($item['url']) }}" class="nav-link">{{ $item['label'] }}</a>
            @endforeach
        </nav>

        <a href="{{ \App\Support\Locales::url('/') }}" class="brand brand--center" aria-label="{{ $siteName }} anasayfa">
            <span class="brand-mark" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 2 7l10 5 10-5-10-5Z"/><path d="m2 17 10 5 10-5"/><path d="m2 12 10 5 10-5"/></svg>
            </span>
            <span class="brand-name">{{ $siteName }}</span>
        </a>

        <nav class="nav nav-split nav-split--right" aria-label="Ana menü (sağ)">
            @foreach ($right as $item)
                <a href="{{ \App\Support\Locales::url($item['url']) }}" class="nav-link">{{ $item['label'] }}</a>
            @endforeach
        </nav>

        <div class="header-actions header-actions--corner">
            @include('partials.headers.lang')
            <a href="{{ route('contact') }}" class="btn btn-primary btn-sm">{{ $ctaLabel }}</a>
            <button class="menu-toggle" data-menu-toggle aria-label="Menüyü aç">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
            </button>
        </div>
    </div>
</div>
