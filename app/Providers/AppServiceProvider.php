<?php

namespace App\Providers;

use App\Services\Waaiy\ContentRepository;
use App\Services\Waaiy\DemoContent;
use App\Services\Waaiy\WaaiyClient;
use App\Support\Design;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(WaaiyClient::class);
        $this->app->singleton(DemoContent::class);
        $this->app->singleton(ContentRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $repo = app(ContentRepository::class);
            $settings = $repo->settings();
            $languages = $repo->languages();
            $currentLang = session('waaiy_lang') ?: ($languages[0]['short'] ?? 'tr');

            $view->with([
                'waaiy' => $repo,
                'siteSettings' => $settings,
                'siteName' => $settings['site_basligi'] ?? config('waaiy.seo.site_name'),
                'design' => Design::toClientConfig(),
                'languages' => $languages,
                'currentLang' => $currentLang,
            ]);
        });
    }
}
