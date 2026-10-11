{{--
    Kayan kutu (Tasarım Stüdyosu)
    Header menü, blog görünümü, sektör ön ayarı ve RGB renk seçicileri burada.
    Tüm tercihler localStorage'da saklanır; sayfa yenilenince korunur.
--}}
<button class="cz-tab" data-customizer-toggle aria-controls="customizer" aria-expanded="false" aria-label="Tasarım stüdyosunu aç">
    <span class="cz-tab__icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="3"/>
            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.6 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.6a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1Z"/>
        </svg>
    </span>
    <span class="cz-tab__text">Stüdyo</span>
</button>

<aside id="customizer" class="cz" data-customizer aria-hidden="true">
    <header class="cz__head">
        <div>
            <p class="cz__eyebrow">Waaiy Tasarım Stüdyosu</p>
            <h2 class="cz__title">Varyasyonları canlı dene</h2>
        </div>
        <button class="cz__close" data-customizer-close aria-label="Paneli kapat">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 6l12 12M18 6 6 18"/></svg>
        </button>
    </header>

    <div class="cz__body">
        {{-- Header menü varyasyonları --}}
        <section class="cz-section">
            <div class="cz-section__top">
                <h3 class="cz-label">Header Menü</h3>
                <span class="cz-badge">1 / 2 / 3</span>
            </div>
            <div class="seg" data-seg="header">
                @foreach ($design['headers'] as $num => $h)
                    <button type="button" class="seg__btn" data-value="{{ $num }}" title="{{ $h['description'] }}">
                        <span class="seg__num">{{ $num }}</span>
                        <span class="seg__txt">{{ $h['label'] }}</span>
                    </button>
                @endforeach
            </div>
            <p class="cz-hint" data-hint="header"></p>
        </section>

        {{-- Blog görünüm varyasyonları --}}
        <section class="cz-section">
            <div class="cz-section__top">
                <h3 class="cz-label">Blog Görünümü</h3>
                <span class="cz-badge">1 / 2 / 3</span>
            </div>
            <div class="seg" data-seg="blogLayout">
                @foreach ($design['blogLayouts'] as $num => $h)
                    <button type="button" class="seg__btn" data-value="{{ $num }}" title="{{ $h['description'] }}">
                        <span class="seg__num">{{ $num }}</span>
                        <span class="seg__txt">{{ $h['label'] }}</span>
                    </button>
                @endforeach
            </div>
            <p class="cz-hint" data-hint="blogLayout"></p>
        </section>

        {{-- Sektör ön ayarları (multi-proposal) --}}
        <section class="cz-section">
            <div class="cz-section__top">
                <h3 class="cz-label">Sektör Ön Ayarı</h3>
                <span class="cz-badge">4 sektör</span>
            </div>
            <div class="sectors" data-seg="sector">
                @foreach ($design['sectors'] as $key => $sector)
                    <button type="button" class="sector-btn" data-value="{{ $key }}" title="{{ $sector['label'] }}">
                        <span class="sector-btn__swatch">
                            <i style="background: {{ $sector['primary'] }}"></i>
                            <i style="background: {{ $sector['accent'] }}"></i>
                        </span>
                        <span class="sector-btn__name">{{ $sector['label'] }}</span>
                    </button>
                @endforeach
            </div>
            <p class="cz-hint" data-hint="sector"></p>
        </section>

        {{-- Ana renk (RGB) --}}
        <section class="cz-section">
            <h3 class="cz-label">Ana Renk <span class="cz-sub">RGB</span></h3>
            @include('partials.customizer.color', ['name' => 'primary', 'label' => 'Ana renk'])
        </section>

        {{-- Vurgu rengi (RGB) --}}
        <section class="cz-section">
            <h3 class="cz-label">Vurgu Rengi <span class="cz-sub">RGB</span></h3>
            @include('partials.customizer.color', ['name' => 'accent', 'label' => 'Vurgu rengi'])
        </section>

        {{-- Köşe yuvarlaklığı --}}
        <section class="cz-section">
            <div class="cz-section__top">
                <h3 class="cz-label">Köşe Yuvarlaklığı</h3>
                <span class="cz-badge" data-radius-out>18px</span>
            </div>
            <input type="range" class="cz-range" min="0" max="32" step="1" value="18" data-radius>
        </section>

        <button type="button" class="btn btn-ghost cz-reset" data-customizer-reset>Sıfırla</button>
    </div>

    <footer class="cz__foot">
        <span>Önizleme modu</span>
        <a href="{{ config('waaiy.cms.url') }}" target="_blank" rel="noopener">{{ config('waaiy.cms.name') }}</a>
    </footer>
</aside>

<script>
    window.WAAIY_DESIGN = @json($design);
</script>
