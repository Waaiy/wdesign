<?php

namespace App\Http\Controllers;

use App\Support\Locales;
use Illuminate\Http\RedirectResponse;

/**
 * Dil kökü: / → aktif ya da varsayılan dilin anasayfasına yönlendirir.
 */
class LocaleController extends Controller
{
    public function root(): RedirectResponse
    {
        $code = session('waaiy_lang') ?: Locales::default();

        return redirect()->to('/'.$code);
    }
}
