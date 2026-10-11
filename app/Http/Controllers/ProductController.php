<?php

namespace App\Http\Controllers;

use App\Services\Waaiy\ContentRepository;
use App\Support\Locales;
use App\Support\Seo;

class ProductController extends Controller
{
    public function index(ContentRepository $waaiy)
    {
        return $this->listing($waaiy, '');
    }

    public function category(ContentRepository $waaiy)
    {
        return $this->listing($waaiy, (string) request()->route('slug'));
    }

    protected function listing(ContentRepository $waaiy, string $categorySlug)
    {
        $page = max(1, (int) request()->integer('page', 1));
        $limit = 12;
        $result = $waaiy->products($categorySlug, $page, $limit);

        $category = $categorySlug !== '' ? $result['category'] : null;
        $title = $category ? $category['name'].' Ürünleri' : 'Ürünler';
        $canonical = $categorySlug !== ''
            ? route('product.category', ['slug' => $categorySlug, 'page' => $page])
            : route('product.index', ['page' => $page]);

        $breadcrumbs = [
            ['name' => 'Anasayfa', 'url' => route('home')],
            ['name' => 'Ürünler', 'url' => route('product.index')],
        ];
        if ($category) {
            $breadcrumbs[] = ['name' => $category['name']];
        }

        $seo = Seo::make([
            'title' => $title,
            'description' => $category
                ? $category['name'].' kategorisindeki tüm ürünler, fiyatlar ve detaylar.'
                : 'Öne çıkan ürünler, güncel fiyatlar, teknik özellikler ve hızlı kargo.',
            'canonical' => $canonical,
            'type' => 'website',
            'breadcrumbs' => $breadcrumbs,
            'alternates' => Locales::alternatesForCurrent(),
        ]);

        return view('pages.product.index', [
            'products' => $result['items'],
            'total' => $result['total'],
            'category' => $category,
            'categorySlug' => $categorySlug,
            'page' => $page,
            'limit' => $limit,
            'seo' => $seo,
        ]);
    }

    public function show(ContentRepository $waaiy)
    {
        $slug = (string) request()->route('slug');
        $product = $waaiy->product($slug);
        if (! $product) {
            abort(404);
        }

        $product['url'] = route('product.show', $product['slug']);

        $related = array_values(array_filter(
            $waaiy->products('', 1, 8)['items'],
            fn ($p) => $p['slug'] !== $product['slug']
        ));
        $related = array_slice($related, 0, 4);

        $canonical = $product['url'];
        $override = $product['seo'] ?? [];

        $breadcrumbs = [
            ['name' => 'Anasayfa', 'url' => route('home')],
            ['name' => 'Ürünler', 'url' => route('product.index')],
            ['name' => $product['title']],
        ];

        // Waaiy JSON-LD'si varsa onu; yoksa ürün grafiğini kendimiz üretiriz.
        $jsonld = [];
        if (! empty($product['jsonld'])) {
            $jsonld[] = $product['jsonld'];
        } else {
            $jsonld[] = Seo::productGraph($product);
            $jsonld[] = Seo::breadcrumbGraph($breadcrumbs);
        }
        if ($faq = Seo::faqGraph($product['accordions'] ?? [])) {
            $jsonld[] = $faq;
        }

        $seo = Seo::make([
            'title' => ($override['og_title'] ?? '') ?: $product['title'],
            'description' => ($override['og_description'] ?? '') ?: $product['excerpt'],
            'focus_keyword' => $override['focus_keyword'] ?? '',
            'canonical' => ($override['canonical'] ?? '') ?: $canonical,
            'robots' => ($override['robots'] ?? '') ?: 'index,follow',
            'image' => $product['cover'],
            'og_image' => ($override['og_image'] ?? '') ?: $product['cover'],
            'type' => 'product',
            'content_type' => 'product',
            'keywords' => implode(', ', $product['tags'] ?? []),
            'modified_time' => $product['updated_at'],
            'alternates' => Locales::alternatesForCurrent(),
            'jsonld' => $jsonld,
            'breadcrumbs' => $breadcrumbs,
        ]);

        return view('pages.product.show', [
            'product' => $product,
            'related' => $related,
            'seo' => $seo,
        ]);
    }
}
