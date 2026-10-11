<?php

namespace App\Http\Middleware;

use App\Services\Waaiy\ContentRepository;
use App\Support\Locales;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * URL ön ekini ({locale}) aktif dile bağlar.
 *
 * - Dil kısa adını (küçük harfe çevirerek) doğrular; geçersizse 404.
 * - Oturuma waaiy_lang / waaiy_lang_id yazar.
 * - route() varsayılan locale parametresini önce ayarlar; böylece 404 sayfasında
 *   da route() çalışır.
 */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $requested = strtolower((string) ($request->route('locale') ?? ''));
        $fallback = strtolower((string) session('waaiy_lang')) ?: Locales::default();
        $locale = $requested !== '' ? $requested : $fallback;

        // Önce varsayılanı ayarla (hata/404 sayfasında da route() çalışsın).
        URL::defaults(['locale' => $locale]);
        View::share('currentLang', $locale);

        if ($requested !== '') {
            $languages = app(ContentRepository::class)->languages();
            $codes = array_map(fn ($l) => strtolower((string) $l['short']), $languages);

            if ($codes !== [] && ! in_array($requested, $codes, true)) {
                URL::defaults(['locale' => $fallback]);
                View::share('currentLang', $fallback);
                abort(404);
            }

            $lang = collect($languages)->first(fn ($l) => strtolower((string) $l['short']) === $requested);
            session([
                'waaiy_lang' => $requested,
                'waaiy_lang_id' => $lang['id'] ?? null,
            ]);
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
