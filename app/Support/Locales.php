<?php

namespace App\Support;

use App\Services\Waaiy\ContentRepository;

/**
 * Dil (locale) yardımcıları.
 *
 * Kısa dil adı (`tr`, `en` …) URL ön eki olarak kullanılır:
 *   /tr/hakkimizda, /en/blog ...
 * Dil listesi Waaiy'in `languages()` ucundan gelir; kısa adlar baz alınır.
 */
class Locales
{
    /** Aktif dilin kısa adı. */
    public static function current(): string
    {
        return session('waaiy_lang')
            ?: (self::all()[0]['short'] ?? 'tr');
    }

    /** @return array<int, array<string, mixed>> */
    public static function all(): array
    {
        try {
            return app(ContentRepository::class)->languages();
        } catch (\Throwable $e) {
            return [['id' => 1, 'name' => 'Türkçe', 'short' => 'tr', 'is_default' => true]];
        }
    }

    /** Varsayılan dilin kısa adı (listenin ilki). */
    public static function default(): string
    {
        return self::all()[0]['short'] ?? 'tr';
    }

    /**
     * Uygulama içi bir yolu dil önekiyle birlikte tam URL'e çevirir.
     * Dış bağlantılar (http, mailto, tel, #) olduğu gibi bırakılır.
     */
    public static function url(string $path): string
    {
        if ($path === '' || preg_match('#^([a-z][a-z0-9+.-]*:)?//#i', $path) || str_starts_with($path, '#')) {
            return $path === '' ? url('/') : $path;
        }

        $locale = self::current();
        $clean = '/'.ltrim($path, '/');

        if ($clean === '/') {
            return url('/'.$locale);
        }

        // Zaten dil önekiyle başlıyorsa tekrar ekleme.
        if (preg_match('#^/'.preg_quote($locale, '#').'(/|$)#', $clean)) {
            return url($clean);
        }

        return url('/'.$locale.$clean);
    }

    /** Aktif yolun dil öneki çıkarılmış hali (ör. /tr/blog → /blog). */
    public static function path(): string
    {
        $path = '/'.ltrim(request()->path(), '/');
        $locale = request()->route('locale') ?: self::current();

        if ($locale && str_starts_with($path, '/'.$locale)) {
            $path = substr($path, strlen('/'.$locale));
        }

        return $path === '' ? '/' : $path;
    }

    /**
     * Tüm aktif diller için mevcut sayfanın hreflang alternatifleri.
     *
     * @return array<string, string>
     */
    public static function alternatesForCurrent(): array
    {
        $path = self::path();
        $suffix = $path === '/' ? '' : $path;

        $out = [];
        foreach (self::all() as $lang) {
            $out[$lang['short']] = url('/'.$lang['short'].$suffix);
        }
        $out['x-default'] = url('/'.self::default().$suffix);

        return $out;
    }
}
