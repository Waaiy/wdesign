@extends('layouts.app')

@section('content')
    <article class="article">
        <header class="page-hero page-hero--article">
            <div class="container-x page-hero__inner">
                <div class="page-hero__copy">
                    @if (! empty($post['category']))
                        <a href="{{ route('blog.category', ['slug' => $post['category']['slug']]) }}" class="chip chip--brand">{{ $post['category']['name'] }}</a>
                    @endif
                    <h1 class="page-hero__title">{{ $post['title'] }}</h1>
                    <p class="page-hero__sub">{{ $post['excerpt'] }}</p>
                    <div class="article-meta">
                        <span class="article-meta__author">{{ $post['author'] }}</span>
                        <span aria-hidden="true">·</span>
                        <time datetime="{{ $post['created_at'] }}">{{ \Illuminate\Support\Carbon::parse($post['created_at'])->format('d.m.Y') }}</time>
                        <span aria-hidden="true">·</span>
                        <span>{{ $post['reading_time'] }} dk okuma</span>
                    </div>
                </div>
            </div>
        </header>

        @include('partials.breadcrumbs', ['seo' => $seo])

        @if (! empty($post['cover']))
            <div class="container-x">
                <figure class="article-cover">
                    <img src="{{ $post['cover'] }}" alt="{{ $post['title'] }}" width="1600" height="900" fetchpriority="high">
                </figure>
            </div>
        @endif

        <div class="container-x article-layout">
            <div class="article-main">
                <div class="prose" data-article-content>
                    {!! $post['content'] !!}
                </div>

                @if (! empty($post['tags']))
                    <div class="article-tags">
                        @foreach ($post['tags'] as $tag)
                            <span class="tag">#{{ $tag }}</span>
                        @endforeach
                    </div>
                @endif

                @if (! empty($post['accordions']))
                    <section class="accordions">
                        <h2 class="block-title">Sık Sorulan Sorular</h2>
                        @foreach ($post['accordions'] as $acc)
                            @php $acc = (array) $acc; @endphp
                            <details class="accordion">
                                <summary>{{ $acc['title'] ?? $acc['_TITLE'] ?? '' }}</summary>
                                <div class="accordion__body">{!! $acc['content'] ?? $acc['_CONTENT'] ?? '' !!}</div>
                            </details>
                        @endforeach
                    </section>
                @endif

                <section class="comments">
                    <h2 class="block-title">Yorum Yap</h2>
                    @if (session('status'))
                        <p class="form-note form-note--ok">{{ session('status') }}</p>
                    @endif
                    @if (session('error'))
                        <p class="form-note form-note--err">{{ session('error') }}</p>
                    @endif
                    <form method="post" action="{{ route('comment') }}" class="form-grid">
                        @csrf
                        <input type="hidden" name="type" value="blog">
                        <input type="hidden" name="id" value="{{ $post['id'] }}">
                        <input type="text" name="name" required placeholder="Adınız" class="field">
                        <input type="email" name="email" required placeholder="E-posta" class="field">
                        <textarea name="message" rows="4" required placeholder="Yorumunuz" class="field field--full"></textarea>
                        <button type="submit" class="btn btn-primary">Gönder</button>
                    </form>
                </section>
            </div>

            <aside class="article-side">
                <div class="toc" data-toc hidden>
                    <h2 class="block-title">İçindekiler</h2>
                    <ol class="toc__list"></ol>
                </div>
                <div class="side-card">
                    <h2 class="block-title">Bu yazıyı paylaş</h2>
                    @php $shareUrl = urlencode($seo['canonical']); @endphp
                    <div class="share">
                        <a href="https://x.com/intent/tweet?url={{ $shareUrl }}&text={{ urlencode($post['title']) }}" target="_blank" rel="noopener">X</a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}" target="_blank" rel="noopener">LinkedIn</a>
                        <a href="https://wa.me/?text={{ urlencode($post['title'].' '.$seo['canonical']) }}" target="_blank" rel="noopener">WhatsApp</a>
                    </div>
                </div>
            </aside>
        </div>
    </article>

    @if (! empty($related))
        <section class="section section--alt">
            <div class="container-x">
                <header class="section-head">
                    <p class="eyebrow"><span class="eyebrow__dot"></span>İlgili</p>
                    <h2 class="section-title">Bunlar da ilginizi çekebilir</h2>
                </header>
                @include('partials.blog.grid', ['posts' => $related])
            </div>
        </section>
    @endif
@endsection
