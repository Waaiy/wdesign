<div class="product-grid">
    @forelse ($products as $product)
        @include('partials.product.card', ['product' => $product])
    @empty
        <p class="empty-state">Henüz yayınlanmış bir ürün bulunmuyor.</p>
    @endforelse
</div>
