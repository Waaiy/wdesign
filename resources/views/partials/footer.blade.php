@php
    $menu = $waaiy->menu();
    $categories = $waaiy->categories();
    $social = $siteSettings['sosyal'] ?? [];
    $cms = config('waaiy.cms');
@endphp

<footer class="site-footer">
    <div class="container-x">
        <div class="footer-top">
            <div class="footer-brand">
                <a href="{{ \App\Support\Locales::url('/') }}" class="brand brand--light">
                    <span class="brand-mark" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 2 7l10 5 10-5-10-5Z"/><path d="m2 17 10 5 10-5"/><path d="m2 12 10 5 10-5"/></svg>
                    </span>
                    <span class="brand-name">{{ $siteName }}</span>
                </a>
                <p class="footer-desc">{{ $siteSettings['slogan'] ?? config('waaiy.seo.description') }}</p>

                <div class="footer-social">
                    @foreach ($social as $name => $url)
                        @if ($url)
                            <a href="{{ $url }}" class="social-link" target="_blank" rel="noopener nofollow" aria-label="{{ ucfirst($name) }}">
                                {{ strtoupper(substr($name, 0, 2)) }}
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="footer-col">
                <h3 class="footer-heading">Kurumsal</h3>
                <ul class="footer-list">
                    @foreach ($menu as $item)
                        <li><a href="{{ \App\Support\Locales::url($item['url']) }}">{{ $item['label'] }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div class="footer-col">
                <h3 class="footer-heading">Kategoriler</h3>
                <ul class="footer-list">
                    @foreach ($categories as $cat)
                        <li><a href="{{ route('blog.category', ['slug' => $cat['slug']]) }}">{{ $cat['name'] }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div class="footer-col">
                <h3 class="footer-heading">Bültene Katıl</h3>
                <p class="footer-desc">Yeni yazı ve ürün güncellemelerini ilk siz öğrenin.</p>
                <form class="newsletter" method="post" action="{{ route('subscribe') }}">
                    @csrf
                    <label class="sr-only" for="newsletter-email">E-posta adresiniz</label>
                    <input id="newsletter-email" type="email" name="email" required placeholder="ornek@site.com" class="newsletter-input">
                    <button type="submit" class="btn btn-primary btn-sm">Abone Ol</button>
                </form>
                @if (session('status'))
                    <p class="form-note form-note--ok">{{ session('status') }}</p>
                @endif
                @if (session('error'))
                    <p class="form-note form-note--err">{{ session('error') }}</p>
                @endif
            </div>
        </div>

        <div class="footer-bottom">
            <p class="copyright">
                © {{ date('Y') }} {{ $siteName }}. Tüm hakları saklıdır.
            </p>
            <p class="cms-credit">
                <a href="{{ $cms['url'] }}" target="_blank" rel="noopener" class="cms-credit__link">
                    {{ $cms['label'] }}
                </a>
                <span class="cms-credit__by">— {{ $cms['name'] }}</span>
            </p>
        </div>
    </div>
</footer>
