<?php

namespace Database\Seeders;

use App\Models\VehicleType;
use Illuminate\Database\Seeder;

class VehicleTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicleTypes = [
            [
                'name' => 'Motorcycle',
                'slug' => 'motorcycle',
                'icon' => 'motorcycle',
                'is_active' => true,
            ],
            [
                'name' => 'Car',
                'slug' => 'car',
                'icon' => 'car',
                'is_active' => true,
            ],
            [
                'name' => 'SUV',
                'slug' => 'suv',
                'icon' => 'car',
                'is_active' => true,
            ],
            [
                'name' => 'Microbus',
                'slug' => 'microbus',
                'icon' => 'car',
                'is_active' => true,
            ],
            [
                'name' => 'Truck',
                'slug' => 'truck',
                'icon' => 'truck',
                'is_active' => true,
            ],
        ];

        foreach ($vehicleTypes as $vehicleType) {
            VehicleType::updateOrCreate(
                ['slug' => $vehicleType['slug']],
                $vehicleType
            );
        }
    }
}