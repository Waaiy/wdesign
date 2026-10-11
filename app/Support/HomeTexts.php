<?php

namespace App\Support;

use App\Services\Waaiy\ContentRepository;

/**
 * Anasayfa metinleri.
 *
 * Tüm metinler Waaiy "Önyüz Çevirileri" modülü üzerinden yönetilir:
 * `translation(anahtar, varsayilan)` çağrısı anahtar panelde yoksa varsayılan
 * metinle OTOMATİK kaydeder (get-or-create) ve aynı kaynaktan okur.
 *
 * defaults() → panelde oluşturulacak varsayılan metinler (seed komutu kullanır)
 * all()      → anahtar→çeviri haritası (controller kullanır)
 */
class HomeTexts
{
    /** @return array<string, string> anahtar => varsayılan metin */
    public static function defaults(): array
    {
        $sector = Design::sector(Design::DEFAULT_SECTOR);
        $f = $sector['features'];

        return [
            'home.hero.eyebrow' => $sector['hero']['eyebrow'],
            'home.hero.title' => $sector['hero']['title'],
            'home.hero.subtitle' => $sector['hero']['subtitle'],
            'home.hero.cta.primary' => $sector['hero']['primary_cta'],
            'home.hero.cta.secondary' => $sector['hero']['secondary_cta'],

            'home.trust.1' => 'Headless & API öncelikli',
            'home.trust.2' => 'SEO odaklı altyapı',
            'home.trust.3' => 'Çok dilli yönetim',

            'home.stats.1.value' => '1000+',
            'home.stats.1.label' => 'Yönetilen site',
            'home.stats.2.value' => '120/dk',
            'home.stats.2.label' => 'Okuma limiti',
            'home.stats.3.value' => '2.1',
            'home.stats.3.label' => 'API sürümü',
            'home.stats.4.value' => '%99.9',
            'home.stats.4.label' => 'Çalışma süresi',

            'home.why.eyebrow' => 'Neden bu tasarım',
            'home.why.title' => 'Tek iskelet, sektöre göre şekillenen içerik.',
            'home.why.sub' => 'Sağdaki stüdyodan sektörü değiştirin; renk, tipografi ve metinler anında uyum sağlar.',

            'home.features.1.title' => $f[0]['title'],
            'home.features.1.text' => $f[0]['text'],
            'home.features.2.title' => $f[1]['title'],
            'home.features.2.text' => $f[1]['text'],
            'home.features.3.title' => $f[2]['title'],
            'home.features.3.text' => $f[2]['text'],

            'home.variants.eyebrow' => 'Varyasyonlar',
            'home.variants.title' => "Header ve blog görünümü 3'er seçenek.",
            'home.variants.sub' => 'Tasarımı bozmadan yalnızca yerleşimi değiştirin. Hepsi aynı içerikten beslenir.',

            'home.blog.eyebrow' => 'Blog',
            'home.blog.title' => 'Güncel yazılar',
            'home.blog.all' => 'Tüm yazılar',

            'home.products.eyebrow' => 'Mağaza',
            'home.products.title' => 'Öne çıkan ürünler',
            'home.products.all' => 'Tüm ürünler',

            'home.cta.title' => 'Tasarımı kendi içeriğinizle canlandırın.',
            'home.cta.sub' => 'Waaiy panelinden içeriklerinizi girin; bu şablon anında sizinki olsun.',
            'home.cta.button.primary' => "Waaiy'i Keşfet",
            'home.cta.button.secondary' => 'İletişime Geç',
        ];
    }

    /**
     * Anasayfa şablonunun beklediği iç içe yapı (her metin translation()'dan).
     *
     * @return array<string, mixed>
     */
    public static function all(ContentRepository $waaiy): array
    {
        $sector = Design::sector(Design::DEFAULT_SECTOR);
        $defaults = self::defaults();
        $t = fn (string $key): string => $waaiy->translation($key, $defaults[$key] ?? $key);

        $features = [];
        foreach ($sector['features'] as $i => $feature) {
            $n = $i + 1;
            $features[] = [
                'icon' => $feature['icon'],
                'title' => $t("home.features.{$n}.title"),
                'text' => $t("home.features.{$n}.text"),
            ];
        }

        return [
            'hero' => [
                'eyebrow' => $t('home.hero.eyebrow'),
                'title' => $t('home.hero.title'),
                'subtitle' => $t('home.hero.subtitle'),
                'primary_cta' => $t('home.hero.cta.primary'),
                'secondary_cta' => $t('home.hero.cta.secondary'),
            ],
            'trust' => [$t('home.trust.1'), $t('home.trust.2'), $t('home.trust.3')],
            'stats' => [
                ['value' => $t('home.stats.1.value'), 'label' => $t('home.stats.1.label')],
                ['value' => $t('home.stats.2.value'), 'label' => $t('home.stats.2.label')],
                ['value' => $t('home.stats.3.value'), 'label' => $t('home.stats.3.label')],
                ['value' => $t('home.stats.4.value'), 'label' => $t('home.stats.4.label')],
            ],
            'features' => $features,
            'why' => [
                'eyebrow' => $t('home.why.eyebrow'),
                'title' => $t('home.why.title'),
                'sub' => $t('home.why.sub'),
            ],
            'variants' => [
                'eyebrow' => $t('home.variants.eyebrow'),
                'title' => $t('home.variants.title'),
                'sub' => $t('home.variants.sub'),
            ],
            'blog' => [
                'eyebrow' => $t('home.blog.eyebrow'),
                'title' => $t('home.blog.title'),
                'all' => $t('home.blog.all'),
            ],
            'products' => [
                'eyebrow' => $t('home.products.eyebrow'),
                'title' => $t('home.products.title'),
                'all' => $t('home.products.all'),
            ],
            'cta' => [
                'title' => $t('home.cta.title'),
                'sub' => $t('home.cta.sub'),
                'primary' => $t('home.cta.button.primary'),
                'secondary' => $t('home.cta.button.secondary'),
            ],
        ];
    }
}
