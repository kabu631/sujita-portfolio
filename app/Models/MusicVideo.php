<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MusicVideo extends Model
{
    protected $fillable = [
        'title', 'artist', 'role', 'director', 'year', 'video_url',
        'thumbnail', 'description', 'is_featured', 'is_published', 'sort_order',
    ];

    protected $appends = ['thumbnail_url', 'embed_url', 'provider'];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'year' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('id');
    }

    public static function youtubeId(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        $pattern = '~(?:youtube(?:-nocookie)?\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/|v/)|youtu\.be/)([A-Za-z0-9_-]{11})~i';

        return preg_match($pattern, $url, $m) ? $m[1] : null;
    }

    public static function vimeoId(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        return preg_match('~vimeo\.com/(?:video/)?(\d+)~i', $url, $m) ? $m[1] : null;
    }

    public static function embedFor(?string $url): ?string
    {
        if ($id = static::youtubeId($url)) {
            return "https://www.youtube-nocookie.com/embed/{$id}?autoplay=1&rel=0&modestbranding=1&playsinline=1";
        }

        if ($id = static::vimeoId($url)) {
            return "https://player.vimeo.com/video/{$id}?autoplay=1&title=0&byline=0&portrait=0";
        }

        return null;
    }

    public function getProviderAttribute(): ?string
    {
        return match (true) {
            (bool) static::youtubeId($this->video_url) => 'youtube',
            (bool) static::vimeoId($this->video_url) => 'vimeo',
            default => null,
        };
    }

    public function getEmbedUrlAttribute(): ?string
    {
        return static::embedFor($this->video_url);
    }

    /**
     * Uploaded cover wins; otherwise fall back to the YouTube poster frame.
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        if ($this->thumbnail) {
            return Media::url($this->thumbnail);
        }

        if ($id = static::youtubeId($this->video_url)) {
            return "https://i.ytimg.com/vi/{$id}/hqdefault.jpg";
        }

        return null;
    }
}
