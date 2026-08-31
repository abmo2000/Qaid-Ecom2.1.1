<?php

use App\Models\BuisnessSetting;
use App\Models\City;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

if (!function_exists('getBuisnessSettings')) {
    function getBuisnessSettings($key = null, $default = null)
    {
        
         $settings = BuisnessSetting::query()->where('key', $key)->first();

        if (! $settings) {
            return null;
        }

        $value = $settings->value;

        if (is_array($value)) {
            return (object) $value;
        }

        if (is_string($value)) {
            $decodedValue = json_decode($value, true);
            if (is_array($decodedValue)) {
                return (object) $decodedValue;
            }
        }

        return $default;
    
    }

}

if(!function_exists('getCities')){
  
    function getCities(){
       return City::query()
            ->orderByDesc('id')
            ->get()
            ->map(function ($city) {
                return [
                    'id' => $city->id,
                    'value' => $city?->name,
                    'price' => $city->price,
                    'has_discussion_for_delivery' => $city->has_discussion_for_delivery,
                ];
            })
            ->unique('value')
            ->values();
    }

}

if (!function_exists('storage_image_url')) {
    function storage_image_url($path, $disk = 'local'): ?string
    {
        if (empty($path)) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        $relativePath = ltrim((string) $path, '/');
        $relativePath = preg_replace('#^storage/#', '', $relativePath) ?? $relativePath;

        if ($relativePath === '') {
            return null;
        }

        $publicDisk = Storage::disk('public');
        if ($publicDisk->exists($relativePath)) {
            return '/' . ltrim('storage/' . $relativePath, '/');
        }

        $sourceDisk = Storage::disk($disk);
        if ($sourceDisk->exists($relativePath)) {
            try {
                $contents = $sourceDisk->get($relativePath);
                $publicDisk->put($relativePath, $contents);

                if ($publicDisk->exists($relativePath)) {
                    return '/' . ltrim('storage/' . $relativePath, '/');
                }
            } catch (\Throwable $e) {
                // Fall back to the public asset path below.
            }
        }

        return '/' . ltrim('storage/' . $relativePath, '/');
    }
}



