<?php

namespace App\Http\Middleware;

use App\Models\Inquiry;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => fn () => $request->user()?->only('id', 'name', 'email'),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'brand' => fn () => [
                'name' => Setting::get('site_name'),
                'tagline' => Setting::get('tagline'),
                'logo' => Setting::publicPayload()['logo'] ?? null,
            ],
            'unreadInquiries' => fn () => $request->user() ? Inquiry::unread()->count() : 0,
        ];
    }
}
