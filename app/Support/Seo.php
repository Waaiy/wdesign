<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * SEO verisini tek çatı altında toplar.
 *
 * Meta etiketleri, canonical, robots, Open Graph, Twitter Card, hreflang
 * alternatifleri, breadcrumb ve JSON-LD (schema.org) grafiklerini üretir.
 * Böylece her sayfa arama motorları için zengin ve tutarlı bir yapı sunar.
 */
class Seo
{
    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public static function make(array $input = []): array
    {
        $cfg = config('waaiy.seo', []);
        $siteName = $input['site_name'] ?? ($cfg['site_name'] ?? config('app.name'));

        $title = trim((string) ($input['title'] ?? '')) ?: $siteName;
        $appendSite = $input['append_site'] ?? true;
        if ($appendSite && $title !== $siteName && ! str_contains($title, $siteName)) {
            $title .= ' | '.$siteName;
        }

        $description = trim((string) ($input['description'] ?? '')) ?: ($cfg['description'] ?? '');
        $canonical = $input['canonical'] ?? url()->current();
        $robots = trim((string) ($input['robots'] ?? '')) ?: 'index,follow';
        $type = $input['type'] ?? 'website';
        $image = $input['image'] ?? ($cfg['organization']['logo'] ?? null);
        $image = $image ?: asset('images/og-default.jpg');

        $og = [
            'title' => $input['og_title'] ?? $title,
            'description' => $input['og_description'] ?? $description,
            'image' => $input['og_image'] ?? $image,
            'type' => $type === 'article' ? 'article' : $type,
            'url' => $canonical,
            'site_name' => $siteName,
            'locale' => $input['locale'] ?? ($cfg['locale'] ?? 'tr_TR'),
        ];

        return [
            'title' => $title,
            'description' => $description,
            'keywords' => $input['keywords'] ?? ($cfg['keywords'] ?? ''),
            'focus_keyword' => $input['focus_keyword'] ?? '',
            'canonical' => $canonical,
            'robots' => $robots,
            'type' => $type,
            'image' => $image,
            'published_time' => $input['published_time'] ?? null,
            'modified_time' => $input['modified_time'] ?? null,
            'twitter' => $input['twitter'] ?? ($cfg['twitter'] ?? ''),
            'locale' => $og['locale'],
            'og' => $og,
            'alternates' => $input['alternates'] ?? [],
            'breadcrumbs' => $input['breadcrumbs'] ?? [],
            'jsonld' => self::graphs($input, $og, $canonical, $image),
        ];
    }

    /**
     * Site geneli + içerik + breadcrumb grafiklerini birleştirir.
     *
     * @return array<int, array<string, mixed>>
     */
    protected static function graphs(array $input, array $og, string $canonical, ?string $image): array
    {
        // 1) Waaiy API zengin JSON-LD grafiği döndürdüyse onu kullanırız
        //    (panelden yönetilen Organization/FAQPage/BreadcrumbList dahil).
        if (! empty($input['jsonld']) && is_array($input['jsonld'])) {
            $apiGraphs = [];
            foreach ($input['jsonld'] as $graph) {
                if (is_object($graph)) {
                    $graph = json_decode(json_encode($graph), true);
                }
                if (is_array($graph) && $graph !== []) {
                    $apiGraphs[] = $graph;
                }
            }
            if ($apiGraphs !== []) {
                return $apiGraphs;
            }
        }

        // 2) API grafiği yoksa site geneli + içerik + breadcrumb üretiriz.
        $graphs = [
            self::organizationGraph(),
            self::websiteGraph(),
        ];

        if (! empty($input['content_type'])) {
            $graphs[] = self::contentGraph($input, $og, $canonical, $image);
        }

        if (! empty($input['breadcrumbs'])) {
            $graphs[] = self::breadcrumbGraph($input['breadcrumbs']);
        }

        return $graphs;
    }

    public static function organizationGraph(): array
    {
        $org = config('waaiy.seo.organization', []);
        $graph = [
            '@type' => 'Organization',
            '@id' => url('/').'#organization',
            'name' => $org['name'] ?? config('app.name'),
            'url' => url('/'),
        ];
        if (! empty($org['logo'])) {
            $graph['logo'] = ['@type' => 'ImageObject', 'url' => $org['logo']];
        }
        if (! empty($org['phone'])) {
            $graph['telephone'] = $org['phone'];
        }
        if (! empty($org['email'])) {
            $graph['email'] = $org['email'];
        }
        if (! empty($org['address'])) {
            $graph['address'] = ['@type' => 'PostalAddress', 'streetAddress' => $org['address']];
        }

        return $graph;
    }

    public static function websiteGraph(): array
    {
        return [
            '@type' => 'WebSite',
            '@id' => url('/').'#website',
            'url' => url('/'),
            'name' => config('waaiy.seo.site_name', config('app.name')),
            'inLanguage' => str_replace('_', '-', config('waaiy.seo.locale', 'tr_TR')),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => ['@type' => 'EntryPoint', 'urlTemplate' => url('/blog').'?q={search_term_string}'],
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    /**
     * @param  array<int, array{name:string, url?:string}>  $crumbs
     */
    public static function breadcrumbGraph(array $crumbs): array
    {
        $items = [];
        foreach (array_values($crumbs) as $i => $crumb) {
            $item = [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $crumb['name'] ?? '',
            ];
            if (! empty($crumb['url'])) {
                $item['item'] = $crumb['url'];
            }
            $items[] = $item;
        }

        return [
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items,
        ];
    }

    /**
     * Akordiyon/SSS içeriklerinden FAQPage grafiği üretir.
     *
     * @param  array<int, mixed>  $accordions
     */
    public static function faqGraph(array $accordions): ?array
    {
        $questions = [];
        foreach ($accordions as $item) {
            $item = (array) $item;
            $q = $item['title'] ?? $item['name'] ?? $item['_TITLE'] ?? $item['soru'] ?? null;
            $a = $item['content'] ?? $item['description'] ?? $item['_CONTENT'] ?? $item['cevap'] ?? null;
            if (is_array($q)) {
                $q = reset($q);
            }
            if (is_array($a)) {
                $a = reset($a);
            }
            if (! $q || ! $a) {
                continue;
            }
            $questions[] = [
                '@type' => 'Question',
                'name' => trim(strip_tags((string) $q)),
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => trim(strip_tags((string) $a)),
                ],
            ];
        }

        if ($questions === []) {
            return null;
        }

        return [
            '@type' => 'FAQPage',
            'mainEntity' => $questions,
        ];
    }

    /**
     * Ürün için Offer/AggregateRating içeren zengin Product grafiği.
     *
     * @param  array<string, mixed>  $product
     */
    public static function productGraph(array $product): array
    {
        $url = $product['url'] ?? url('/');
        $gallery = array_values(array_filter($product['gallery'] ?? []));

        $graph = [
            '@type' => 'Product',
            '@id' => $url.'#product',
            'name' => $product['title'] ?? '',
            'description' => $product['excerpt'] ?? '',
            'url' => $url,
            'brand' => ['@type' => 'Brand', 'name' => config('waaiy.seo.site_name', config('app.name'))],
        ];
        if (! empty($product['sku'])) {
            $graph['sku'] = (string) $product['sku'];
        }
        if ($gallery !== []) {
            $graph['image'] = $gallery;
        } elseif (! empty($product['cover'])) {
            $graph['image'] = $product['cover'];
        }
        if (! empty($product['rating'])) {
            $graph['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => (string) $product['rating'],
                'reviewCount' => (string) ($product['reviews'] ?? 1),
            ];
        }
        if (! empty($product['price'])) {
            $graph['offers'] = [
                '@type' => 'Offer',
                'url' => $url,
                'price' => number_format((float) $product['price'], 2, '.', ''),
                'priceCurrency' => $product['currency'] ?? 'TRY',
                'availability' => ($product['stock'] ?? 0) > 0
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
            ];
        }

        return $graph;
    }

    protected static function contentGraph(array $input, array $og, string $canonical, ?string $image): array
    {
        $type = match ($input['content_type']) {
            'article' => 'BlogPosting',
            'product' => 'Product',
            default => 'WebPage',
        };

        $graph = [
            '@type' => $type,
            '@id' => $canonical.'#content',
            'headline' => $input['title'] ?? '',
            'description' => $input['description'] ?? ($og['description'] ?? ''),
            'url' => $canonical,
            'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $canonical],
            'inLanguage' => str_replace('_', '-', $og['locale']),
        ];
        if ($image) {
            $graph['image'] = $image;
        }
        if (! empty($input['published_time'])) {
            $graph['datePublished'] = $input['published_time'];
        }
        if (! empty($input['modified_time'])) {
            $graph['dateModified'] = $input['modified_time'];
        }
        if ($type === 'BlogPosting') {
            $graph['author'] = ['@id' => url('/').'#organization'];
            $graph['publisher'] = ['@id' => url('/').'#organization'];
        }

        return $graph;
    }

    public static function excerpt(?string $html, int $length = 160): string
    {
        return Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags((string) $html))), $length);
    }
}
