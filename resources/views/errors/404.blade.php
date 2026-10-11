@php
    $seo = \App\Support\Seo::make([
        'title' => 'Sayfa bulunamadı',
        'description' => 'Aradığınız sayfa taşınmış, adı değişmiş veya kaldırılmış olabilir.',
        'robots' => 'noindex,follow',
        'breadcrumbs' => [
            ['name' => 'Anasayfa', 'url' => \App\Support\Locales::url('/')],
            ['name' => '404'],
        ],
    ]);
@endphp
@extends('layouts.app')

@section('content')
    <section class="page-hero page-hero--404">
        <div class="container-x page-hero__inner">
            <p class="eyebrow">Hata 404</p>
            <h1 class="page-hero__title">Bu sayfa yayından kalkmış görünüyor.</h1>
            <p class="page-hero__sub">Aradığınız içerik taşınmış olabilir. Anasayfadan veya blogdan devam edebilirsiniz.</p>
            <div class="hero-actions">
                <a href="{{ \App\Support\Locales::url('/') }}" class="btn btn-primary">Anasayfaya Dön</a>
                <a href="{{ \App\Support\Locales::url('/blog') }}" class="btn btn-ghost">Bloga Git</a>
            </div>
        </div>
    </section>
@endsection
