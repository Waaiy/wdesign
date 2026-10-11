<?php

namespace App\Http\Controllers;

use App\Services\Waaiy\ContentRepository;
use App\Support\Locales;
use App\Support\Seo;

class PageController extends Controller
{
    public function show(ContentRepository $waaiy)
    {
        return $this->renderPage($waaiy, (string) request()->route('slug'));
    }

    /** Sayfa gövdesini oluşturur (route hedefi ve dahili çağrılar için). */
    public function renderPage(ContentRepository $waaiy, string $slug)
    {
        $page = $waaiy->page($slug);
        if (! $page) {
            abort(404);
        }

        $canonical = Locales::url('/'.$page['slug']);
        $override = $page['seo'] ?? [];

        $jsonld = [];
        if (! empty($page['jsonld'])) {
            $jsonld[] = $page['jsonld'];
        }
        if ($faq = Seo::faqGraph($page['accordions'] ?? [])) {
            $jsonld[] = $faq;
        }

        $seo = Seo::make([
            'title' => ($override['og_title'] ?? '') ?: $page['title'],
            'description' => ($override['og_description'] ?? '') ?: $page['excerpt'],
            'focus_keyword' => $override['focus_keyword'] ?? '',
            'canonical' => ($override['canonical'] ?? '') ?: $canonical,
            'robots' => ($override['robots'] ?? '') ?: 'index,follow',
            'image' => $page['cover'],
            'og_image' => ($override['og_image'] ?? '') ?: $page['cover'],
            'type' => 'website',
            'content_type' => 'page',
            'jsonld' => $jsonld,
            'alternates' => Locales::alternatesForCurrent(),
            'breadcrumbs' => [
                ['name' => 'Anasayfa', 'url' => route('home')],
                ['name' => $page['title']],
            ],
        ]);

        return view('pages.page', [
            'page' => $page,
            'seo' => $seo,
        ]);
    }

    /**
     * İletişim sayfası.
     * CMS'te "iletisim" sayfası varsa onu gösterir; yoksa ayarlardaki
     * telefon/e-posta/adres ve form ile yerleşik iletişim sayfasını döner.
     * Böylece header/footer'daki iletişim bağlantısı hiçbir zaman 404 olmaz.
     */
    public function contact(ContentRepository $waaiy)
    {
        if ($waaiy->page('iletisim')) {
            return $this->renderPage($waaiy, 'iletisim');
        }

        $canonical = route('contact');
        $seo = Seo::make([
            'title' => 'İletişim',
            'description' => 'Bize ulaşın: iletişim formu, telefon, e-posta ve adres bilgileri.',
            'canonical' => $canonical,
            'type' => 'website',
            'content_type' => 'page',
            'alternates' => Locales::alternatesForCurrent(),
            'breadcrumbs' => [
                ['name' => 'Anasayfa', 'url' => route('home')],
                ['name' => 'İletişim'],
            ],
        ]);

        return view('pages.contact', [
            'seo' => $seo,
        ]);
    }
}
