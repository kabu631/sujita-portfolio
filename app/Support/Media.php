<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Small helper around the public disk so that every model resolves and
 * stores media the same way.
 */
class Media
{
    public const DISK = 'public';

    /**
     * Resolve a stored path (or an absolute URL) to a browser-ready URL.
     */
    public static function url(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '//'])) {
            return $path;
        }

        // asset() follows the current request host, so images work on any
        // port/domain without depending on APP_URL being exact.
        return asset('storage/'.ltrim($path, '/'));
    }

    public static function store(UploadedFile $file, string $folder): string
    {
        return $file->store($folder, self::DISK);
    }

    public static function delete(?string $path): void
    {
        if (filled($path) && ! Str::startsWith($path, ['http://', 'https://'])) {
            Storage::disk(self::DISK)->delete($path);
        }
    }

    /**
     * Replace an existing file with a new upload (deleting the old one).
     */
    public static function replace(?string $old, UploadedFile $file, string $folder): string
    {
        self::delete($old);

        return self::store($file, $folder);
    }
}
