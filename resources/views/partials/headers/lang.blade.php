@php
    $current = strtoupper($currentLang ?? \App\Support\Locales::current());
    $path = \App\Support\Locales::path();
    $suffix = $path === '/' ? '' : $path;
    $langs = $languages ?? \App\Support\Locales::all();
@endphp
<details class="lang-switch">
    <summary aria-label="Dil seç">🌐 {{ $current }}</summary>
    <div class="lang-menu">
        @foreach ($langs as $lang)
            <a href="{{ url('/'.$lang['short'].$suffix) }}"
               class="lang-option {{ ($currentLang ?? '') === $lang['short'] ? 'is-active' : '' }}"
               hreflang="{{ $lang['short'] }}">
                {{ strtoupper($lang['short']) }} · {{ $lang['name'] }}
            </a>
        @endforeach
    </div>
</details>
