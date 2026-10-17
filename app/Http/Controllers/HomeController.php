<?php

namespace App\Http\Controllers;

use App\Models\GalleryItem;
use App\Models\MusicVideo;
use App\Models\Setting;
use App\Models\Testimonial;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(): Response
    {
        $settings = Setting::publicPayload();
        $settings['showreel_embed'] = MusicVideo::embedFor($settings['showreel_url'] ?? null);

        $categories = config('portfolio.gallery_categories');

        return Inertia::render('Home', [
            'settings' => $settings,
            'videos' => MusicVideo::published()->ordered()->get()
                ->map->only('id', 'title', 'artist', 'role', 'director', 'year', 'description', 'is_featured', 'thumbnail_url', 'embed_url', 'provider', 'video_url'),
            'gallery' => GalleryItem::published()->ordered()->get()
                ->map(fn (GalleryItem $g) => [
                    'id' => $g->id,
                    'src' => $g->image_url,
                    'caption' => $g->caption,
                    'category' => $g->category,
                    'label' => $categories[$g->category] ?? ucfirst($g->category),
                ]),
            'testimonials' => Testimonial::published()->ordered()->get(['id', 'name', 'role', 'quote']),
            'categories' => $categories,
            'projectTypes' => config('portfolio.project_types'),
        ]);
    }
}
