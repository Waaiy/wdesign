@extends('layouts.app')

@section('content')
    <section class="page-hero">
        <div class="container-x page-hero__inner">
            <p class="eyebrow"><span class="eyebrow__dot"></span>Sayfa</p>
            <h1 class="page-hero__title">{{ $page['title'] }}</h1>
            @if (! empty($page['excerpt']))
                <p class="page-hero__sub">{{ $page['excerpt'] }}</p>
            @endif
        </div>
    </section>

    @include('partials.breadcrumbs', ['seo' => $seo])

    <section class="section">
        <div class="container-x page-layout {{ $page['slug'] === 'iletisim' ? 'page-layout--contact' : '' }}">
            <div class="prose">
                {!! $page['content'] !!}
            </div>

            @if ($page['slug'] === 'iletisim')
                <div class="contact-panel">
                    <h2 class="block-title">Bize yazın</h2>
                    @if (session('status'))
                        <p class="form-note form-note--ok">{{ session('status') }}</p>
                    @endif
                    @if (session('error'))
                        <p class="form-note form-note--err">{{ session('error') }}</p>
                    @endif
                    <form method="post" action="{{ route('form.submit') }}" class="form-grid">
                        @csrf
                        <input type="hidden" name="form_slug" value="iletisim">
                        <input type="text" name="ad_soyad" required placeholder="Ad Soyad" class="field">
                        <input type="email" name="eposta" required placeholder="E-posta" class="field">
                        <textarea name="mesaj" rows="5" required placeholder="Mesajınız" class="field field--full"></textarea>
                        <button type="submit" class="btn btn-primary">Mesajı Gönder</button>
                    </form>

                    <ul class="contact-info">
                        @if (! empty($siteSettings['telefon']))<li><strong>Telefon</strong> {{ $siteSettings['telefon'] }}</li>@endif
                        @if (! empty($siteSettings['eposta']))<li><strong>E-posta</strong> {{ $siteSettings['eposta'] }}</li>@endif
                        @if (! empty($siteSettings['adres']))<li><strong>Adres</strong> {{ $siteSettings['adres'] }}</li>@endif
                    </ul>
                </div>
            @endif
        </div>
    </section>
@endsection
