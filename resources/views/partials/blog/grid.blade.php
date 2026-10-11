{{-- Ortak kart markaşu; görünüm (ızgra/liste/dergi) CSS ile değişir. --}}
<div class="post-grid" data-post-grid>
    @forelse ($posts as $post)
        @include('partials.blog.card', ['post' => $post])
    @empty
        <p class="empty-state">Henüz yayınlanmış bir yazı bulunmuyor.</p>
    @endforelse
</div>
