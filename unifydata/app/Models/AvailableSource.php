<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Support\Facades\File;

class AvailableSource extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'image',
    ];

    public static function fetchDataFromJson($jsonPath)
    {
        if (!File::exists($jsonPath)) {
            throw new \Exception("JSON file not found: {$jsonPath}");
        }

        $jsonData = File::get($jsonPath);

        $sources = json_decode($jsonData, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Error decoding JSON: ' . json_last_error_msg());
        }

        return $sources;
    }

    public static function encodeImage($imagePath)
    {
        if (!file_exists($imagePath)) {
            throw new \Exception("Image file not found: {$imagePath}");
        }

        $imageData = file_get_contents($imagePath);
        return base64_encode($imageData);
    }

    public static function createSourceFromData($name, $type, $imagePath)
    {
        // Encode the image file to Base64
        $base64Image = self::encodeImage($imagePath);

        // Create the source record
        return self::create([
            'name' => $name,
            'type' => $type,
            'image' => $base64Image,
        ]);
    }
}
