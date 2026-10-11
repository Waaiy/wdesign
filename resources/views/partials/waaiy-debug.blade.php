{{-- Canlı API çağrısı başarısız olduğunda geliştiriciyi uyarır (yalnızca debug). --}}
@if (config('app.debug') && isset($waaiy) && $waaiy->fellBack())
    <div class="waaiy-debug" role="status">
        <strong>Waaiy API uyarısı:</strong>
        <span>{{ $waaiy->lastError() ?: 'Bilinmeyen hata' }}</span>
        <em>Güvenli varsayılan içerik gösteriliyor. Kontrol: <code>php artisan waaiy:check</code></em>
    </div>
@endif
