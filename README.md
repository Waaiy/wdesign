# Waaiy Design — Headless CMS Örnek Tasarım

[Waaiy](https://waaiy.dev) headless CMS'in public REST API'sini tüketen, **tek iskelet üzerinden
çoklu varyasyon** sunan, **SEO odaklı** ve **çok dilli** bir Laravel ön yüz (frontend) şablonu.

Tasarım iskeleti; header menü (1-2-3), blog görünümü (1-2-3), 4 sektör ön ayarı ve canlı **RGB
renk seçiciyi** içeren "kayan kutu" (Tasarım Stüdyosu) ile dakikalar içinde farklı kimliklere
bürünebilir. **API anahtarı olmadan da** zengin demo içerikle eksiksiz çalışır.

---

## Özellikler

### Tasarım
- **Header varyasyonları:** 1 · Klasik (logo sol) · 2 · Merkez (logo ortada) · 3 · Yüzen (pill bar + mega menü)
- **Blog varyasyonları:** 1 · Izgara · 2 · Liste · 3 · Dergi (aynı kartlar, CSS ile değişir)
- **Sektör ön ayarları (multi-proposal):** Dijital Ajans · SaaS & Teknoloji · Klinik & Sağlık · Restoran & Kafe — renk, tipografi, köşe ve içerik metinlerini birlikte değiştirir
- **Kayan kutu (Tasarım Stüdyosu):** varyasyon seçicileri, sektör seçici, **RGB color picker** (native + R/G/B slider + hex, otomatik 50–900 palet üretimi), köşe yuvarlaklığı, sıfırla
- Tercihler `localStorage`'da saklanır ve ilk boyamadan önce uygulanır (FOUC yok)
- Modern prensipler: cam efektli sticky header, gradyan orb'lar, yumuşak gölgeler, reveal animasyonları, `prefers-reduced-motion` desteği

### İçerik
- **Blog:** liste / kategori / detay (içindekiler, yorum formu, ilgili yazılar)
- **Ürünler:** liste / kategori / **detay** (galeri + küçük görseller, fiyat + indirim, varyant seçicileri, adet, sekmeler, SSS, benzer ürünler)
- **Sayfalar:** CMS sayfa endpointi; `/slug` güzel adresleri
- **İletişim:** CMS'te sayfa varsa onu, yoksa yerleşik sayfayı (form + ayarlardan gelen telefon/e-posta/adres) gösterir
- **Etkileşim:** bülten aboneliği, form gönderimi, yorum (Waaiy uçlarına proxy)
- **Önyüz çevirileri:** UI metinleri Waaiy "Önyüz Çevirileri"nden okunur; yoksa **auto-register** ile kaydedilir

### SEO
- `title`, `description`, `robots`, `canonical`, `keywords`
- Open Graph + Twitter Card
- `hreflang` alternatifleri + `x-default`
- JSON-LD (schema.org): `Organization`, `WebSite`, `BlogPosting` / `WebPage`, `Product` + `Offer` + `AggregateRating`, `FAQPage`, `BreadcrumbList`
- `/sitemap.xml`, `/sitemap-{tip}-{sayfa}.xml`, `/robots.txt` (Waaiy'den proxy)
- 404 dalında panelde tanımlı **301/302 yönlendirme** kontrolü
- Semantik HTML, breadcrumb, erişilebilirlik iyileştirmeleri

### Altyapı
- Waaiy SDK (`waaiy/api`) → normalize edilmiş içerik
- Önbellekleme (429 / istek sınırı riskini azaltır, sayfa hızı)
- Hata/anahtarsızlık durumunda **güvenli fallback** (demo içerik + debug uyarısı)
- Çok dillilik: URL ön eki olarak dil kısa adı (`/tr/...`, `/en/...`)

---

## Gereksinimler

- PHP **8.3+**
- Composer 2
- Node.js **20+** ve npm
- SQLite (varsayılan; session/cache için) veya MySQL/PostgreSQL

---

## Kurulum

**Hızlı yol** (bağımlılıklar + `.env` + key + migrate + varlık derleme):

```bash
composer setup
php artisan serve
```

**Adım adım:**

```bash
# 1) Bağımlılıklar
composer install
npm install

# 2) Ortam dosyası ve anahtar
cp .env.example .env
php artisan key:generate

# 3) Veritabanı (session/cache tabloları)
php artisan migrate

# 4) Varlıkları derle
npm run build      # üretim
# ya da geliştirme sırasında:
npm run dev

# 5) Çalıştır
php artisan serve
```

Uygulama `http://localhost:8000` adresinde açılır ve kök `/` otomatik olarak aktif dile
(ör. `/tr`) yönlendirir.

---

## Yapılandırma (`.env`)

```dotenv
APP_NAME="Waaiy Design"
APP_URL=http://localhost:8000

# --- Waaiy Headless CMS ---
WAAIY_API_URL=https://api.waaiy.dev
WAAIY_PUBLIC_KEY=
WAAIY_PRIVATE_KEY=
WAAIY_USER_KEY=
WAAIY_MEDIA_URL=

WAAIY_MENU_SLUG=ana-menu
# auto | demo | live
WAAIY_MODE=auto

WAAIY_CACHE=true
WAAIY_CACHE_TTL=300

# UI metinlerini yoksa otomatik kaydet (get-or-create)
WAAIY_TRANSLATIONS_AUTOREGISTER=true
```

### Çalışma modları (`WAAIY_MODE`)

| Mod    | Davranış                                                                 |
|--------|--------------------------------------------------------------------------|
| `auto` | Üç anahtar da doluysa **canlı API**, değilse demo. **(Önerilen)**        |
| `live` | Her zaman API denenir; anahtar yoksa güvenli şekilde demoya düşer.        |
| `demo` | Her zaman örnek içerik (sunum / geliştirme).                             |

> Üç anahtardan biri bile boşsa uygulama `auto` modda otomatik olarak demoya düşer; tasarım
> eksiksiz görüntülenir.

---

## Komutlar

```bash
# API anahtarlarını ve bağlantıyı test eder (her uç noktanın durumunu listeler)
php artisan waaiy:check

# Anasayfa metinlerini Waaiy "Önyüz Çevirileri"ne kaydeder (yoksa oluşturur)
php artisan waaiy:translations
php artisan waaiy:translations --throttle=6   # yazma sınırına takılmamak için bekleme
```

---

## URL yapısı

İçerik adresleri **dil kısa adı** ön ekiyle başlar. Dil listesi Waaiy'den gelir; kısa ad küçük
harfe çevrilir (`TR` → `tr`).

| Amaç              | Örnek                                       |
|-------------------|---------------------------------------------|
| Kök (yönlendirme) | `/` → `/tr`                                 |
| Anasayfa          | `/tr`                                        |
| Blog              | `/tr/blog`, `/tr/blogs/{kategori}`          |
| Blog detay        | `/tr/blog/{slug}` · `/tr/blog-detay/{slug}` |
| Ürünler           | `/tr/urunler`, `/tr/urunler/{kategori}`     |
| Ürün detay        | `/tr/urun/{slug}`                            |
| Sayfa             | `/tr/{slug}` · `/tr/page/{slug}`            |
| İletişim          | `/tr/iletisim`                              |
| SEO               | `/sitemap.xml`, `/robots.txt` (öneksiz)     |

Geçersiz dil öneki **404** döner. Header'daki dil değiştirici, bulunduğunuz sayfanın aynı yolunu
hedef dilde açar (ör. `/tr/blog/x` → `/en/blog/x`).

---

## Mimari

```
app/
├── Console/Commands/         # waaiy:check, waaiy:translations
├── Http/
│   ├── Controllers/          # Home, Blog, Product, Page, Fallback, Seo, Interaction, Locale
│   └── Middleware/           # SetLocale (dil öneki), ShareWaaiyContext (ayarlar → config)
├── Services/Waaiy/
│   ├── WaaiyClient.php       # waaiy/api istemcisini kurar (mod/anahtar)
│   ├── ContentRepository.php # SDK'yı sarar: normalize + önbellek + fallback
│   └── DemoContent.php       # API'siz örnek içerik (blog, sayfa, ürün)
└── Support/
    ├── Design.php            # varyasyon + sektör tanımları (tek kaynak)
    ├── HomeTexts.php         # anasayfa çeviri anahtarları
    ├── Locales.php           # dil öneki / hreflang yardımcıları
    └── Seo.php               # meta + JSON-LD üretici

config/waaiy.php              # API, önbellek, önek, SEO, atıf ayarları
routes/web.php                # {locale} grubu + SEO uçları
resources/
├── css/app.css               # tasarım sistemi (CSS değişkenleri + bileşenler)
├── js/app.js                 # kayan kutu, renk seçici, galeri, sekmeler, reveal
└── views/
    ├── layouts/app.blade.php
    ├── partials/             # seo, header(+headers/1-2-3), footer, customizer, breadcrumbs
    └── pages/                # home, blog/, product/, page, contact, errors/404
```

**Veri akışı:** `Controller → ContentRepository → WaaiyClient → Waaiy\API`
→ normalize edilmiş içerik → Blade. API hatalarında (404 hariç) loglanır ve güvenli varsayılana
düşülür; `APP_DEBUG=true` iken sayfanın sol altında küçük bir uyarı çubuğu görünür.

---

## Tasarım Stüdyosu (kayan kutu)

Sağ kenardaki **Stüdyo** sekmesinden:

1. **Header Menü** — 1 / 2 / 3
2. **Blog Görünümü** — 1 / 2 / 3
3. **Sektör Ön Ayarı** — 4 sektör (renk + tipografi + köşe + metin)
4. **Ana Renk / Vurgu Rengi** — RGB sliders + native color picker + hex (otomatik palet)
5. **Köşe Yuvarlaklığı** — 0–32px
6. **Sıfırla**

Tüm seçimler `localStorage`'da saklanır. Varsayılanlar `app/Support/Design.php` dosyasındadır.

---

## SEO notları

- **Canonical & hreflang** dile göre üretilir.
- İçerik JSON-LD'si Waaiy panelinden geliyorsa o kullanılır; yoksa otomatik üretilir.
- Canlı `/sitemap.xml` Waaiy'in kendi sitemap'ini döndürür. Tamamen yerelleştirilmiş bir sitemap
  gerekiyorsa özelleştirilebilir.
- `admin` panelindeki **menü** kaydı boş/bozuksa (ör. `ana-menu` bulunamazsa) uygulama güvenli
  varsayılan menüye düşer.

---

## Üretim (deployment)

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan optimize        # config + route + view cache
```

Web sunucusunun document root'u `public/` dizini olmalıdır.

---

## Atıf

Footer (copyrights) bölümünde, içerik yönetim sistemine **`içerik yönetim sistemi`** metniyle
[waaiy.dev](https://waaiy.dev) bağlantısı yer alır (`config/waaiy.php` → `cms`).

---

## Lisans

MIT.
