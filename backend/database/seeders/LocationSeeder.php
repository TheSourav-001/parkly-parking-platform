<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            'Dhaka' => [
                'Dhaka',
                'Faridpur',
                'Gazipur',
                'Gopalganj',
                'Kishoreganj',
                'Madaripur',
                'Manikganj',
                'Munshiganj',
                'Narayanganj',
                'Narsingdi',
                'Rajbari',
                'Shariatpur',
                'Tangail',
            ],

            'Chattogram' => [
                'Bandarban',
                'Brahmanbaria',
                'Chandpur',
                'Chattogram',
                "Cox's Bazar",
                'Cumilla',
                'Feni',
                'Khagrachhari',
                'Lakshmipur',
                'Noakhali',
                'Rangamati',
            ],

            'Rajshahi' => [
                'Bogura',
                'Chapainawabganj',
                'Joypurhat',
                'Naogaon',
                'Natore',
                'Pabna',
                'Rajshahi',
                'Sirajganj',
            ],

            'Khulna' => [
                'Bagerhat',
                'Chuadanga',
                'Jashore',
                'Jhenaidah',
                'Khulna',
                'Kushtia',
                'Magura',
                'Meherpur',
                'Narail',
                'Satkhira',
            ],

            'Barishal' => [
                'Barguna',
                'Barishal',
                'Bhola',
                'Jhalokati',
                'Patuakhali',
                'Pirojpur',
            ],

            'Sylhet' => [
                'Habiganj',
                'Moulvibazar',
                'Sunamganj',
                'Sylhet',
            ],

            'Rangpur' => [
                'Dinajpur',
                'Gaibandha',
                'Kurigram',
                'Lalmonirhat',
                'Nilphamari',
                'Panchagarh',
                'Rangpur',
                'Thakurgaon',
            ],

            'Mymensingh' => [
                'Jamalpur',
                'Mymensingh',
                'Netrokona',
                'Sherpur',
            ],
        ];

        foreach ($locations as $divisionName => $districts) {

            // Create or update the division.
            $division = Location::updateOrCreate(
                [
                    'parent_id' => null,
                    'slug' => Str::slug($divisionName),
                ],
                [
                    'name' => $divisionName,
                    'type' => 'division',
                ]
            );

            // Create or update districts under that division.
            foreach ($districts as $districtName) {
                Location::updateOrCreate(
                    [
                        'parent_id' => $division->id,
                        'slug' => Str::slug($districtName),
                    ],
                    [
                        'name' => $districtName,
                        'type' => 'district',
                    ]
                );
            }
        }
    }
}
