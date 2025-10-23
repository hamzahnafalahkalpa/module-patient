<?php

use Illuminate\Support\Facades\Storage;

if (!function_exists('asset_url')) {
    /**
     * Generate asset URL, compatible with local/public and S3.
     */
    function asset_url(string $url): string
    {
        $disk = config('filesystems.default', 'public');
        $base = rtrim(config('filesystems.asset_url', '/assets'), '/');
        $path = ltrim($url, '/');

        // Kalau disk-nya S3, generate via Storage
        if ($disk === 's3') {
            return Storage::disk('s3')->url($path);
        }

        // Selain S3, tetap pakai asset() lokal
        return asset($base . '/' . $path);
    }
}

if (!function_exists('profile_photo')) {
    /**
     * Generate patient profile photo URL (support S3 or local).
     */
    function profile_photo(string $photo): string
    {
        $base = rtrim(config('module-patient.filesystem.profile_photo'), '/');
        return $base . '/' . ltrim($photo, '/');
    }
}
