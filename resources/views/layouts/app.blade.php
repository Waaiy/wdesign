<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $currentLang ?? 'tr') }}"
      data-header="{{ \App\Support\Design::DEFAULT_HEADER }}"
      data-blog="{{ \App\Support\Design::DEFAULT_BLOG_LAYOUT }}"
      data-sector="{{ \App\Support\Design::DEFAULT_SECTOR }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('partials.seo', ['seo' => $seo])

    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>&#127760;</text></svg>">

    @include('partials.design-boot')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="waaiy-body antialiased">
    <a href="#main" class="skip-link">İçeriğe geç</a>

    @include('partials.header')

    <main id="main">
        @yield('content')
    </main>

    @include('partials.footer')
    @include('partials.customizer')

    @include('partials.waaiy-debug')

    @stack('scripts')
</body>
</html>
