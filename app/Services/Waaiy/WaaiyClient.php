<?php

namespace App\Services\Waaiy;

use Waaiy\API;

/**
 * Waaiy\API istemcisini uygulama yapılandırmasıyla kurar.
 *
 * Anahtarlar eksikse veya demo modu açıksa uygulama API'ye hiç gitmez; bu
 * durumda ContentRepository, DemoContent üzerinden örnek içerik sunar.
 */
class WaaiyClient
{
    protected ?API $api = null;

    /** Üç kimlik anahtarı da dolu mu? */
    public function configured(): bool
    {
        $keys = config('waaiy.keys', []);

        return ! empty($keys['public']) && ! empty($keys['private']) && ! empty($keys['user']);
    }

    /** API yerine örnek içerik mi kullanılmalı? */
    public function demo(): bool
    {
        $mode = strtolower((string) config('waaiy.mode', 'auto'));

        if ($mode === 'demo') {
            return true;
        }

        // 'auto' ve 'live': anahtarlar varsa canlı, yoksa güvenli demo.
        return ! $this->configured();
    }

    public function api(): API
    {
        if ($this->api === null) {
            $keys = config('waaiy.keys', []);
            $prefixes = config('waaiy.prefixes', []);

            $this->api = new API(config('waaiy.base_url'), [
                'public_key' => $keys['public'] ?? '',
                'private_key' => $keys['private'] ?? '',
                'user_key' => $keys['user'] ?? '',
                'timeout' => config('waaiy.timeout', 20),
                'connect_timeout' => config('waaiy.connect_timeout', 10),
                'page_prefix' => $prefixes['page'] ?? 'page',
                'blog_category_prefix' => $prefixes['blog_category'] ?? 'blogs',
                'product_category_prefix' => $prefixes['product_category'] ?? 'urunler',
            ]);
        }

        return $this->api;
    }

    public function mediaUrl(): string
    {
        $url = (string) config('waaiy.media_url');

        return $url !== '' ? rtrim($url, '/') : rtrim(config('app.url'), '/');
    }
}
