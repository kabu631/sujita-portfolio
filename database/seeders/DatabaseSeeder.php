<?php

namespace Database\Seeders;

use App\Models\GalleryItem;
use App\Models\MusicVideo;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the portfolio with Sujita's profile content and starter media.
     * Everything here can be edited or replaced from the admin panel.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@sujita.com'],
            ['name' => 'Sujita Khakural', 'password' => 'Sujita@2026'],
        );

        $media = $this->copyAssets();

        Setting::putMany([
            'hero_image' => $media['sujita-1.jpg'],
            'about_image' => $media['sujita-2.jpg'],
            'og_image' => $media['sujita-2.jpg'],
        ]);

        if (MusicVideo::count() === 0) {
            // Demo entries — replace titles/artists and paste real YouTube links in Admin → Music Videos.
            $videos = [
                ['title' => 'Saanjh',        'artist' => 'Featured Artist', 'role' => 'Lead Model',     'year' => 2026, 'thumbnail' => $media['cover-himalaya.jpg'], 'is_featured' => true,
                    'description' => 'A golden-hour love story filmed across the hills — soft, cinematic and full of longing.'],
                ['title' => 'Neon Nights',   'artist' => 'Featured Artist', 'role' => 'Lead Model',     'year' => 2026, 'thumbnail' => $media['cover-neon.jpg'],
                    'description' => 'High-energy performance piece under stage lights and haze.'],
                ['title' => 'City of Lights', 'artist' => 'Featured Artist', 'role' => 'Female Lead',   'year' => 2025, 'thumbnail' => $media['sujita-1.jpg'],
                    'description' => 'A dusk-to-midnight journey above the skyline.'],
                ['title' => 'Mann Ko Kura',  'artist' => 'Featured Artist', 'role' => 'Featured Model', 'year' => 2025, 'thumbnail' => $media['sujita-3.jpg'],
                    'description' => 'An intimate, elegant visual for a heartfelt ballad.'],
            ];

            foreach ($videos as $i => $video) {
                MusicVideo::create($video + ['sort_order' => $i + 1, 'is_published' => true]);
            }
        }

        if (GalleryItem::count() === 0) {
            $gallery = [
                ['image' => $media['sujita-2.jpg'],       'category' => 'portrait',    'caption' => 'Skyline, blue hour'],
                ['image' => $media['sujita-1.jpg'],       'category' => 'editorial',   'caption' => 'Above the city'],
                ['image' => $media['sujita-3.jpg'],       'category' => 'portrait',    'caption' => 'Satin & light'],
                ['image' => $media['cover-himalaya.jpg'], 'category' => 'music-video', 'caption' => 'On location — Saanjh'],
                ['image' => $media['cover-neon.jpg'],     'category' => 'bts',         'caption' => 'Stage set — Neon Nights'],
            ];

            foreach ($gallery as $i => $item) {
                GalleryItem::create($item + ['sort_order' => $i + 1]);
            }
        }

        if (Testimonial::count() === 0) {
            $testimonials = [
                ['name' => 'Music Video Director', 'role' => 'Sample testimonial — edit in admin',
                    'quote' => 'Sujita understands the camera instinctively. She brought real emotion to every take and made the whole crew\'s job easier.'],
                ['name' => 'Creative Producer', 'role' => 'Sample testimonial — edit in admin',
                    'quote' => 'Professional, punctual and endlessly elegant. She elevated the concept far beyond the storyboard.'],
                ['name' => 'Fashion Photographer', 'role' => 'Sample testimonial — edit in admin',
                    'quote' => 'Effortless poise and a natural sense of storytelling — every frame from our shoot was a keeper.'],
                ['name' => 'Fashion & Runway Coordinator', 'role' => 'Sample testimonial — edit in admin',
                    'quote' => 'Sujita brings unmatched poise and presence to both the runway and commercial campaigns. Her ability to embody diverse concepts makes every production an effortless success.'],
            ];

            foreach ($testimonials as $i => $t) {
                Testimonial::create($t + ['sort_order' => $i + 1]);
            }
        }
    }

    /**
     * Copy bundled starter images to the public disk. Returns filename => stored path.
     */
    private function copyAssets(): array
    {
        $paths = [];
        $disk = Storage::disk('public');

        foreach (glob(database_path('seeders/assets/*.jpg')) as $file) {
            $name = basename($file);
            $target = 'seed/'.$name;

            if (! $disk->exists($target)) {
                $disk->put($target, file_get_contents($file));
            }

            $paths[$name] = $target;
        }

        return $paths;
    }
}
