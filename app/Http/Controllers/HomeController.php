<?php

namespace App\Http\Controllers;

use App\Services\Waaiy\ContentRepository;
use App\Support\Design;
use App\Support\HomeTexts;
use App\Support\Seo;

class HomeController extends Controller
{
    public function index(ContentRepository $waaiy)
    {
        $sector = Design::sector(Design::DEFAULT_SECTOR);
        $t = HomeTexts::all($waaiy);
        $latest = $waaiy->blogs('', 1, 3);
        $products = $waaiy->products('', 1, 4)['items'];

        $seo = Seo::make([
            'title' => ($waaiy->settings()['site_basligi'] ?? config('app.name')).' | '.($sector['tagline'] ?? ''),
            'append_site' => false,
            'description' => $t['hero']['subtitle'],
            'canonical' => route('home'),
            'type' => 'website',
            'alternates' => \App\Support\Locales::alternatesForCurrent(),
            'breadcrumbs' => [
                ['name' => 'Anasayfa', 'url' => route('home')],
            ],
        ]);

        return view('pages.home', [
            'sector' => $sector,
            't' => $t,
            'posts' => $latest['items'],
            'products' => $products,
            'seo' => $seo,
            'blogLayout' => Design::DEFAULT_BLOG_LAYOUT,
        ]);
    }
}
