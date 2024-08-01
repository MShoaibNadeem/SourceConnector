<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AvailableSource;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Path to the JSON file
        $jsonPath = database_path('seeders/data/sources.json');

        try {
            // Fetch the data from the JSON file
            $sources = AvailableSource::fetchDataFromJson($jsonPath);

            // Loop through each source and create the record
            foreach ($sources as $source) {
                // Get the image path
                $imagePath = base_path($source['image_path']);

                // Create the source with the Base64 encoded image
                try {
                    AvailableSource::createSourceFromData(
                        $source['name'],
                        $source['type'],
                        $imagePath
                    );
                } catch (\Exception $e) {
                    $this->command->warn("Failed to process image file: {$imagePath}. Error: " . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            $this->command->error($e->getMessage());
        }
    }
}
