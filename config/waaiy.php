<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Waaiy API Kök Adresi
    |--------------------------------------------------------------------------
    | Waaiy headless CMS'in public REST API'sinin kök adresi. SDK bu adrese
    | üç özel header (W-Public-Key / W-Private-Key / W-User-Key) ile bağlanır.
    */
    'base_url' => env('WAAIY_API_URL', 'https://api.waaiy.dev'),

    /*
    |--------------------------------------------------------------------------
    | Kimlik Anahtarları
    |--------------------------------------------------------------------------
    | Üç anahtar da dolu değilse uygulama otomatik olarak "tasarım/demo"
    | moduna düşer ve örnek içerikle çalışır. Böylece tasarım anahtar olmadan
    | da eksiksiz görüntülenebilir.
    */
    'keys' => [
        'public' => env('WAAIY_PUBLIC_KEY', ''),
        'private' => env('WAAIY_PRIVATE_KEY', ''),
        'user' => env('WAAIY_USER_KEY', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Çalışma Modu
    |--------------------------------------------------------------------------
    | auto  -> Üç anahtar da doluysa GERÇEK API kullanılır, değilse demo.
    |          (Anahtar girildiğinde otomatik olarak canlıya geçer.)
    | live  -> Her zaman API denenir (anahtar yoksa güvenli şekilde demoya düşer).
    | demo  -> Her zaman örnek içerik kullanılır (sunum/geliştirme).
    */
    'mode' => env('WAAIY_MODE', 'auto'),

    /*
    |--------------------------------------------------------------------------
    | Önyüz Çevirileri (auto register)
    |--------------------------------------------------------------------------
    | true ise anasayfa gibi tasarım metinleri translation() ile okunur;
    | anahtar panelde yoksa verilen varsayılan metinle OTOMATİK kaydedilir
    | (get-or-create). Böylece içerik hem panelde oluşur hem oradan gösterilir.
    */
    'auto_register_translations' => (bool) env('WAAIY_TRANSLATIONS_AUTOREGISTER', true),

    /*
    |--------------------------------------------------------------------------
    | Medya URL Öneki
    |--------------------------------------------------------------------------
    | Waaiy media() ucu, medya id'sini tam URL'e çevirmek için bir URL öneki
    | bekler. CDN domaininizi buraya yazın.
    */
    'media_url' => env('WAAIY_MEDIA_URL', ''),

    /*
    |--------------------------------------------------------------------------
    | Menü Ayarları
    |--------------------------------------------------------------------------
    */
    'menu' => env('WAAIY_MENU_SLUG', 'ana-menu'),

    /*
    |--------------------------------------------------------------------------
    | URL Önekleri
    |--------------------------------------------------------------------------
    | menuitems() ucuna gönderilir; menüde üretilen URL'ler bu öneklerle kurulur.
    */
    'prefixes' => [
        'page' => 'page',
        'blog_category' => 'blogs',
        'product_category' => 'urunler',
    ],

    /*
    |--------------------------------------------------------------------------
    | Önbellek
    |--------------------------------------------------------------------------
    | API yanıtları bu süre boyunca cache'lenir; 429 (istek sınırı) riskini
    | azaltır ve sayfa hızını artırır (Core Web Vitals / SEO).
    */
    'cache' => [
        'enabled' => (bool) env('WAAIY_CACHE', true),
        'ttl' => (int) env('WAAIY_CACHE_TTL', 300),
        'prefix' => 'waaiy:',
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP Ayarları
    |--------------------------------------------------------------------------
    */
    'timeout' => (int) env('WAAIY_TIMEOUT', 20),
    'connect_timeout' => (int) env('WAAIY_CONNECT_TIMEOUT', 10),

    /*
    |--------------------------------------------------------------------------
    | SEO Varsayılanları
    |--------------------------------------------------------------------------
    | API'den gelen SEO bloğu boş olduğunda kullanılacak site geneli değerler.
    */
    'seo' => [
        'site_name' => env('WAAIY_SITE_NAME', env('APP_NAME', 'Waaiy Design')),
        'title_suffix' => env('WAAIY_TITLE_SUFFIX', ''),
        'description' => env('WAAIY_SITE_DESCRIPTION', 'Waaiy headless CMS ile geliştirilmiş, çok varyasyonlu ve SEO odaklı modern web tasarımı.'),
        'keywords' => env('WAAIY_SITE_KEYWORDS', 'headless cms, waaiy, laravel, seo, web tasarımı'),
        'twitter' => env('WAAIY_TWITTER', ''),
        'locale' => env('WAAIY_LOCALE', 'tr_TR'),
        'organization' => [
            'name' => env('WAAIY_ORG_NAME', env('APP_NAME', 'Waaiy Design')),
            'logo' => env('WAAIY_ORG_LOGO', ''),
            'phone' => env('WAAIY_ORG_PHONE', ''),
            'email' => env('WAAIY_ORG_EMAIL', ''),
            'address' => env('WAAIY_ORG_ADDRESS', ''),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | İçerik Yönetim Sistemi Atıf Bağlantısı
    |--------------------------------------------------------------------------
    | Footer / copyrights bölümünde gösterilir.
    */
    'cms' => [
        'name' => 'Waaiy',
        'url' => env('WAAIY_CMS_URL', 'https://waaiy.dev'),
        'label' => 'içerik yönetim sistemi',
    ],

];
