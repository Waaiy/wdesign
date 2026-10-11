@if (! empty($seo['breadcrumbs']))
    <nav class="breadcrumbs" aria-label="Breadcrumb">
        <div class="container-x">
            <ol class="breadcrumbs__list">
                @foreach ($seo['breadcrumbs'] as $i => $crumb)
                    <li class="breadcrumbs__item">
                        @if (! empty($crumb['url']) && $i < count($seo['breadcrumbs']) - 1)
                            <a href="{{ $crumb['url'] }}">{{ $crumb['name'] }}</a>
                        @else
                            <span aria-current="page">{{ $crumb['name'] }}</span>
                        @endif
                        @if ($i < count($seo['breadcrumbs']) - 1)
                            <svg class="breadcrumbs__sep" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m9 6 6 6-6 6"/></svg>
                        @endif
                    </li>
                @endforeach
            </ol>
        </div>
    </nav>
@endif
