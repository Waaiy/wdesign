<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\FallbackController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InteractionController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SeoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Kök — aktif/varsayılan dil önekine yönlendirir (ör. / → /tr)
|--------------------------------------------------------------------------
*/
Route::get('/', [LocaleController::class, 'root'])->name('root');

/*
|--------------------------------------------------------------------------
| SEO uçları (dil öneki yok — arama motorları kökten ister)
|--------------------------------------------------------------------------
*/
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/sitemap-{type}-{page}.xml', [SeoController::class, 'sitemapPart'])
    ->whereNumber('page')->name('sitemap.part');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');

/*
|--------------------------------------------------------------------------
| İçerik — dil kısa adı URL ön eki: /tr/... , /en/...
|--------------------------------------------------------------------------
*/
Route::pattern('locale', '[A-Za-z]{2,5}');

Route::prefix('{locale}')->group(function () {
    // Anasayfa
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Blog
    Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
    Route::get('/blogs/{slug}', [BlogController::class, 'category'])
        ->where('slug', '[A-Za-z0-9\-_]+')->name('blog.category');
    Route::get('/blog/{slug}', [BlogController::class, 'show'])
        ->where('slug', '[A-Za-z0-9\-_]+')->name('blog.show');
    Route::get('/blog-detay/{slug}', [BlogController::class, 'show'])
        ->where('slug', '[A-Za-z0-9\-_]+')->name('blog.show.detay');

    // Ürünler
    Route::get('/urunler', [ProductController::class, 'index'])->name('product.index');
    Route::get('/urunler/{slug}', [ProductController::class, 'category'])
        ->where('slug', '[A-Za-z0-9\-_]+')->name('product.category');
    Route::get('/urun/{slug}', [ProductController::class, 'show'])
        ->where('slug', '[A-Za-z0-9\-_]+')->name('product.show');

    // İletişim (CMS sayfası varsa onu, yoksa yerleşik sayfayı gösterir)
    Route::get('/iletisim', [PageController::class, 'contact'])->name('contact');

    // Statik CMS sayfası
    Route::get('/page/{slug}', [PageController::class, 'show'])
        ->where('slug', '[A-Za-z0-9\-_]+')->name('page.show');

    // Etkileşim uçları
    Route::post('/abone-ol', [InteractionController::class, 'subscribe'])->name('subscribe');
    Route::post('/form-gonder', [InteractionController::class, 'formSubmit'])->name('form.submit');
    Route::post('/yorum-yap', [InteractionController::class, 'comment'])->name('comment');

    // En sonda: güzel sayfa adresi (/tr/hakkimizda) → sayfa / 301-302 yönlendirme / 404
    Route::get('/{slug}', [FallbackController::class, 'page'])
        ->where('slug', '[A-Za-z0-9\-_]+')->name('page.pretty');
});
