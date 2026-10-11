<?php

namespace App\Http\Controllers;

use App\Services\Waaiy\ContentRepository;
use App\Support\Locales;
use Illuminate\Http\Request;

/**
 * Dil önekli güzel sayfa adresleri için son durak.
 *
 * Sırasıyla: sayfa slug'ı olarak dener → panelde tanımlı 301/302 yönlendirmesi
 * arar → bulamazsa 404 döner. Böylece eski adresler SEO değerini korur.
 */
class FallbackController extends Controller
{
    public function page(Request $request, ContentRepository $waaiy)
    {
        $slug = (string) $request->route('slug');

        if ($slug !== '' && $waaiy->page($slug)) {
            return app(PageController::class)->renderPage($waaiy, $slug);
        }

        if ($redirect = $waaiy->redirect(Locales::path())) {
            return redirect()->to($redirect->yeni_url, (int) ($redirect->tip ?? 301));
        }

        abort(404);
    }
}
