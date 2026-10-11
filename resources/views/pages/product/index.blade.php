@extends('layouts.app')

@section('content')
    <section class="page-hero">
        <div class="container-x">
            <p class="eyebrow"><span class="eyebrow__dot"></span>{{ $category ? 'Kategori' : 'Mağaza' }}</p>
            <h1 class="page-hero__title">{{ $category ? $category['name'].' Ürünleri' : 'Öne çıkan ürünler' }}</h1>
            <p class="page-hero__sub">
                {{ $category
                    ? $category['name'].' kategorisindeki tüm ürünler.'
                    : 'Seçili ürünler, güncel fiyatlar, teknik özellikler ve hızlı kargo.' }}
            </p>
        </div>
    </section>

    @include('partials.breadcrumbs', ['seo' => $seo])

    <section class="section">
        <div class="container-x">
            <div class="filter-bar">
                <a href="{{ route('product.index') }}" class="chip {{ ! $category ? 'is-active' : '' }}">Tümü</a>
                @foreach ($waaiy->productCategories() as $cat)
                    <a href="{{ route('product.category', ['slug' => $cat['slug']]) }}"
                       class="chip {{ $category && $category['slug'] === $cat['slug'] ? 'is-active' : '' }}">
                        {{ $cat['name'] }}
                    </a>
                @endforeach
            </div>

            @include('partials.product.grid', ['products' => $products])

            @php
                $lastPage = max(1, (int) ceil($total / $limit));
                $buildUrl = fn ($p) => $categorySlug !== ''
                    ? route('product.category', ['slug' => $categorySlug, 'page' => $p])
                    : route('product.index', ['page' => $p]);
            @endphp
            @if ($lastPage > 1)
                <nav class="pager" aria-label="Sayfalama">
                    @if ($page > 1)
                        <a href="{{ $buildUrl($page - 1) }}" class="pager__btn" rel="prev">← Önceki</a>
                    @else
                        <span class="pager__btn is-disabled">← Önceki</span>
                    @endif
                    <span class="pager__info">{{ $page }} / {{ $lastPage }}</span>
                    @if ($page < $lastPage)
                        <a href="{{ $buildUrl($page + 1) }}" class="pager__btn" rel="next">Sonraki →</a>
                    @else
                        <span class="pager__btn is-disabled">Sonraki →</span>
                    @endif
                </nav>
            @endif
        </div>
    </section>
@endsection
