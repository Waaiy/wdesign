<?php

namespace App\Services\Waaiy;

use App\Support\Seo;
use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Throwable;

/**
 * Waaiy içeriğine tek erişim noktası.
 *
 * - API yanıtlarını şablonların beklediği sade yapıya normalize eder.
 * - Önbellekler (429 riskini azaltır, sayfa hızını artırır).
 * - Hata durumunda ya da demo modunda DemoContent'e düşer.
 */
class ContentRepository
{
    /** Canlı API çağrısı başarısız olup güvenli varsayılana düşüldü mü? */
    protected bool $fellBack = false;

    protected ?string $lastError = null;

    /** Auto-register bu istekte hata aldıysa tekrar denemeyi durdurur. */
    protected bool $autoRegisterBroken = false;

    public function __construct(
        protected WaaiyClient $client,
        protected DemoContent $demo,
    ) {}

    public function fellBack(): bool
    {
        return $this->fellBack;
    }

    public function lastError(): ?string
    {
        return $this->lastError;
    }

    public function demo(): bool
    {
        return $this->client->demo();
    }

    public function mediaUrl(): string
    {
        return $this->client->mediaUrl();
    }

    // -----------------------------------------------------------------
    // Ayarlar & menü
    // -----------------------------------------------------------------

    /** @return array<string, mixed> */
    public function settings(): array
    {
        if ($this->demo()) {
            return $this->normalizeSettings($this->demo->settings());
        }

        return $this->remember('settings', function () {
            try {
                return $this->normalizeSettings((array) $this->api()->settings());
            } catch (Throwable $e) {
                $this->report($e);

                return $this->normalizeSettings($this->demo->settings());
            }
        });
    }

    /**
     * Waaiy ayar anahtarlarını şablonların beklediği ortak yapıya çevirir
     * (telefon/eposta/sosyal gibi farklı isimlendirmeleri eşitler).
     *
     * @param  array<string, mixed>  $raw
     * @return array<string, mixed>
     */
    protected function normalizeSettings(array $raw): array
    {
        $raw['telefon'] = $raw['telefon'] ?? $raw['iletisim_telefon'] ?? '';
        $raw['eposta'] = $raw['eposta'] ?? $raw['iletisim_eposta'] ?? '';
        $raw['adres'] = $raw['adres'] ?? '';

        $sosyal = is_array($raw['sosyal'] ?? null) ? $raw['sosyal'] : [];
        $map = [
            'facebook' => 'facebook', 'instagram' => 'instagram',
            'twitter' => 'x', 'x' => 'x', 'linkedin' => 'linkedin', 'youtube' => 'youtube',
        ];
        foreach ($map as $source => $name) {
            if (! empty($raw[$source]) && empty($sosyal[$name])) {
                $sosyal[$name] = $raw[$source];
            }
        }
        $raw['sosyal'] = $sosyal;

        if (empty($raw['slogan'])) {
            $footer = $raw['footer_metni'] ?? null;
            if (is_string($footer) && $footer !== '') {
                $raw['slogan'] = $footer;
            }
        }

        return $raw;
    }

    public function setting(string $key, mixed $default = null): mixed
    {
        $settings = $this->settings();

        return $settings[$key] ?? $default;
    }

    /** @return array<int, array<string, mixed>> */
    public function menu(): array
    {
        if ($this->demo()) {
            return $this->demo->menu();
        }

        return $this->remember('menu', function () {
            try {
                $items = $this->api()->menuitems((string) config('waaiy.menu'));

                $normalized = array_map(fn ($item) => $this->normalizeMenuItem($item), is_array($items) ? $items : []);
                $normalized = array_values(array_filter($normalized, fn ($i) => ! empty($i['label'])));

                return $normalized !== [] ? $normalized : $this->fallbackMenu();
            } catch (Throwable $e) {
                // Menü boş/bozuksa güvenli menüye düşeriz; bu "demo içerik"
                // uyarısı sayılmaz, yalnızca loglanır.
                $this->log($e);

                return $this->fallbackMenu();
            }
        });
    }

    /**
     * Menü ucu hata verdiğinde/boş döndüğünde kullanılan güvenli menü.
     * (Örn. panelde "ana-menu" adlı bir menü tanımlı değilse.)
     *
     * @return array<int, array<string, mixed>>
     */
    protected function fallbackMenu(): array
    {
        return [
            ['label' => 'Anasayfa', 'url' => '/', 'children' => []],
            ['label' => 'Ürünler', 'url' => '/urunler', 'children' => []],
            ['label' => 'Blog', 'url' => '/blog', 'children' => []],
            ['label' => 'İletişim', 'url' => '/iletisim', 'children' => []],
        ];
    }

    // -----------------------------------------------------------------
    // Tekil içerikler
    // -----------------------------------------------------------------

    /** @return array<string, mixed>|null */
    public function page(string $slug): ?array
    {
        return $this->content('page', $slug);
    }

    /** @return array<string, mixed>|null */
    public function blog(string $slug): ?array
    {
        return $this->content('blog', $slug);
    }

    /** @return array<string, mixed>|null */
    public function product(string $slug): ?array
    {
        return $this->content('product', $slug);
    }

    /** @return array<string, mixed>|null */
    protected function content(string $type, string $slug): ?array
    {
        if ($this->demo()) {
            return match ($type) {
                'page' => $this->demo->findPage($slug),
                'product' => $this->demo->findProduct($slug),
                default => $this->demo->findBlog($slug),
            };
        }

        return $this->remember("content:{$type}:{$slug}", function () use ($type, $slug) {
            try {
                $obj = match ($type) {
                    'page' => $this->api()->page($slug),
                    'product' => $this->api()->product($slug),
                    default => $this->api()->blog($slug),
                };

                return $this->normalizeContent($obj, $type);
            } catch (Throwable $e) {
                // Kayıt yokluğu (404) normaldir; API arızası sayılmaz.
                if (! $this->isNotFound($e)) {
                    $this->report($e);
                }

                return null;
            }
        });
    }

    // -----------------------------------------------------------------
    // Listeler
    // -----------------------------------------------------------------

    /**
     * @return array{items: array<int, array<string, mixed>>, total: int, category: array<string, mixed>|null}
     */
    public function blogs(string $categorySlug = '', int $page = 1, int $limit = 9): array
    {
        $categorySlug = trim($categorySlug);
        $page = max(1, $page);
        $limit = min(100, max(1, $limit));

        if ($this->demo()) {
            return $this->demo->blogList($categorySlug, $page, $limit);
        }

        return $this->remember("list:{$categorySlug}:{$page}:{$limit}", function () use ($categorySlug, $page, $limit) {
            try {
                $data = $this->api()->blogcategory($categorySlug, $page, $limit);
                $items = isset($data->__BLOGS) && is_array($data->__BLOGS)
                    ? array_map(fn ($b) => $this->normalizeContent($b, 'blog', false), $data->__BLOGS)
                    : [];

                return [
                    'items' => $items,
                    'total' => (int) ($data->__TOTAL_BLOGS ?? count($items)),
                    'category' => isset($data->__CATEGORY) ? $this->normalizeCategory($data->__CATEGORY) : null,
                ];
            } catch (Throwable $e) {
                $this->report($e);

                return $this->demo->blogList($categorySlug, $page, $limit);
            }
        });
    }

    /** @return array<int, array<string, mixed>> */
    public function categories(): array
    {
        if ($this->demo()) {
            return array_values($this->demo->categories());
        }

        return $this->remember('categories', function () {
            try {
                $items = $this->api()->blogcategories();

                return array_map(fn ($c) => $this->normalizeCategory($c), is_array($items) ? $items : []);
            } catch (Throwable $e) {
                $this->report($e);

                return array_values($this->demo->categories());
            }
        });
    }

    /**
     * Ürün listesi.
     *
     * @return array{items: array<int, array<string, mixed>>, total: int, category: array<string, mixed>|null}
     */
    public function products(string $categorySlug = '', int $page = 1, int $limit = 12): array
    {
        $categorySlug = trim($categorySlug);
        $page = max(1, $page);
        $limit = min(30, max(1, $limit)); // ürünler sunucuda en fazla 30

        if ($this->demo()) {
            return $this->demo->productList($categorySlug, $page, $limit);
        }

        return $this->remember("plist:{$categorySlug}:{$page}:{$limit}", function () use ($categorySlug, $page, $limit) {
            try {
                $data = $this->api()->productcategory($categorySlug, $page, $limit);
                $items = isset($data->__PRODUCTS) && is_array($data->__PRODUCTS)
                    ? array_map(fn ($p) => $this->normalizeContent($p, 'product', false), $data->__PRODUCTS)
                    : [];

                return [
                    'items' => $items,
                    // API toplam ürün sayısı vermez; sayfalama için tahmin üretilir.
                    'total' => ($page - 1) * $limit + count($items) + (count($items) === $limit ? 1 : 0),
                    'category' => isset($data->__CATEGORY) ? $this->normalizeCategory($data->__CATEGORY) : null,
                ];
            } catch (Throwable $e) {
                $this->report($e);

                return $this->demo->productList($categorySlug, $page, $limit);
            }
        });
    }

    /** @return array<int, array<string, mixed>> */
    public function productCategories(): array
    {
        if ($this->demo()) {
            return array_values($this->demo->productCategories());
        }

        return $this->remember('product_categories', function () {
            try {
                $items = $this->api()->productcategories();

                return array_map(fn ($c) => $this->normalizeCategory($c), is_array($items) ? $items : []);
            } catch (Throwable $e) {
                $this->report($e);

                return array_values($this->demo->productCategories());
            }
        });
    }

    // -----------------------------------------------------------------
    // Diller & çeviriler
    // -----------------------------------------------------------------

    /** @return array<int, array<string, mixed>> */
    public function languages(): array
    {
        if ($this->demo()) {
            return [
                ['id' => 1, 'name' => 'Türkçe', 'short' => 'tr', 'is_default' => true],
                ['id' => 2, 'name' => 'English', 'short' => 'en', 'is_default' => false],
            ];
        }

        return $this->remember('languages', function () {
            try {
                $langs = $this->api()->languages();

                return array_map(fn ($l) => [
                    'id' => (int) ($l->_ID ?? 0),
                    'name' => $l->_NAME ?? '',
                    'short' => strtolower((string) ($l->_LANG_SHORT_NAME ?? '')),
                    'is_default' => (bool) ($l->_IS_DEFAULT ?? false),
                ], is_array($langs) ? $langs : []);
            } catch (Throwable $e) {
                $this->report($e);

                return [];
            }
        });
    }

    public function translation(string $key, string $default = ''): string
    {
        $fallback = $default !== '' ? $default : $key;

        if ($this->demo()) {
            return $fallback;
        }

        $map = $this->translationMap();
        if (! empty($map[$key])) {
            return $map[$key];
        }

        if (! config('waaiy.auto_register_translations', true) || $this->autoRegisterBroken) {
            return $fallback;
        }

        try {
            // Auto register (get-or-create): anahtar panelde yoksa varsayılan
            // metinle kaydeder ve aynı kaynaktan okur.
            $value = $this->api()->translation($key, $fallback);
            $this->rememberTranslation($key, $value);

            return $value;
        } catch (Throwable $e) {
            // İstek sınırı/yetki hatasında kalan anahtarları zorlamayı bırak.
            $this->autoRegisterBroken = true;
            $this->report($e);

            return $fallback;
        }
    }

    /** @return array<string, string> {anahtar: metin} */
    public function translationMap(bool $fresh = false): array
    {
        if ($fresh) {
            Cache::forget($this->cacheKey('translation_map'));
        }

        return $this->remember('translation_map', function () {
            try {
                return $this->api()->translation_map();
            } catch (Throwable $e) {
                $this->report($e);

                return [];
            }
        });
    }

    /** Auto-register sonrası önbelleği yerelde günceller (tekrar yazma olmasın). */
    protected function rememberTranslation(string $key, string $value): void
    {
        if (! config('waaiy.cache.enabled', true)) {
            return;
        }

        $map = $this->translationMap();
        $map[$key] = $value;
        Cache::put($this->cacheKey('translation_map'), $map, $this->cacheTtl());
    }

    // -----------------------------------------------------------------
    // SEO araçları
    // -----------------------------------------------------------------

    public function sitemap(): string
    {
        if ($this->demo()) {
            return $this->demoSitemap();
        }

        try {
            return $this->api()->sitemap();
        } catch (Throwable $e) {
            $this->report($e);

            return $this->demoSitemap();
        }
    }

    public function sitemapPart(string $type, int $page): string
    {
        if ($this->demo()) {
            return $this->demoSitemap();
        }

        try {
            return $this->api()->sitemap_part($type, $page);
        } catch (Throwable $e) {
            $this->report($e);

            return $this->demoSitemap();
        }
    }

    public function robots(): string
    {
        if ($this->demo()) {
            return "User-agent: *\nAllow: /\n\nSitemap: " . url('/sitemap.xml') . "\n";
        }

        try {
            return $this->api()->robots();
        } catch (Throwable $e) {
            $this->report($e);

            return "User-agent: *\nAllow: /\n\nSitemap: " . url('/sitemap.xml') . "\n";
        }
    }

    /** 404 dalında eski adresleri yeni adrese yönlendirmek için. */
    public function redirect(string $path): ?object
    {
        if ($this->demo()) {
            return null;
        }

        try {
            return $this->api()->redirect_check($path);
        } catch (Throwable $e) {
            $this->report($e);

            return null;
        }
    }

    // -----------------------------------------------------------------
    // Etkileşim
    // -----------------------------------------------------------------

    public function submitForm(string $formSlug, array $data): string
    {
        if ($this->demo()) {
            return 'Mesajınız alındı. Teşekkür ederiz! (demo)';
        }

        return $this->api()->form_submit($formSlug, $data);
    }

    public function subscribe(string $email, ?string $name = null): object
    {
        if ($this->demo()) {
            return (object) ['message' => 'Abone olundu. (demo)', 'ukey' => 'demo'];
        }

        return $this->api()->subscribe($email, $name);
    }

    public function addComment(string $type, int $id, array $data): string
    {
        if ($this->demo()) {
            return 'Yorumunuz moderatör onayı bekliyor. (demo)';
        }

        return $this->api()->comment_add(
            $type,
            $id,
            $data['name'] ?? '',
            $data['email'] ?? '',
            $data['message'] ?? '',
            (int) ($data['parent_id'] ?? 0)
        );
    }

    // -----------------------------------------------------------------
    // İç yardımcılar
    // -----------------------------------------------------------------

    /** Aktif dili API oturumuna uygular ve istemciyi döndürür. */
    protected function api(): \Waaiy\API
    {
        $api = $this->client->api();
        $id = session('waaiy_lang_id');
        if ($id) {
            $api->set_lang((int) $id);
        }

        return $api;
    }

    protected function langPrefix(): string
    {
        $code = session('waaiy_lang');

        return $code ? $code.':' : '';
    }

    /** Önbellek anahtarını (prefix + dil) üretir. */
    protected function cacheKey(string $key): string
    {
        return (string) config('waaiy.cache.prefix', 'waaiy:').$this->langPrefix().$key;
    }

    protected function cacheTtl(): int
    {
        return (int) config('waaiy.cache.ttl', 300);
    }

    /**
     * @template T
     * @param  Closure():T  $callback
     * @return T
     */
    protected function remember(string $key, Closure $callback): mixed
    {
        if (! config('waaiy.cache.enabled', true)) {
            return $callback();
        }

        return Cache::remember($this->cacheKey($key), $this->cacheTtl(), $callback);
    }

    /** API içeriğini şablonların beklediği düz yapıya çevirir. */
    protected function normalizeContent(object $obj, string $type, bool $withSeo = true): array
    {
        $api = $this->api();
        $content = (string) $api->informations($obj, '_CONTENT');

        $item = [
            'type' => $type,
            'id' => $this->contentId($obj),
            'slug' => (string) $api->slug($obj),
            'title' => $this->decode((string) $api->informations($obj, '_TITLE')),
            'excerpt' => Seo::excerpt((string) $api->informations($obj, '_DESCRIPTIONSEO') ?: $content, 160),
            'content' => $content,
            'cover' => $api->cover_image_or_first($obj, $this->mediaUrl()),
            'category' => null,
            'tags' => [],
            'author' => 'Waaiy',
            'created_at' => $obj->_CREATED_DATE ?? null,
            'updated_at' => $obj->_LAST_UPDATED_DATE ?? null,
            'reading_time' => max(1, (int) ceil(Str::wordCount(strip_tags($content)) / 180)),
            'tabs' => $this->normalizeTabs((array) ($obj->_TABS ?? [])),
            'accordions' => $this->normalizeAccordions((array) ($obj->_ACORDIONS ?? [])),
            'fields' => (array) ($obj->_FIELDS ?? []),
            'photos' => (array) ($obj->_PHOTOS ?? []),
            'seo' => [],
            'jsonld' => null,
        ];

        if ($withSeo) {
            $item['seo'] = $api->seo($obj);
            $item['jsonld'] = $api->jsonld($obj);
        }

        if ($type === 'product') {
            $item = array_merge($item, $this->productExtras($obj, $item));
        }

        return $item;
    }

    /**
     * Ürün alanlarını _FIELDS / _PHOTOS / _TABS üzerinden çıkarır.
     * Panelde tanımlı olmayan alanlar için güvenli varsayılanlar döner.
     *
     * @param  array<string, mixed>  $base
     * @return array<string, mixed>
     */
    protected function productExtras(object $obj, array $base): array
    {
        $media = $this->mediaUrl();

        $gallery = [];
        foreach ((array) ($obj->_PHOTOS ?? []) as $photo) {
            $photo = (array) $photo;
            if (! empty($photo['medya_id'])) {
                $gallery[] = $this->api()->media((int) $photo['medya_id'], $media);
            }
        }
        if ($gallery === [] && ! empty($base['cover'])) {
            $gallery[] = $base['cover'];
        }

        $specs = [];
        $data = [
            'price' => null, 'old_price' => null, 'currency' => 'TRY',
            'sku' => '', 'stock' => 0, 'rating' => 0, 'reviews' => 0,
            'badge' => null, 'variants' => [],
        ];
        $map = [
            'price' => 'price', 'fiyat' => 'price',
            'old_price' => 'old_price', 'eski_fiyat' => 'old_price', 'indirimli_fiyat' => 'old_price',
            'sku' => 'sku', 'stok_kodu' => 'sku',
            'stock' => 'stock', 'stok' => 'stock',
            'badge' => 'badge', 'etiket' => 'badge',
        ];

        foreach ((array) ($obj->_FIELDS ?? []) as $key => $value) {
            if (is_array($value) && array_key_exists('value', $value)) {
                $key = $value['key'] ?? $key;
                $value = $value['value'];
            } elseif (is_array($value) && array_key_exists('key', $value)) {
                $key = $value['key'];
            }
            if (! is_string($key) || $key === '') {
                continue;
            }

            $lower = mb_strtolower($key);
            if (isset($map[$lower])) {
                $target = $map[$lower];
                $data[$target] = in_array($target, ['stock', 'reviews']) ? (int) $value
                    : (in_array($target, ['price', 'old_price']) ? (float) $value : (string) $value);
                continue;
            }

            if (is_scalar($value)) {
                $specs[] = ['label' => ucwords(str_replace(['_', '-'], ' ', $key)), 'value' => (string) $value];
            }
        }

        $discount = ($data['old_price'] && $data['price'])
            ? (int) round((1 - $data['price'] / $data['old_price']) * 100)
            : 0;

        return array_merge($data, [
            'gallery' => $gallery,
            'specs' => $specs,
            'discount' => $discount,
            'cover' => $base['cover'] ?: ($gallery[0] ?? null),
            'tabs' => $base['tabs'] ?: [['title' => 'Açıklama', 'content' => $base['content']]],
            'accordions' => $base['accordions'],
        ]);
    }

    /**
     * Panelden gelen _TABS dizisini {title, content} listesine çevirir.
     *
     * @return array<int, array{title:string, content:string}>
     */
    protected function normalizeTabs(array $tabs): array
    {
        $out = [];
        foreach ($tabs as $tab) {
            $tab = (array) $tab;
            $title = $this->pickLang($tab['_TITLE'] ?? $tab['title'] ?? '');
            $content = $this->pickLang($tab['_CONTENT'] ?? $tab['content'] ?? '');
            if (is_array($tab['title'] ?? null) || is_array($tab['content'] ?? null)) {
                // Bazı kurulumlarda başlık/içerik dil haritası olarak gelir.
                $title = $title ?: '';
                $content = $content ?: '';
            }
            if ($title !== '' || $content !== '') {
                $out[] = ['title' => $title, 'content' => $content];
            }
        }

        return $out;
    }

    /**
     * @return array<int, array{title:string, content:string}>
     */
    protected function normalizeAccordions(array $items): array
    {
        $out = [];
        foreach ($items as $item) {
            $item = (array) $item;
            $title = $this->pickLang($item['_TITLE'] ?? $item['title'] ?? '');
            $content = $this->pickLang($item['_CONTENT'] ?? $item['content'] ?? '');
            if ($title !== '' || $content !== '') {
                $out[] = ['title' => $title, 'content' => $content];
            }
        }

        return $out;
    }

    /** HTML entity'lerini çözer (ör. &Uuml;r&uuml;n → Ürün). */
    protected function decode(string $value): string
    {
        return html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * İçeriğin kalıcı bir kimliği. Waaiy tekil içeriklerde `_ID` alanı
     * döndürmediği için slug'dan türetilen sabit bir değere düşer.
     */
    protected function contentId(object $obj): int
    {
        if (isset($obj->_ID)) {
            return (int) $obj->_ID;
        }

        $slug = (string) $this->api()->slug($obj);

        return abs(crc32($slug)) ?: abs(crc32('waaiy'));
    }

    /** @return array<string, mixed> */
    protected function normalizeMenuItem(object $item): array
    {
        $type = $item->type ?? 'normal';
        $slug = $item->slug ?? '';
        $title = $this->localized($item->informations ?? null, '_TITLE');

        $url = match ($type) {
            'page' => '/'.trim((string) config('waaiy.prefixes.page', 'page'), '/').'/'.$slug,
            'blog_category' => '/'.trim((string) config('waaiy.prefixes.blog_category', 'blogs'), '/').'/'.$slug,
            'product_category' => '/'.trim((string) config('waaiy.prefixes.product_category', 'products'), '/').'/'.$slug,
            default => Str::startsWith((string) $slug, ['http', '/']) ? $slug : '/'.$slug,
        };

        $children = [];
        if (! empty($item->children) && is_array($item->children)) {
            $children = array_map(fn ($c) => $this->normalizeMenuItem($c), $item->children);
        }

        return ['label' => $title, 'url' => $url, 'children' => $children];
    }

    /** @return array<string, mixed> */
    protected function normalizeCategory(object $cat): array
    {
        // Kategori ağacı elemanları "INFOS" sarmalayıcısı kullanır.
        $node = isset($cat->INFOS) ? (array) $cat->INFOS : (array) $cat;

        $infos = $node['_INFORMATIONS'] ?? $node['informations'] ?? null;
        $name = $infos ? $this->localized((object) $infos, '_TITLE') : '';
        if ($name === '') {
            $name = (string) ($node['_NAME'] ?? $node['name'] ?? '');
        }

        $slug = '';
        if (isset($node['_SLUGS'])) {
            $slug = $this->pickLang($node['_SLUGS']);
        }
        if ($slug === '') {
            $slug = (string) ($node['slug'] ?? $node['_SLUG'] ?? '');
        }

        return ['name' => $name, 'slug' => $slug];
    }

    /** informations içindeki dile ait değeri düz string olarak alır. */
    protected function localized(mixed $informations, string $field): string
    {
        if (is_object($informations) && isset($informations->{$field})) {
            return $this->pickLang($informations->{$field});
        }
        if (is_array($informations) && isset($informations[$field])) {
            return $this->pickLang($informations[$field]);
        }

        return '';
    }

    /** {dil_id: deger} haritasından aktif dilin değerini (yoksa ilkini) alır. */
    protected function pickLang(mixed $map): string
    {
        if (is_object($map)) {
            $map = (array) $map;
        }
        if (is_scalar($map)) {
            return (string) $map;
        }
        if (! is_array($map) || $map === []) {
            return '';
        }

        $langId = null;
        if (! $this->client->demo()) {
            try {
                $langId = (string) $this->api()->lang_id();
            } catch (Throwable $e) {
                $langId = null;
            }
        }

        if ($langId !== null && $langId !== '' && array_key_exists($langId, $map)) {
            return (string) $map[$langId];
        }

        return (string) (reset($map) ?: '');
    }

    protected function demoSitemap(): string
    {
        $prefix = '/'.(session('waaiy_lang') ?: ($this->languages()[0]['short'] ?? 'tr'));

        $urls = [url($prefix), url($prefix.'/blog')];
        foreach ($this->demo->blogs() as $blog) {
            $urls[] = url($prefix.'/blog/'.$blog['slug']);
        }
        foreach (array_keys($this->demo->pages()) as $slug) {
            $urls[] = url($prefix.'/'.$slug);
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
        foreach ($urls as $u) {
            $xml .= "  <url><loc>".e($u)."</loc></url>\n";
        }
        $xml .= '</urlset>';

        return $xml;
    }

    protected function report(Throwable $e): void
    {
        $this->fellBack = true;
        $this->lastError = $e->getMessage();

        $this->log($e);
    }

    /** Yalnızca loglar; "demo içerik" uyarısı sayılmaz. */
    protected function log(Throwable $e): void
    {
        if (app()->bound('log')) {
            logger()->warning('Waaiy isteği başarısız: '.$e->getMessage());
        }
    }

    /** İstisna "kayıt bulunamadı" (404) anlamına mı geliyor? */
    protected function isNotFound(Throwable $e): bool
    {
        $message = mb_strtolower($e->getMessage());

        return str_contains($message, 'not found')
            || str_contains($message, 'bulunamadı')
            || str_contains($message, 'bulunamadi');
    }
}
