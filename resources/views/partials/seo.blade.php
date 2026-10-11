{{--
    SEO: meta etiketleri, canonical, robots, Open Graph, Twitter Card, hreflang
    alternatifleri ve JSON-LD (schema.org) grafikleri.
--}}
<title>{{ $seo['title'] }}</title>
<meta name="description" content="{{ $seo['description'] }}">
@if (! empty($seo['keywords']))
    <meta name="keywords" content="{{ $seo['keywords'] }}">
@endif
<meta name="robots" content="{{ $seo['robots'] }}">
<meta name="author" content="{{ $siteName }}">
<link rel="canonical" href="{{ $seo['canonical'] }}">

@foreach ($seo['alternates'] as $hreflang => $href)
    <link rel="alternate" hreflang="{{ $hreflang }}" href="{{ $href }}">
@endforeach

{{-- Open Graph --}}
<meta property="og:type" content="{{ $seo['og']['type'] }}">
<meta property="og:site_name" content="{{ $seo['og']['site_name'] }}">
<meta property="og:title" content="{{ $seo['og']['title'] }}">
<meta property="og:description" content="{{ $seo['og']['description'] }}">
<meta property="og:url" content="{{ $seo['og']['url'] }}">
<meta property="og:locale" content="{{ $seo['og']['locale'] }}">
@if ($seo['image'])
    <meta property="og:image" content="{{ $seo['image'] }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
@endif
@if ($seo['published_time'])
    <meta property="article:published_time" content="{{ $seo['published_time'] }}">
@endif
@if ($seo['modified_time'])
    <meta property="article:modified_time" content="{{ $seo['modified_time'] }}">
@endif

{{-- Twitter Card --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seo['og']['title'] }}">
<meta name="twitter:description" content="{{ $seo['og']['description'] }}">
@if ($seo['image'])
    <meta name="twitter:image" content="{{ $seo['image'] }}">
@endif
@if ($seo['twitter'])
    <meta name="twitter:site" content="{{ $seo['twitter'] }}">
@endif

<meta name="theme-color" content="#5b2ee6">

{{-- Yapılandırılmış veri (JSON-LD) --}}
@foreach ($seo['jsonld'] as $graph)
    @php
        $graph = is_array($graph) ? (['@context' => 'https://schema.org'] + $graph) : $graph;
    @endphp
    <script type="application/ld+json">{!! json_encode($graph, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
@endforeach
