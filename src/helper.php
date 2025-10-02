<?php

if (!function_exists('asset_url')) {
    function asset_url(string $url): string {
        $base = rtrim(config('filesystems.asset_url','/assets'), '/');
        return asset($base . '/' . ltrim($url, '/'));
    }
}

if (!function_exists('profile_photo')) {
    function profile_photo(string $photo): string {
        $base = rtrim(config('module-patient.filesystem.profile_photo'), '/');
        return $base . '/' . ltrim($photo, '/');
    }
}
