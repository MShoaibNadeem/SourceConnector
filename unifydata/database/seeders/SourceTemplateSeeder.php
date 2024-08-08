<?php

namespace Database\Seeders;

use App\Models\SourceTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SourceTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = database_path('seeders/data/sourca.json');

        try {
            // Fetch the data from the JSON file
            $sources = SourceTemplate::fetchDataFromJson($jsonPath);

            // Loop through each source and create the record
            foreach ($sources as $source) {


                // Get the image path
                $imagePath = base_path($source['image']);


                // Create the source with the Base64 encoded image
                try {
                    SourceTemplate::createSourceFromData(
                        $source['name'],
                        $source['type'],
                        $imagePath,
                        $source['fields']
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
