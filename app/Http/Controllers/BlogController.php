<?php

namespace App\Http\Controllers;

use App\Services\Waaiy\ContentRepository;
use App\Support\Design;
use App\Support\Locales;
use App\Support\Seo;

class BlogController extends Controller
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
        $limit = 9;
        $result = $waaiy->blogs($categorySlug, $page, $limit);

        // "Tümü" görünümünde (kategori slug'ı boş) API kategori döndürse bile
        // kategori başlığı gösterilmez; aksi halde {slug} boş kalır.
        $category = $categorySlug !== '' ? $result['category'] : null;
        $title = $category ? $category['name'].' Yazıları' : 'Blog';
        $canonical = $category
            ? route('blog.category', ['slug' => $categorySlug, 'page' => $page])
            : route('blog.index', ['page' => $page]);

        $breadcrumbs = [
            ['name' => 'Anasayfa', 'url' => route('home')],
            ['name' => 'Blog', 'url' => route('blog.index')],
        ];
        if ($category) {
            $breadcrumbs[] = ['name' => $category['name']];
        }

        $seo = Seo::make([
            'title' => $title,
            'description' => $category
                ? $category['name'].' kategorisindeki tüm yazılar ve rehberler.'
                : 'Headless CMS, SEO, tasarım ve teknoloji üzerine güncel yazılar.',
            'canonical' => $canonical,
            'type' => 'website',
            'breadcrumbs' => $breadcrumbs,
            'alternates' => Locales::alternatesForCurrent(),
        ]);

        return view('pages.blog.index', [
            'posts' => $result['items'],
            'total' => $result['total'],
            'category' => $category,
            'categorySlug' => $categorySlug,
            'page' => $page,
            'limit' => $limit,
            'seo' => $seo,
            'blogLayout' => Design::DEFAULT_BLOG_LAYOUT,
        ]);
    }

    public function show(ContentRepository $waaiy)
    {
        $slug = (string) request()->route('slug');
        $blog = $waaiy->blog($slug);
        if (! $blog) {
            abort(404);
        }

        $related = array_values(array_filter(
            $waaiy->blogs('', 1, 6)['items'],
            fn ($b) => $b['slug'] !== $blog['slug']
        ));
        $related = array_slice($related, 0, 3);

        $canonical = route('blog.show', $blog['slug']);
        $override = $blog['seo'] ?? [];

        $jsonld = [];
        if (! empty($blog['jsonld'])) {
            $jsonld[] = $blog['jsonld'];
        }
        if ($faq = Seo::faqGraph($blog['accordions'] ?? [])) {
            $jsonld[] = $faq;
        }

        $seo = Seo::make([
            'title' => ($override['og_title'] ?? '') ?: $blog['title'],
            'description' => ($override['og_description'] ?? '') ?: $blog['excerpt'],
            'focus_keyword' => $override['focus_keyword'] ?? '',
            'canonical' => ($override['canonical'] ?? '') ?: $canonical,
            'robots' => ($override['robots'] ?? '') ?: 'index,follow',
            'image' => $blog['cover'],
            'og_image' => ($override['og_image'] ?? '') ?: $blog['cover'],
            'type' => 'article',
            'content_type' => 'article',
            'keywords' => implode(', ', $blog['tags'] ?? []),
            'published_time' => $blog['created_at'],
            'modified_time' => $blog['updated_at'],
            'alternates' => Locales::alternatesForCurrent(),
            'jsonld' => $jsonld,
            'breadcrumbs' => [
                ['name' => 'Anasayfa', 'url' => route('home')],
                ['name' => 'Blog', 'url' => route('blog.index')],
                ['name' => $blog['title']],
            ],
        ]);

        return view('pages.blog.show', [
            'post' => $blog,
            'related' => $related,
            'seo' => $seo,
        ]);
    }
}
