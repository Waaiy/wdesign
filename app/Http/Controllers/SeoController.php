<?php

namespace App\Http\Controllers;

use App\Services\Waaiy\ContentRepository;

/**
 * Sitemap, robots.txt ve sitemap parçalarını Waaiy API'sinden proxy'ler.
 *
 * Arama motorları API anahtarlarını gönderemeyeceği için bu uçlar frontend
 * üzerinden servis edilir.
 */
class SeoController extends Controller
{
    public function sitemap(ContentRepository $waaiy)
    {
        return response($waaiy->sitemap(), 200)
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }

    public function sitemapPart(ContentRepository $waaiy, string $type, int $page)
    {
        $allowed = ['anasayfa', 'sayfa', 'blog', 'urun', 'blog_kategori', 'urun_kategori'];
        if (! in_array($type, $allowed, true)) {
            abort(404);
        }

        return response($waaiy->sitemapPart($type, $page), 200)
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }

    public function robots(ContentRepository $waaiy)
    {
        return response($waaiy->robots(), 200)
            ->header('Content-Type', 'text/plain; charset=utf-8');
    }
}
