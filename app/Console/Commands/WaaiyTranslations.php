<?php

namespace App\Console\Commands;

use App\Services\Waaiy\ContentRepository;
use App\Services\Waaiy\WaaiyClient;
use App\Support\HomeTexts;
use Illuminate\Console\Command;
use Throwable;

class WaaiyTranslations extends Command
{
    protected $signature = 'waaiy:translations {--throttle=0 : Kayıtlar arası bekleme (saniye)}';

    protected $description = 'Anasayfa metinlerini Waaiy önyüz çevirilerine kaydeder (yoksa oluşturur)';

    public function handle(ContentRepository $repo, WaaiyClient $client): int
    {
        if ($client->demo()) {
            $this->warn('Demo modunda çalışıyorsunuz; kayıt yapılmaz. Önce .env içine API anahtarlarını girin.');

            return self::SUCCESS;
        }

        $defaults = HomeTexts::defaults();
        $throttle = max(0, (int) $this->option('throttle'));
        $created = 0;

        $bar = $this->output->createProgressBar(count($defaults));
        $bar->start();

        foreach ($defaults as $key => $default) {
            try {
                // translation() → yoksa varsayılan metinle otomatik kaydeder.
                $repo->translation($key, $default);
                $created++;
                if ($throttle > 0) {
                    sleep($throttle);
                }
            } catch (Throwable $e) {
                $this->newLine();
                $this->error($key.' => '.$e->getMessage());
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("İşlendi: {$created} anahtar. Panel > Önyüz Çevirileri bölümünden düzenleyebilirsiniz.");

        return self::SUCCESS;
    }
}
