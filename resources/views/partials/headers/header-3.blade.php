@php
    $menu = $waaiy->menu();
    $ctaLabel = $waaiy->translation('header.cta', 'Teklif Al');
@endphp

{{-- Varyasyon 3 · Yüzen: sayfa üzerinde süzülen yuvarlak bar --}}
<div class="announce">
    <div class="container-x announce-inner">
        <span class="announce-dot" aria-hidden="true"></span>
        {{ $waaiy->translation('header.announce', 'Yeni: Waaiy API 2.1 ile ön yüz çevirileri ve otomatik kayıt yayında.') }}
        <a href="{{ route('blog.index') }}" class="announce-link">{{ $waaiy->translation('header.announce_link', 'İncele') }} →</a>
    </div>
</div>
<div class="header-floating">
    <div class="container-x">
        <div class="pill-bar">
            <a href="{{ \App\Support\Locales::url('/') }}" class="brand" aria-label="{{ $siteName }} anasayfa">
                <span class="brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 2 7l10 5 10-5-10-5Z"/><path d="m2 17 10 5 10-5"/><path d="m2 12 10 5 10-5"/></svg>
                </span>
                <span class="brand-name">{{ $siteName }}</span>
            </a>

            <nav class="nav nav-pill" aria-label="Ana menü">
                @foreach ($menu as $item)
                    <div class="nav-item">
                        <a href="{{ \App\Support\Locales::url($item['url']) }}" class="nav-link">{{ $item['label'] }}</a>
                        @if ($item['children'])
                            <div class="mega">
                                @foreach ($item['children'] as $child)
                                    <a href="{{ \App\Support\Locales::url($child['url']) }}" class="mega-link">{{ $child['label'] }}</a>
                                @endforeach
                            </div>
                        @endif
                    </div>
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
</div>
