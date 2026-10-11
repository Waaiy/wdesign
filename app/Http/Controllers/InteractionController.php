<?php

namespace App\Http\Controllers;

use App\Services\Waaiy\ContentRepository;
use Illuminate\Http\Request;
use Throwable;

/**
 * Bülten, form ve yorum uçları.
 *
 * Demo modunda Waaiy API'sine gidilmez; kullanıcıya örnek başarı mesajı döner.
 */
class InteractionController extends Controller
{
    public function subscribe(Request $request, ContentRepository $waaiy)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:180'],
            'name' => ['nullable', 'string', 'max:120'],
        ]);

        try {
            $result = $waaiy->subscribe($data['email'], $data['name'] ?? null);

            return back()->with('status', $result->message ?: 'Abone olundu.');
        } catch (Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function formSubmit(Request $request, ContentRepository $waaiy)
    {
        $slug = (string) $request->input('form_slug', 'iletisim');
        $data = $request->except(['_token', 'form_slug']);

        $validated = $request->validate([
            'ad_soyad' => ['nullable', 'string', 'max:160'],
            'eposta' => ['nullable', 'email', 'max:180'],
            'mesaj' => ['nullable', 'string', 'max:5000'],
        ]);

        try {
            $message = $waaiy->submitForm($slug, array_merge($data, $validated));

            return back()->with('status', $message ?: 'Mesajınız gönderildi.');
        } catch (Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function comment(Request $request, ContentRepository $waaiy)
    {
        $data = $request->validate([
            'type' => ['required', 'in:blog,sayfa,urun'],
            'id' => ['required', 'integer', 'min:1'],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180'],
            'message' => ['required', 'string', 'max:3000'],
            'parent_id' => ['nullable', 'integer', 'min:0'],
        ]);

        try {
            $message = $waaiy->addComment($data['type'], (int) $data['id'], $data);

            return back()->with('status', $message ?: 'Yorumunuz alındı.');
        } catch (Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
