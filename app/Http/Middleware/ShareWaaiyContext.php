<?php

namespace App\Http\Middleware;

use App\Services\Waaiy\ContentRepository;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Waaiy ayarlarını istek başında yükleyip uygulama geneline yayar.
 * Böylece SEO etiketleri (site adı, telefon, e-posta, adres) panelden
 * yönetilen değerlerle üretilir.
 */
class ShareWaaiyContext
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $settings = app(ContentRepository::class)->settings();
        } catch (Throwable $e) {
            $settings = [];
        }

        if (! empty($settings['site_basligi'])) {
            config([
                'app.name' => $settings['site_basligi'],
                'waaiy.seo.site_name' => $settings['site_basligi'],
            ]);
        }

        $org = config('waaiy.seo.organization', []);
        if (! empty($settings['eposta'])) {
            $org['email'] = $settings['eposta'];
        }
        if (! empty($settings['telefon'])) {
            $org['phone'] = $settings['telefon'];
        }
        if (! empty($settings['adres'])) {
            $org['address'] = $settings['adres'];
        }
        config(['waaiy.seo.organization' => $org]);

        return $next($request);
    }
}
