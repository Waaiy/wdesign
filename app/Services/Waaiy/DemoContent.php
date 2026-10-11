<?php

namespace App\Services\Waaiy;

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * API anahtarı olmadan tasarımın eksiksiz görüntülenebilmesi için örnek içerik.
 *
 * Dönen yapı, ContentRepository'nin API yanıtlarını normalize ettiği yapıyla
 * birebir aynıdır; böylece Blade şablonları iki kaynağı ayırt etmez.
 */
class DemoContent
{
    public function settings(): array
    {
        return [
            'site_basligi' => config('app.name', 'Waaiy Design'),
            'slogan' => 'Headless CMS ile tasarlanmış, çok varyasyonlu modern web deneyimi.',
            'telefon' => '+90 332 000 00 00',
            'eposta' => 'merhaba@ornek.com',
            'adres' => 'Konya, Türkiye',
            'sosyal' => [
                'instagram' => 'https://instagram.com',
                'linkedin' => 'https://linkedin.com',
                'x' => 'https://x.com',
            ],
        ];
    }

    /** @return array<int, array<string, mixed>> */
    public function menu(): array
    {
        return [
            ['label' => 'Anasayfa', 'url' => '/', 'children' => []],
            ['label' => 'Hakkımızda', 'url' => '/hakkimizda', 'children' => []],
            ['label' => 'Ürünler', 'url' => '/urunler', 'children' => []],
            ['label' => 'Blog', 'url' => '/blog', 'children' => []],
            ['label' => 'İletişim', 'url' => '/iletisim', 'children' => []],
        ];
    }

    /** @return array<string, array<string, mixed>> */
    public function pages(): array
    {
        return [
            'hakkimizda' => $this->page(
                'hakkimizda',
                'Hakkımızda',
                'Waaiy headless CMS ile içeriğini özgürce yöneten, hızlı ve erişilebilir dijital deneyimler üretiyoruz.',
                '<p>2015 yılından bu yana içerik yönetimini yeniden düşünüyoruz. <strong>Waaiy</strong> ile tasarımı geliştiricinin, içeriği editörün eline bırakmayan; ikisini de aynı hızda çalıştıran bir altyapı kuruyoruz.</p>
                <h2>Neden Headless?</h2>
                <p>Geleneksel sistemlerde tasarım ve içerik birbirine kilitlidir. Headless yaklaşımda içerik bir API üzerinden servis edilir; böylece her kanal — web, mobil, kiosk — aynı kaynaktan beslenir.</p>
                <ul><li>Ölçeklenebilir mimari</li><li>Daha hızlı yayına alma</li><li>Tek kaynak, çok kanal</li></ul>'
            ),
            'iletisim' => $this->page(
                'iletisim',
                'İletişim',
                'Projenizi konuşalım. Formu doldurun ya da doğrudan bize ulaşın.',
                '<p>Formu doldurduğunuzda mesajınız doğrudan Waaiy panelindeki form yönetimine düşer. Ekibimiz en kısa sürede dönüş yapar.</p>'
            ),
            'gizlilik' => $this->page(
                'gizlilik',
                'Gizlilik Politikası',
                'Kişisel verilerinizin işlenmesine ilişkin aydınlatma metni.',
                '<p>Bu sayfa, KVKK ve GDPR uyumlu aydınlatma metninin nasıl bir içerik yapısıyla sunulacağını gösteren örnek bir sayfadır.</p>'
            ),
        ];
    }

    /**
     * @return array<string, array{name:string, slug:string}>
     */
    public function categories(): array
    {
        return [
            'seo' => ['name' => 'SEO', 'slug' => 'seo'],
            'tasarim' => ['name' => 'Tasarım', 'slug' => 'tasarim'],
            'teknoloji' => ['name' => 'Teknoloji', 'slug' => 'teknoloji'],
        ];
    }

    /** @return array<int, array<string, mixed>> */
    public function blogs(): array
    {
        $cats = $this->categories();

        $items = [
            [
                'slug' => 'headless-cms-ile-hizli-web-uygulamalari',
                'title' => 'Headless CMS ile Daha Hızlı Web Uygulamaları',
                'category' => $cats['teknoloji'],
                'cover' => 'https://picsum.photos/seed/waaiy-headless/1600/1000',
                'excerpt' => 'Headless mimari, içeriği arayüzden ayırarak her kanalda tutarlı ve hızlı bir deneyim sunar. Peki performansı nasıl doğrudan etkiler?',
                'tags' => ['headless', 'api', 'performans'],
                'body' => '
                    <p>Headless CMS, içerik yönetimini sunum katmanından ayırır. Bu ayrım, önyüzün yalnızca ihtiyaç duyduğu veriyi çekmesini ve hafif kalmasını sağlar.</p>
                    <h2>Performansın üç ayağı</h2>
                    <ul><li><strong>İstemci taraflı önbellek:</strong> API yanıtları CDN ve uygulama katmanında cache\'lenir.</li><li><strong>Az veri, çok hız:</strong> Yalnızca gerekli alanlar taşınır.</li><li><strong>Bağımsız ölçekleme:</strong> Trafik artışında yalnızca önyüz ölçeklenir.</li></ul>
                    <blockquote>İyi bir mimari, içerik ekibini de geliştiriciyi de yavaşlatmaz.</blockquote>
                    <p>Waaiy bu noktada hazır bir REST API sunar; tasarım tamamen size kalır.</p>',
            ],
            [
                'slug' => 'core-web-vitals-teknik-seo-rehberi',
                'title' => 'Core Web Vitals: SEO İçin Teknik Rehber',
                'category' => $cats['seo'],
                'cover' => 'https://picsum.photos/seed/waaiy-vitals/1600/1000',
                'excerpt' => 'LCP, INP ve CLS metrikleri sıralamanızı doğrudan etkiler. Ölçün, iyileştirin, koruyun.',
                'tags' => ['seo', 'core web vitals', 'performans'],
                'body' => '
                    <p>Core Web Vitals, kullanıcı deneyimini ölçülebilir üç metrikle değerlendirir: yükleme (LCP), etkileşim (INP) ve görsel kararlılık (CLS).</p>
                    <h2>Hızlı iyileştirme adımları</h2>
                    <ol><li>Görselleri modern formatlarda (AVIF/WebP) ve doğru boyutta sunun.</li><li>Kritik CSS\'i satır içine alın, geri kalanını erteleyin.</li><li>Yazı tipi değişimlerini <code>font-display: swap</code> ile yönetin.</li></ol>
                    <p>Bu tasarımda görseller <code>loading="lazy"</code> ve sabit oranlarla yerleştirilmiştir; böylece CLS neredeyse sıfırdır.</p>',
            ],
            [
                'slug' => '2026-web-tasarim-trendleri',
                'title' => '2026 Web Tasarım Trendleri',
                'category' => $cats['tasarim'],
                'cover' => 'https://picsum.photos/seed/waaiy-trends/1600/1000',
                'excerpt' => 'Bento düzenler, değişken tipografi ve yumuşak gradyanlar bu yılın öne çıkan yaklaşımları arasında.',
                'tags' => ['tasarım', 'trend', 'ui'],
                'body' => '
                    <p>2026 tasarımı, sadeliği kişilikle birleştiriyor. İşte bu tasarımda da uyguladığımız başlıca yaklaşımlar.</p>
                    <h2>Öne çıkanlar</h2>
                    <ul><li><strong>Bento düzenler:</strong> Farklı boyutlarda modüler kartlar.</li><li><strong>Değişken tipografi:</strong> Tek dosyada sınırsız ağırlık.</li><li><strong>Yumuşak gradyanlar:</strong> Derinlik hissi veren katmanlı renkler.</li><li><strong>Erişilebilir kontrast:</strong> WCAG AA hedefi artık standart.</li></ul>',
            ],
            [
                'slug' => 'yapilandirilmis-veri-ile-zengin-sonuclar',
                'title' => 'Yapılandırılmış Veri ile Zengin Sonuçlar',
                'category' => $cats['seo'],
                'cover' => 'https://picsum.photos/seed/waaiy-schema/1600/1000',
                'excerpt' => 'JSON-LD sayesinde içeriğiniz arama sonuçlarında zengin parçacıklar olarak görünür.',
                'tags' => ['seo', 'json-ld', 'schema'],
                'body' => '
                    <p>Schema.org, içeriğinizi makine tarafından anlaşılır kılar. Waaiy, JSON-LD grafiğini panelden otomatik üretir.</p>
                    <h2>Bu tasarımda neler var?</h2>
                    <ul><li>Organization ve WebSite düğümleri</li><li>BlogPosting / WebPage</li><li>BreadcrumbList</li><li>FAQPage (akordiyonlardan türetilir)</li></ul>
                    <p>Sağ taraftaki kaynak kodunu incelediğinizde tüm grafikleri görebilirsiniz.</p>',
            ],
            [
                'slug' => 'tasarim-sistemleri-neden-gerekli',
                'title' => 'Tasarım Sistemleri Neden Gerekli?',
                'category' => $cats['tasarim'],
                'cover' => 'https://picsum.photos/seed/waaiy-system/1600/1000',
                'excerpt' => 'Tek bir iskelet, onlarca varyasyon. Tasarım sistemi tutarlılığı ölçeklenebilir kılar.',
                'tags' => ['tasarım', 'design system', 'ui'],
                'body' => '
                    <p>Bu tasarım, tek bir iskeletin header ve blog varyasyonlarıyla nasıl farklılaşabileceğini gösterir. Renkler CSS değişkenleriyle yönetildiği için yeni bir kimlik dakikalar içinde kurulur.</p>
                    <h2>Kazanımlar</h2>
                    <ul><li>Tutarlı görsel dil</li><li>Daha hızlı geliştirme</li><li>Kolay bakım ve güncelleme</li></ul>',
            ],
            [
                'slug' => 'api-oncelikli-mimariye-gecis',
                'title' => 'API Öncelikli Mimariye Geçiş',
                'category' => $cats['teknoloji'],
                'cover' => 'https://picsum.photos/seed/waaiy-api/1600/1000',
                'excerpt' => 'Mevcut sitenizi bozmadan içerik katmanını API\'ye taşımanın pratik yolları.',
                'tags' => ['api', 'mimari', 'headless'],
                'body' => '
                    <p>Geçiş korkutucu görünse de kademeli ilerlemek mümkündür. Önce blogu taşıyın, ardından sayfaları.</p>
                    <h2>Kademeli plan</h2>
                    <ol><li>İçerik modelini çıkarın.</li><li>Tek bir modülü API\'ye bağlayın.</li><li>Önbellek ve yönlendirmeleri kurun.</li><li>SEO eşitliğini doğrulayın.</li></ol>',
            ],
            [
                'slug' => 'yerel-seo-sehir-bazli-gorunurluk',
                'title' => 'Yerel SEO: Şehir Bazlı Görünürlük',
                'category' => $cats['seo'],
                'cover' => 'https://picsum.photos/seed/waaiy-local/1600/1000',
                'excerpt' => 'Yerel işletmeler için haritalar, LocalBusiness şeması ve şehir sayfaları nasıl kurgulanır?',
                'tags' => ['seo', 'yerel seo', 'local'],
                'body' => '
                    <p>Yerel aramalarda görünmek için tutarlı işletme bilgisi, doğru şema ve bölge odaklı içerik şarttır.</p>
                    <h2>Kontrol listesi</h2>
                    <ul><li>LocalBusiness / Organization şeması</li><li>NAP tutarlılığı (Ad, Adres, Telefon)</li><li>Şehir bazlı açılış sayfaları</li></ul>',
            ],
            [
                'slug' => 'mikro-animasyonlarla-kullanici-deneyimi',
                'title' => 'Mikro Animasyonlarla Kullanıcı Deneyimi',
                'category' => $cats['tasarim'],
                'cover' => 'https://picsum.photos/seed/waaiy-motion/1600/1000',
                'excerpt' => 'Doğru kullanılan küçük hareketler, arayüzü anlaşılır ve keyifli hale getirir.',
                'tags' => ['tasarım', 'animasyon', 'ux'],
                'body' => '
                    <p>Animasyon bir süs değil, bir geri bildirim aracıdır. Kullanıcının nerede olduğunu ve ne olduğunu anlatır.</p>
                    <p>Bu tasarımda kaydırma ile beliren (reveal) bölümler ve geçişler hafif tutulmuştur; <code>prefers-reduced-motion</code> desteklenir.</p>',
            ],
            [
                'slug' => 'cok-dilli-sitelerde-seo-stratejisi',
                'title' => 'Çok Dilli Sitelerde SEO Stratejisi',
                'category' => $cats['seo'],
                'cover' => 'https://picsum.photos/seed/waaiy-multilang/1600/1000',
                'excerpt' => 'hreflang, dil bazlı URL yapısı ve çeviri yönetimi ile uluslararası görünürlük.',
                'tags' => ['seo', 'çok dilli', 'hreflang'],
                'body' => '
                    <p>Waaiy, içeriği dil bazlı haritalarla saklar; aynı içerik birden çok dilde yayınlanabilir.</p>
                    <h2>Doğru yapı</h2>
                    <ul><li>Her dil için benzersiz URL</li><li>Karşılıklı hreflang etiketleri</li><li>Dil bazında sitemap</li></ul>',
            ],
        ];

        $out = [];
        foreach ($items as $i => $item) {
            $created = Carbon::now()->subDays(($i + 1) * 6)->setTime(9, 30);
            $out[] = array_merge($item, [
                'id' => $i + 1,
                'author' => 'Waaiy Editör',
                'created_at' => $created->toIso8601String(),
                'updated_at' => $created->copy()->addDays(2)->toIso8601String(),
                'reading_time' => max(3, (int) ceil(Str::wordCount(strip_tags($item['body'])) / 180)),
            ]);
        }

        return $out;
    }

    public function findBlog(string $slug): ?array
    {
        foreach ($this->blogs() as $blog) {
            if ($blog['slug'] === $slug) {
                return $this->normalizeDemo($blog, 'blog');
            }
        }

        return null;
    }

    public function findPage(string $slug): ?array
    {
        $pages = $this->pages();

        return isset($pages[$slug]) ? $this->normalizeDemo($pages[$slug], 'page') : null;
    }

    /**
     * @return array{items: array<int, array>, total: int, category: array|null}
     */
    public function blogList(string $categorySlug, int $page, int $limit): array
    {
        $all = $this->blogs();
        $cat = null;

        if ($categorySlug !== '') {
            $cats = $this->categories();
            $cat = $cats[$categorySlug] ?? ['name' => ucfirst($categorySlug), 'slug' => $categorySlug];
            $all = array_values(array_filter($all, fn ($b) => ($b['category']['slug'] ?? null) === $categorySlug));
        }

        $total = count($all);
        $offset = max(0, ($page - 1) * $limit);
        $slice = array_slice($all, $offset, $limit);

        return [
            'items' => array_map(fn ($b) => $this->normalizeDemo($b, 'blog'), $slice),
            'total' => $total,
            'category' => $cat,
        ];
    }

    // -----------------------------------------------------------------
    // Ürünler
    // -----------------------------------------------------------------

    /** @return array<string, array{name:string, slug:string}> */
    public function productCategories(): array
    {
        return [
            'elektronik' => ['name' => 'Elektronik', 'slug' => 'elektronik'],
            'aksesuar' => ['name' => 'Aksesuar', 'slug' => 'aksesuar'],
            'ev-yasam' => ['name' => 'Ev & Yaşam', 'slug' => 'ev-yasam'],
        ];
    }

    /** @return array<int, array<string, mixed>> */
    public function products(): array
    {
        $cats = $this->productCategories();

        $items = [
            [
                'slug' => 'aurora-kablosuz-kulaklik',
                'title' => 'Aurora Kablosuz Kulaklık',
                'category' => $cats['elektronik'],
                'price' => 2499.00,
                'old_price' => 3199.00,
                'badge' => 'Çok Satan',
                'rating' => 4.8,
                'reviews' => 214,
                'stock' => 32,
                'sku' => 'WAI-AUR-01',
                'excerpt' => 'Aktif gürültü engelleme, 40 saat pil ömrü ve kristal netliğinde ses. Gün boyu konfor.',
                'gallery' => ['waaiy-aurora-1', 'waaiy-aurora-2', 'waaiy-aurora-3'],
                'variants' => [
                    ['name' => 'Renk', 'options' => ['Uzay Grisi', 'Gece Siyahı', 'Kum Beyazı']],
                    ['name' => 'Bağlantı', 'options' => ['Bluetooth 5.3', '2.4GHz + BT']],
                ],
                'specs' => [
                    ['label' => 'Pil Ömrü', 'value' => '40 saat (ANC kapalı)'],
                    ['label' => 'Bağlantı', 'value' => 'Bluetooth 5.3'],
                    ['label' => 'Ağırlık', 'value' => '255 g'],
                    ['label' => 'Garanti', 'value' => '2 yıl'],
                ],
                'tags' => ['kablosuz', 'kulaklık', 'anc'],
            ],
            [
                'slug' => 'nimbus-akilli-saat',
                'title' => 'Nimbus Akıllı Saat',
                'category' => $cats['elektronik'],
                'price' => 3899.00,
                'old_price' => null,
                'badge' => 'Yeni',
                'rating' => 4.6,
                'reviews' => 87,
                'stock' => 18,
                'sku' => 'WAI-NMB-02',
                'excerpt' => 'AMOLED ekran, 7 gün pil, nabız ve uyku takibi. Suya dayanıklı (5 ATM).',
                'gallery' => ['waaiy-nimbus-1', 'waaiy-nimbus-2', 'waaiy-nimbus-3'],
                'variants' => [
                    ['name' => 'Kasa', 'options' => ['44mm', '40mm']],
                    ['name' => 'Kordon', 'options' => ['Silikon', 'Örgü']],
                ],
                'specs' => [
                    ['label' => 'Ekran', 'value' => '1.43" AMOLED'],
                    ['label' => 'Pil', 'value' => '7 gün'],
                    ['label' => 'Su Dayanımı', 'value' => '5 ATM'],
                    ['label' => 'Sensörler', 'value' => 'Nabız, SpO2, İvmeölçer'],
                ],
                'tags' => ['akıllı saat', 'spor', 'sağlık'],
            ],
            [
                'slug' => 'terra-deri-cuzdan',
                'title' => 'Terra Deri Cüzdan',
                'category' => $cats['aksesuar'],
                'price' => 899.00,
                'old_price' => 1099.00,
                'badge' => null,
                'rating' => 4.9,
                'reviews' => 156,
                'stock' => 64,
                'sku' => 'WAI-TER-03',
                'excerpt' => 'Tam taneli el yapımı hakiki deri, RFID korumalı ve 8 kart bölmeli slim tasarım.',
                'gallery' => ['waaiy-terra-1', 'waaiy-terra-2', 'waaiy-terra-3'],
                'variants' => [
                    ['name' => 'Renk', 'options' => ['Tabak', 'Siyah', 'Kahve']],
                ],
                'specs' => [
                    ['label' => 'Materyal', 'value' => 'Tam taneli deri'],
                    ['label' => 'Kart Bölmesi', 'value' => '8'],
                    ['label' => 'RFID', 'value' => 'Var'],
                    ['label' => 'Ölçü', 'value' => '11 × 9 cm'],
                ],
                'tags' => ['deri', 'cüzdan', 'rfid'],
            ],
            [
                'slug' => 'lumen-masa-lambasi',
                'title' => 'Lumen Masa Lambası',
                'category' => $cats['ev-yasam'],
                'price' => 1299.00,
                'old_price' => 1599.00,
                'badge' => 'İndirim',
                'rating' => 4.5,
                'reviews' => 102,
                'stock' => 41,
                'sku' => 'WAI-LMN-04',
                'excerpt' => 'Kademeli sıcaklık ve parlaklık ayarı, dokunmatik kontrol, göz yormayan yumuşak ışık.',
                'gallery' => ['waaiy-lumen-1', 'waaiy-lumen-2', 'waaiy-lumen-3'],
                'variants' => [
                    ['name' => 'Gövde', 'options' => ['Mat Beyaz', 'Antrasit']],
                ],
                'specs' => [
                    ['label' => 'Işık', 'value' => '2700K – 6500K'],
                    ['label' => 'Parlaklık', 'value' => '10 kademe'],
                    ['label' => 'Kontrol', 'value' => 'Dokunmatik'],
                    ['label' => 'Güç', 'value' => '12W LED'],
                ],
                'tags' => ['lamba', 'aydınlatma', 'led'],
            ],
            [
                'slug' => 'vertex-mekanik-klavye',
                'title' => 'Vertex Mekanik Klavye',
                'category' => $cats['elektronik'],
                'price' => 2199.00,
                'old_price' => 2599.00,
                'badge' => 'Çok Satan',
                'rating' => 4.7,
                'reviews' => 178,
                'stock' => 26,
                'sku' => 'WAI-VTX-05',
                'excerpt' => 'Hot-swap switch, RGB aydınlatma ve alüminyum gövde. Yazılım ile tam özelleştirme.',
                'gallery' => ['waaiy-vertex-1', 'waaiy-vertex-2', 'waaiy-vertex-3'],
                'variants' => [
                    ['name' => 'Switch', 'options' => ['Linear', 'Tactile', 'Clicky']],
                    ['name' => 'Dizilim', 'options' => ['TR Q', 'US']],
                ],
                'specs' => [
                    ['label' => 'Gövde', 'value' => 'Alüminyum'],
                    ['label' => 'Switch', 'value' => 'Hot-swap'],
                    ['label' => 'Aydınlatma', 'value' => 'RGB'],
                    ['label' => 'Bağlantı', 'value' => 'USB-C / Kablosuz'],
                ],
                'tags' => ['klavye', 'mekanik', 'rgb'],
            ],
            [
                'slug' => 'azure-termos-500ml',
                'title' => 'Azure Termos 500 ml',
                'category' => $cats['ev-yasam'],
                'price' => 549.00,
                'old_price' => 699.00,
                'badge' => null,
                'rating' => 4.4,
                'reviews' => 64,
                'stock' => 90,
                'sku' => 'WAI-AZR-06',
                'excerpt' => 'Çift cidarlı paslanmaz çelik; 12 saat sıcak, 24 saat soğuk. Sızdırmaz kapak.',
                'gallery' => ['waaiy-azure-1', 'waaiy-azure-2', 'waaiy-azure-3'],
                'variants' => [
                    ['name' => 'Renk', 'options' => ['Gece Mavisi', 'Yeşil', 'Gri']],
                ],
                'specs' => [
                    ['label' => 'Hacim', 'value' => '500 ml'],
                    ['label' => 'Materyal', 'value' => '304 Paslanmaz Çelik'],
                    ['label' => 'Sıcak Tutma', 'value' => '12 saat'],
                    ['label' => 'Soğuk Tutma', 'value' => '24 saat'],
                ],
                'tags' => ['termos', 'matara', 'çelik'],
            ],
        ];

        return $items;
    }

    public function findProduct(string $slug): ?array
    {
        foreach ($this->products() as $product) {
            if ($product['slug'] === $slug) {
                return $this->normalizeProductDemo($product);
            }
        }

        return null;
    }

    /**
     * @return array{items: array<int, array>, total: int, category: array|null}
     */
    public function productList(string $categorySlug, int $page, int $limit): array
    {
        $all = $this->products();
        $cat = null;

        if ($categorySlug !== '') {
            $cats = $this->productCategories();
            $cat = $cats[$categorySlug] ?? ['name' => ucfirst($categorySlug), 'slug' => $categorySlug];
            $all = array_values(array_filter($all, fn ($p) => ($p['category']['slug'] ?? null) === $categorySlug));
        }

        $total = count($all);
        $offset = max(0, ($page - 1) * $limit);

        return [
            'items' => array_map(fn ($p) => $this->normalizeProductDemo($p), array_slice($all, $offset, $limit)),
            'total' => $total,
            'category' => $cat,
        ];
    }

    /** Demo ürün kaydını normalize edilmiş içerik yapısına çevirir. */
    private function normalizeProductDemo(array $item): array
    {
        $gallery = array_map(fn ($seed) => 'https://picsum.photos/seed/'.$seed.'/1200/1200', $item['gallery'] ?? []);
        $discount = ($item['old_price'] ?? null) ? (int) round((1 - $item['price'] / $item['old_price']) * 100) : 0;

        return [
            'type' => 'product',
            'id' => abs(crc32($item['slug'])),
            'slug' => $item['slug'],
            'title' => $item['title'],
            'excerpt' => $item['excerpt'],
            'content' => $item['body'] ?? '<p>'.$item['excerpt'].'</p>',
            'cover' => $gallery[0] ?? null,
            'gallery' => $gallery,
            'category' => $item['category'] ?? null,
            'tags' => $item['tags'] ?? [],
            'price' => (float) $item['price'],
            'old_price' => isset($item['old_price']) ? (float) $item['old_price'] : null,
            'currency' => 'TRY',
            'discount' => $discount,
            'badge' => $item['badge'] ?? null,
            'rating' => (float) ($item['rating'] ?? 0),
            'reviews' => (int) ($item['reviews'] ?? 0),
            'stock' => (int) ($item['stock'] ?? 0),
            'sku' => $item['sku'] ?? '',
            'variants' => $item['variants'] ?? [],
            'specs' => $item['specs'] ?? [],
            'tabs' => $this->defaultProductTabs($item),
            'accordions' => $this->defaultProductFaq($item),
            'created_at' => now()->subDays(20)->toIso8601String(),
            'updated_at' => now()->subDays(3)->toIso8601String(),
            'seo' => [],
            'jsonld' => null,
        ];
    }

    private function defaultProductTabs(array $item): array
    {
        $specs = '<ul>';
        foreach ($item['specs'] ?? [] as $spec) {
            $specs .= '<li><strong>'.e($spec['label']).':</strong> '.e($spec['value']).'</li>';
        }
        $specs .= '</ul>';

        return [
            ['title' => 'Açıklama', 'content' => '<p>'.e($item['excerpt']).'</p>'],
            ['title' => 'Teknik Özellikler', 'content' => $specs],
            ['title' => 'Kargo & İade', 'content' => '<p>1-3 iş günü içinde kargoya verilir. 14 gün içinde koşulsuz iade hakkı bulunur.</p>'],
        ];
    }

    private function defaultProductFaq(array $item): array
    {
        return [
            ['title' => 'Kargo ne zaman gelir?', 'content' => 'Siparişler 1-3 iş günü içinde kargoya verilir, teslimat 1-4 iş günü sürer.'],
            ['title' => 'İade ve değişim var mı?', 'content' => '14 gün içinde koşulsuz iade ve ücretsiz değişim hakkınız bulunur.'],
            ['title' => 'Garanti kapsamı nedir?', 'content' => 'Ürünler 2 yıl üretici garantisi ile gelir.'],
        ];
    }

    private function page(string $slug, string $title, string $excerpt, string $body): array
    {
        return [
            'slug' => $slug,
            'title' => $title,
            'excerpt' => $excerpt,
            'body' => $body,
            'cover' => null,
        ];
    }

    /** Demo kaydını normalize edilmiş içerik yapısına çevirir. */
    private function normalizeDemo(array $item, string $type): array
    {
        return [
            'type' => $type,
            'id' => $item['id'] ?? crc32($item['slug']),
            'slug' => $item['slug'],
            'title' => $item['title'] ?? '',
            'excerpt' => $item['excerpt'] ?? Str::limit(strip_tags($item['body'] ?? ''), 160),
            'content' => $item['body'] ?? '',
            'cover' => $item['cover'] ?? null,
            'category' => $item['category'] ?? null,
            'tags' => $item['tags'] ?? [],
            'author' => $item['author'] ?? 'Waaiy Editör',
            'created_at' => $item['created_at'] ?? now()->toIso8601String(),
            'updated_at' => $item['updated_at'] ?? now()->toIso8601String(),
            'reading_time' => $item['reading_time'] ?? 5,
            'tabs' => [],
            'accordions' => [],
            'fields' => [],
            'photos' => [],
            'seo' => [],
            'jsonld' => null,
        ];
    }
}
