@extends('layouts.app')

@php
    $phone = $siteSettings['telefon'] ?? '';
    $email = $siteSettings['eposta'] ?? '';
    $address = $siteSettings['adres'] ?? '';
    $social = $siteSettings['sosyal'] ?? [];
    $t = fn (string $key, string $default) => $waaiy->translation($key, $default);
@endphp

@section('content')
    <section class="page-hero page-hero--contact">
        <div class="hero__bg" aria-hidden="true">
            <span class="orb orb--1"></span>
            <span class="orb orb--2"></span>
        </div>
        <div class="container-x page-hero__inner">
            <p class="eyebrow"><span class="eyebrow__dot"></span>{{ $t('contact.hero.eyebrow', 'İletişim') }}</p>
            <h1 class="page-hero__title">{{ $t('contact.hero.title', 'Bir merhaba deyin') }}</h1>
            <p class="page-hero__sub">{{ $t('contact.hero.sub', 'Projeniz, iş birliği ya da sadece bir sorunuz mu var? Formu doldurun, en kısa sürede dönüş yapalım.') }}</p>

            <ul class="contact-quick">
                <li><span class="contact-quick__ok"></span>{{ $t('contact.quick.1', '24 saat içinde yanıt') }}</li>
                <li><span class="contact-quick__ok"></span>{{ $t('contact.quick.2', 'Gizlilik esaslı') }}</li>
                <li><span class="contact-quick__ok"></span>{{ $t('contact.quick.3', 'Ücretsiz ön görüşme') }}</li>
            </ul>
        </div>
    </section>

    @include('partials.breadcrumbs', ['seo' => $seo])

    <section class="section contact-section">
        <div class="container-x contact-grid">
            {{-- İletişim kanalları --}}
            <aside class="contact-aside">
                @if ($phone)
                    <a class="contact-card" href="tel:{{ preg_replace('/\s+/', '', $phone) }}">
                        <span class="contact-card__icon">@include('partials.icon', ['name' => 'phone'])</span>
                        <span>
                            <span class="contact-card__label">Telefon</span>
                            <span class="contact-card__value">{{ $phone }}</span>
                        </span>
                    </a>
                @endif

                @if ($email)
                    <a class="contact-card" href="mailto:{{ $email }}">
                        <span class="contact-card__icon">@include('partials.icon', ['name' => 'mail'])</span>
                        <span>
                            <span class="contact-card__label">E-posta</span>
                            <span class="contact-card__value">{{ $email }}</span>
                        </span>
                    </a>
                @endif

                @if ($address)
                    <div class="contact-card">
                        <span class="contact-card__icon">@include('partials.icon', ['name' => 'pin'])</span>
                        <span>
                            <span class="contact-card__label">Adres</span>
                            <span class="contact-card__value">{{ $address }}</span>
                        </span>
                    </div>
                @endif

                <div class="contact-card">
                    <span class="contact-card__icon">@include('partials.icon', ['name' => 'clock'])</span>
                    <span>
                        <span class="contact-card__label">Çalışma Saatleri</span>
                        <span class="contact-card__value">{{ $t('contact.hours', 'Pazartesi – Cuma · 09:00 – 18:00') }}</span>
                    </span>
                </div>

                @if (! empty($social))
                    <div class="contact-social">
                        @foreach ($social as $name => $url)
                            @if ($url)
                                <a href="{{ $url }}" class="social-link" target="_blank" rel="noopener nofollow" aria-label="{{ ucfirst($name) }}">
                                    {{ strtoupper(substr($name, 0, 2)) }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                @endif
            </aside>

            {{-- Form --}}
            <div class="contact-form-card">
                <h2 class="contact-form-card__title">{{ $t('contact.form.title', 'Bize yazın') }}</h2>
                <p class="contact-form-card__sub">{{ $t('contact.form.sub', 'Formu doldurun, mesajınız doğrudan panelimize düşsün.') }}</p>

                @if (session('status'))
                    <p class="form-note form-note--ok">{{ session('status') }}</p>
                @endif
                @if (session('error'))
                    <p class="form-note form-note--err">{{ session('error') }}</p>
                @endif

                <form method="post" action="{{ route('form.submit') }}" class="form-grid contact-form">
                    @csrf
                    <input type="hidden" name="form_slug" value="iletisim">
                    <label class="field-group">
                        <span class="field-label">Ad Soyad</span>
                        <input type="text" name="ad_soyad" required placeholder="Adınız Soyadınız" class="field">
                    </label>
                    <label class="field-group">
                        <span class="field-label">E-posta</span>
                        <input type="email" name="eposta" required placeholder="ornek@site.com" class="field">
                    </label>
                    <label class="field-group field-group--full">
                        <span class="field-label">Mesajınız</span>
                        <textarea name="mesaj" rows="5" required placeholder="Kısaca projenizden bahsedin..." class="field"></textarea>
                    </label>
                    <button type="submit" class="btn btn-primary btn-lg">{{ $t('contact.form.button', 'Mesajı Gönder') }}</button>
                </form>
            </div>
        </div>
    </section>
@endsection
