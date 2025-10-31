<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ImageHelper
{
    public static function handleUploadedImage($file, $path, $delete = null)
    {
        if ($file && $file->isValid()) {

            if ($delete) {
                Storage::disk('public')->delete($path . '/' . $delete);
            }

            $name = Str::random(4) . $file->getClientOriginalName();
            $file->storeAs($path, $name, 'public');
            return $name;
        }
        return null;
    }


    public static function uploadSummernoteImage($file, $path)
    {
        // Ensure the directory exists
        $fullPath = storage_path('app/public/' . $path);
        
        if (!file_exists($fullPath)) {
            mkdir($fullPath, 0777, true);
        }

        if ($file && $file->isValid()) {
            $name = 'IM_' . time() .  Str::random(8) . '.' . $file->getClientOriginalExtension();
            $file->storeAs($path, $name, 'public');

            return $name;
        }
        return null;
    }

    /**
     * Get the full URL for an uploaded image
     */
    public static function getImageUrl($path, $filename)
    {
        if (!$filename) {
            return null;
        }
        
        return asset('storage/' . $path . '/' . $filename);
    }

    /**
     * Get the full URL for a direct storage image (for backward compatibility)
     */
    public static function getStorageImageUrl($filename, $subfolder = 'images')
    {
        if (!$filename) {
            return null;
        }
        
        return asset('storage/' . $subfolder . '/' . $filename);
    }

    /**
     * Get image URL with fallback to placeholder
     */
    public static function getImageUrlWithFallback($filename, $subfolder = 'images', $placeholder = 'placeholder.png')
    {
        if (!$filename) {
            return asset('storage/' . $subfolder . '/' . $placeholder);
        }
        
        return asset('storage/' . $subfolder . '/' . $filename);
    }



    public static function ItemhandleUploadedImage($file, $path, $delete = null)
    {
        if ($file && $file->isValid()) {

            if ($delete) {
                Storage::disk('public')->delete($path . '/' . $delete);
            }

            $photoName = 'IM_' . time() .  Str::random(8) . '.' . $file->getClientOriginalExtension();
            $thumbnailName = 'IM_' . time() .  Str::random(8) . '.' . $file->getClientOriginalExtension();

            $file->storeAs($path, $photoName, 'public');

            $image = Image::make($file)->resize(230, 230);

            $thumbnailPath = $path . '/' . $thumbnailName;
            Storage::disk('public')->put($thumbnailPath, (string) $image->encode());

            return [$photoName, $thumbnailName];
        }
        return [null, null];
    }

    public static function handleUpdatedUploadedImage($file, $path, $data, $delete_path, $field)
    {
        if ($file && $file->isValid()) {
            $name = 'IM_' . time() .  Str::random(8) . '.' . $file->getClientOriginalExtension();

            $file->storeAs($path, $name, 'public');

            if ($data[$field] != null) {
                Storage::disk('public')->delete($delete_path . '/' . $data[$field]);
            }

            return $name;
        }
        return null;
    }


    public static function ItemhandleUpdatedUploadedImage($file, $path, $data, $delete_path, $field)
    {
        if ($file && $file->isValid()) {
            $photoName = 'IM_' . time() .  Str::random(8) . '.' . $file->getClientOriginalExtension();
            $thumbnailName = 'IM_' . time() . Str::random(8) . '.' . $file->getClientOriginalExtension();

            $image = Image::make($file)->resize(230, 230);

            $thumbnailPath = $path . '/' . $thumbnailName;
            Storage::disk('public')->put($thumbnailPath, (string) $image->encode());

            $photoPath = $path . '/' . $photoName;
            $file->storeAs($path, $photoName, 'public');

            if (!empty($data['thumbnail'])) {
                Storage::disk('public')->delete($delete_path . '/' . $data['thumbnail']);
            }

            if (!empty($data[$field])) {
                Storage::disk('public')->delete($delete_path . '/' . $data[$field]);
            }

            return [$photoName, $thumbnailName];
        }
        return [null, null];
    }


    public static function handleDeletedImage($data, $field, $delete_path)
    {
        if (!empty($data[$field])) {
            Storage::disk('public')->delete($delete_path . '/' . $data[$field]);
        }
    }
}
