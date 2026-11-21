<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use RuntimeException;

class UpazilaSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = database_path('seeders/data/all-upazila.json');

        if (!file_exists($filePath)) {
            throw new RuntimeException(
                'Upazila JSON dataset was not found.'
            );
        }

        $upazilas = json_decode(
            file_get_contents($filePath),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        // Correct district-name differences in the dataset.
        $districtAliases = [
            'Chapainababganj' => 'Chapainawabganj',
            'Netrakona' => 'Netrokona',
        ];

        $createdOrUpdated = 0;

        foreach ($upazilas as $item) {
            $districtName = $item['parent']['name']['en'] ?? null;
            $upazilaName = $item['name']['en'] ?? null;

            if (!$districtName || !$upazilaName) {
                throw new RuntimeException(
                    'An Upazila record has missing English names.'
                );
            }

            // Apply the district-name corrections.
            $districtName = $districtAliases[$districtName] ?? $districtName;

            $district = Location::where('type', 'district')
                ->where('name', $districtName)
                ->first();

            if (!$district) {
                throw new RuntimeException(
                    "District not found for Upazila: {$upazilaName} ({$districtName})"
                );
            }

            Location::updateOrCreate(
                [
                    'parent_id' => $district->id,
                    'slug' => Str::slug($upazilaName),
                ],
                [
                    'name' => $upazilaName,
                    'type' => 'thana',
                ]
            );

            $createdOrUpdated++;
        }

        $this->command->info(
            "Successfully processed {$createdOrUpdated} Upazila records."
        );
    }
}