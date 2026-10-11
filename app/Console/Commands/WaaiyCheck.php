<?php

namespace App\Console\Commands;

use App\Services\Waaiy\WaaiyClient;
use Illuminate\Console\Command;
use Throwable;

class WaaiyCheck extends Command
{
    protected $signature = 'waaiy:check';

    protected $description = 'Waaiy API anahtarlarını ve bağlantısını test eder';

    public function handle(WaaiyClient $client): int
    {
        $keys = config('waaiy.keys', []);

        $this->table(['Ayar', 'Değer'], [
            ['API URL', (string) config('waaiy.base_url')],
            ['Mod (WAAIY_MODE)', (string) config('waaiy.mode')],
            ['WAAIY_PUBLIC_KEY', ! empty($keys['public']) ? 'tanımlı' : 'YOK'],
            ['WAAIY_PRIVATE_KEY', ! empty($keys['private']) ? 'tanımlı' : 'YOK'],
            ['WAAIY_USER_KEY', ! empty($keys['user']) ? 'tanımlı' : 'YOK'],
            ['configured()', $client->configured() ? 'true' : 'false'],
            ['demo()', $client->demo() ? 'true (örnek içerik)' : 'false (canlı API)'],
        ]);

        if (app()->configurationIsCached()) {
            $this->warn('Yapılandırma önbelleği açık. .env değişikliği için: php artisan config:clear && php artisan config:cache');
        }

        if ($client->demo()) {
            $this->warn('Şu an demo modunda. Anahtarları .env içine girip `php artisan config:clear` çalıştırın.');

            return self::SUCCESS;
        }

        try {
            $api = $client->api();
        } catch (Throwable $e) {
            $this->error('İstemci kurulamadı: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->newLine();

        $checks = [
            'settings' => fn () => count((array) $api->settings(true)).' kayıt',
            'languages' => fn () => count((array) $api->languages(true)).' dil',
            'translations' => fn () => count($api->translation_map()).' anahtar',
            'menu ('.config('waaiy.menu').')' => function () use ($api) {
                $menu = $api->menuitems((string) config('waaiy.menu'));

                return count((array) $menu).' öğe';
            },
            'blogcategory' => function () use ($api) {
                $list = $api->blogcategory('', 1, 3);
                $count = is_array($list->__BLOGS ?? null) ? count($list->__BLOGS) : 0;

                return $count.' yazı (total: '.($list->__TOTAL_BLOGS ?? '?').')';
            },
            'blogcategories' => fn () => count((array) $api->blogcategories()).' kök kategori',
        ];

        $allOk = true;
        foreach ($checks as $label => $check) {
            try {
                $this->info('✓ '.$label.': '.$check());
            } catch (Throwable $e) {
                $allOk = false;
                $this->error('✗ '.$label.': '.$e->getMessage());
            }
        }

        $this->newLine();
        if ($allOk) {
            $this->info('Bağlantı başarılı. Canlı API kullanılıyor.');
        } else {
            $this->warn('Bazı uçlar hata verdi. Yukarıdaki ✗ satırlarına bakın (uygulama otomatik olarak güvenli varsayılanlara düşer).');
        }

        return self::SUCCESS;
    }
}
