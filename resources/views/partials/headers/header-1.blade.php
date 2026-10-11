@php
    $menu = $waaiy->menu();
    $ctaLabel = $waaiy->translation('header.cta', 'Teklif Al');
@endphp

{{-- Varyasyon 1 · Klasik: logo solda, menü ortada, CTA sağda --}}
<div class="header-bar">
    <div class="container-x header-inner">
        <a href="{{ \App\Support\Locales::url('/') }}" class="brand" aria-label="{{ $siteName }} anasayfa">
            <span class="brand-mark" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 2 7l10 5 10-5-10-5Z"/><path d="m2 17 10 5 10-5"/><path d="m2 12 10 5 10-5"/></svg>
            </span>
            <span class="brand-name">{{ $siteName }}</span>
        </a>

        <nav class="nav nav-center" aria-label="Ana menü">
            @foreach ($menu as $item)
                <a href="{{ \App\Support\Locales::url($item['url']) }}" class="nav-link {{ $item['children'] ? 'has-children' : '' }}">
                    {{ $item['label'] }}
                    @if ($item['children'])<svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>@endif
                </a>
            @endforeach
        </nav>

        <div class="header-actions">
            @include('partials.headers.lang')
            <a href="{{ route('contact') }}" class="btn btn-primary btn-sm">{{ $ctaLabel }}</a>
            <button class="menu-toggle" data-menu-toggle aria-label="Menüyü aç">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
            </button>
        </div>
    </div>
</div>
