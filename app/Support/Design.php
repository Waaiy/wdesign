<?php

namespace App\Support;

/**
 * Tasarım varyasyonlarının tek kaynağı.
 *
 * Tek bir tasarım iskeleti; header menü (1-2-3), blog görünümü (1-2-3) ve
 * sektör ön ayarları (multi-proposal) ile farklı kimliklere bürünebilir.
 * Hem Blade tarafı hem de önyüz "kayan kutu" (customizer) bu tanımı kullanır.
 */
class Design
{
    public const DEFAULT_HEADER = 1;

    public const DEFAULT_BLOG_LAYOUT = 1;

    public const DEFAULT_SECTOR = 'agency';

    /** @return array<int, array{label:string, description:string}> */
    public static function headers(): array
    {
        return [
            1 => [
                'label' => 'Klasik',
                'description' => 'Logo solda, menü ortada, CTA sağda. Kurumsal ve güvenilir.',
            ],
            2 => [
                'label' => 'Merkez',
                'description' => 'Logo merkezde, menü iki yana bölünmüş. Editöryel ve dengeli.',
            ],
            3 => [
                'label' => 'Yüzen',
                'description' => 'Sayfa üzerinde süzülen yuvarlak bar + mega menü. Modern ve cesur.',
            ],
        ];
    }

    /** @return array<int, array{label:string, description:string}> */
    public static function blogLayouts(): array
    {
        return [
            1 => [
                'label' => 'Izgara',
                'description' => 'Eşit kartlardan oluşan 3 kolonlu ızgara düzeni.',
            ],
            2 => [
                'label' => 'Liste',
                'description' => 'Görsel solda, içerik sağda yatay liste düzeni.',
            ],
            3 => [
                'label' => 'Dergi',
                'description' => 'Öne çıkan yazı + yan sütun; editöryel dergi düzeni.',
            ],
        ];
    }

    /**
     * Sektör ön ayarları (multi-proposal).
     *
     * Her sektör bir palet, tipografi, köşe yuvarlaklığı ve örnek metin seti
     * taşır. Renk/köşe değişimleri CSS değişkenleri ile anında uygulanır;
     * metinler data-* işaretleri üzerinden JS tarafından yer değiştirir.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function sectors(): array
    {
        return [
            'agency' => [
                'label' => 'Dijital Ajans',
                'tagline' => 'Yaratıcı & cesur',
                'primary' => '#5b2ee6',
                'accent' => '#ff5a8a',
                'bg' => '#f6f6fb',
                'surface' => '#ffffff',
                'text' => '#101234',
                'muted' => '#5b5e7a',
                'dark' => '#0c0c22',
                'radius' => 18,
                'font' => "'Sora', 'Instrument Sans', sans-serif",
                'nav' => ['Hizmetler', 'İşler', 'Stüdyo', 'Blog', 'İletişim'],
                'hero' => [
                    'eyebrow' => 'Dijital Ajans',
                    'title' => 'Markanızı dijitalde bir üst seviyeye taşıyoruz.',
                    'subtitle' => 'Strateji, tasarım ve teknolojiyi tek çatı altında birleştiren yaratıcı stüdyo. Ölçülebilir sonuçlar üretiriz.',
                    'primary_cta' => 'Projeyi Başlat',
                    'secondary_cta' => 'İşlerimizi Gör',
                ],
                'features' => [
                    ['icon' => 'spark', 'title' => 'Marka & Kimlik', 'text' => 'Ayrışan bir görsel dil ve tutarlı marka deneyimi tasarlarız.'],
                    ['icon' => 'layers', 'title' => 'Dijital Ürün', 'text' => 'Kullanıcı odaklı, ölçeklenebilir arayüzler ve web uygulamaları.'],
                    ['icon' => 'chart', 'title' => 'Büyüme & SEO', 'text' => 'Veriye dayalı kampanyalar ve teknik SEO ile sürdürülebilir büyüme.'],
                ],
            ],

            'saas' => [
                'label' => 'SaaS & Teknoloji',
                'tagline' => 'Sade & ölçeklenebilir',
                'primary' => '#2563eb',
                'accent' => '#06b6d4',
                'bg' => '#f5f8fc',
                'surface' => '#ffffff',
                'text' => '#0b1220',
                'muted' => '#55607a',
                'dark' => '#071019',
                'radius' => 14,
                'font' => "'Sora', 'Instrument Sans', sans-serif",
                'nav' => ['Ürün', 'Özellikler', 'Entegrasyonlar', 'Fiyatlandırma', 'Dokümantasyon'],
                'hero' => [
                    'eyebrow' => 'Yeni Nesil Platform',
                    'title' => 'Ekibinizin hızına ayak uyduran altyapı.',
                    'subtitle' => 'Kod yazmadan yayına alın, API ile sınırsız özelleştirin. Güvenli, hızlı ve ölçeklenebilir.',
                    'primary_cta' => 'Ücretsiz Başla',
                    'secondary_cta' => 'Canlı Demo',
                ],
                'features' => [
                    ['icon' => 'bolt', 'title' => 'Hızlı API', 'text' => 'Dakikada yüzlerce isteğe yanıt veren RESTful altyapı.'],
                    ['icon' => 'shield', 'title' => 'Güvenli', 'text' => '2FA, rol bazlı erişim ve kurumsal düzeyde güvenlik.'],
                    ['icon' => 'plug', 'title' => 'Entegrasyon', 'text' => 'Kullandığınız araçlarla sorunsuz bağlantı ve otomasyon.'],
                ],
            ],

            'clinic' => [
                'label' => 'Klinik & Sağlık',
                'tagline' => 'Güven veren & berrak',
                'primary' => '#0d9488',
                'accent' => '#22c55e',
                'bg' => '#f3fbf9',
                'surface' => '#ffffff',
                'text' => '#07211d',
                'muted' => '#4b6b66',
                'dark' => '#052a26',
                'radius' => 12,
                'font' => "'Instrument Sans', ui-sans-serif, sans-serif",
                'nav' => ['Bölümler', 'Hekimler', 'Randevu', 'Sağlık Rehberi', 'İletişim'],
                'hero' => [
                    'eyebrow' => 'Sağlık Merkezi',
                    'title' => 'Sağlığınız için modern, güvenilir bakım.',
                    'subtitle' => 'Uzman hekim kadrosu ve güncel teknoloji ile kişiye özel tedavi planları sunuyoruz.',
                    'primary_cta' => 'Randevu Al',
                    'secondary_cta' => 'Bölümlerimiz',
                ],
                'features' => [
                    ['icon' => 'heart', 'title' => 'Uzman Kadro', 'text' => 'Alanında deneyimli hekimler ve hasta odaklı yaklaşım.'],
                    ['icon' => 'clock', 'title' => 'Hızlı Randevu', 'text' => 'Online randevu ile beklemeden, dakikalar içinde planlama.'],
                    ['icon' => 'shield', 'title' => 'Modern Teknoloji', 'text' => 'Güncel görüntüleme ve teşhis cihazları ile doğru tanı.'],
                ],
            ],

            'restaurant' => [
                'label' => 'Restoran & Kafe',
                'tagline' => 'Sıcak & davetkâr',
                'primary' => '#b45309',
                'accent' => '#e11d48',
                'bg' => '#fdf7ef',
                'surface' => '#fffdf9',
                'text' => '#2a180d',
                'muted' => '#7a5c45',
                'dark' => '#2a1508',
                'radius' => 8,
                'font' => "'Playfair Display', Georgia, serif",
                'nav' => ['Menü', 'Hikayemiz', 'Şubeler', 'Rezervasyon', 'Galeri'],
                'hero' => [
                    'eyebrow' => 'Mutfak & Lezzet',
                    'title' => 'Her tabakta taze bir hikaye.',
                    'subtitle' => 'Yerel üreticilerden gelen malzemelerle hazırlanan, mevsimlik ve özenli bir mutfak.',
                    'primary_cta' => 'Rezervasyon Yap',
                    'secondary_cta' => 'Menüyü İncele',
                ],
                'features' => [
                    ['icon' => 'leaf', 'title' => 'Taze Malzeme', 'text' => 'Her gün yerel üreticiden gelen mevsimlik ürünler.'],
                    ['icon' => 'chef', 'title' => 'Usta Şef', 'text' => 'Geleneği modern dokunuşlarla buluşturan imza tarifler.'],
                    ['icon' => 'heart', 'title' => 'Samimi Ortam', 'text' => 'Ailenizle ve dostlarınızla keyifli vakit geçireceğiniz sıcak mekân.'],
                ],
            ],
        ];
    }

    /** @return array<int, string> */
    public static function headerNumbers(): array
    {
        return array_keys(self::headers());
    }

    /** @return array<int, string> */
    public static function blogLayoutNumbers(): array
    {
        return array_keys(self::blogLayouts());
    }

    /** @return array<string, string> */
    public static function sectorKeys(): array
    {
        return array_keys(self::sectors());
    }

    public static function sector(string $key): array
    {
        $sectors = self::sectors();

        return $sectors[$key] ?? $sectors[self::DEFAULT_SECTOR];
    }

    /**
     * Kayan kutu için JS'e gömülecek özet ayarlar (renk/metin/etiket).
     *
     * @return array<string, mixed>
     */
    public static function toClientConfig(): array
    {
        return [
            'defaults' => [
                'header' => self::DEFAULT_HEADER,
                'blogLayout' => self::DEFAULT_BLOG_LAYOUT,
                'sector' => self::DEFAULT_SECTOR,
            ],
            'headers' => self::headers(),
            'blogLayouts' => self::blogLayouts(),
            'sectors' => self::sectors(),
        ];
    }
}
