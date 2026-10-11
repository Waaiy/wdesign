<article class="post-card">
    <a href="{{ route('blog.show', $post['slug']) }}" class="post-card__media" tabindex="-1" aria-hidden="true">
        @if (! empty($post['cover']))
            <img src="{{ $post['cover'] }}" alt="{{ $post['title'] }}" loading="lazy" decoding="async" width="800" height="500">
        @else
            <span class="post-card__ph"></span>
        @endif
        @if (! empty($post['category']))
            <span class="post-card__cat">{{ $post['category']['name'] }}</span>
        @endif
    </a>
    <div class="post-card__body">
        <div class="post-card__meta">
            <time datetime="{{ $post['created_at'] }}">{{ \Illuminate\Support\Carbon::parse($post['created_at'])->format('d.m.Y') }}</time>
            <span aria-hidden="true">·</span>
            <span>{{ $post['reading_time'] }} dk okuma</span>
        </div>
        <h3 class="post-card__title">
            <a href="{{ route('blog.show', $post['slug']) }}">{{ $post['title'] }}</a>
        </h3>
        <p class="post-card__excerpt">{{ $post['excerpt'] }}</p>
        <span class="post-card__more">
            Devamını oku
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </span>
    </div>
</article>
