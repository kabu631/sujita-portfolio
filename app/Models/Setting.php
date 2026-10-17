<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    public const CACHE_KEY = 'portfolio.settings';

    protected $fillable = ['key', 'value'];

    /**
     * Flattened schema: key => field definition (with group key attached).
     */
    public static function schema(): array
    {
        $fields = [];

        foreach (config('portfolio.settings', []) as $group => $definition) {
            foreach ($definition['fields'] as $key => $field) {
                $fields[$key] = $field + ['group' => $group, 'type' => 'text'];
            }
        }

        return $fields;
    }

    /**
     * All settings (defaults merged with stored values), cached forever and
     * flushed whenever settings are saved.
     */
    public static function allCached(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            $defaults = array_map(fn ($f) => $f['default'] ?? null, static::schema());

            try {
                $stored = static::query()->pluck('value', 'key')->all();
            } catch (\Throwable) {
                $stored = []; // table not migrated yet
            }

            return array_merge($defaults, $stored);
        });
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return static::allCached()[$key] ?? $default;
    }

    public static function putMany(array $values): void
    {
        foreach ($values as $key => $value) {
            static::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Settings ready for the front-end: image paths resolved to URLs.
     */
    public static function publicPayload(): array
    {
        $settings = static::allCached();

        foreach (static::schema() as $key => $field) {
            if ($field['type'] === 'image') {
                $settings[$key] = Media::url($settings[$key] ?? null);
            }
        }

        return $settings;
    }
}
