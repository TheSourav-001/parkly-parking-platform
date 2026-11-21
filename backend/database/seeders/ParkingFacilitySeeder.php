<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ParkingFacilitySeeder extends Seeder
{
    /**
     * Seed vehicle types, locations, parking facilities, rates, and availability.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // ── Vehicle Types ──────────────────────────────────────────────
        $vehicleTypes = [
            ['name' => 'Car/Sedan', 'slug' => 'car', 'icon' => '🚗', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'SUV/4x4',   'slug' => 'suv', 'icon' => '🚙', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Motorbike', 'slug' => 'bike', 'icon' => '🏍️', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'EV',        'slug' => 'ev',   'icon' => '⚡', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('vehicle_types')->insert($vehicleTypes);

        $vtCar  = DB::table('vehicle_types')->where('slug', 'car')->value('id');
        $vtSuv  = DB::table('vehicle_types')->where('slug', 'suv')->value('id');
        $vtBike = DB::table('vehicle_types')->where('slug', 'bike')->value('id');
        $vtEv   = DB::table('vehicle_types')->where('slug', 'ev')->value('id');

        // ── Locations (Dhaka areas) ────────────────────────────────────
        $dhakaId = DB::table('locations')->insertGetId([
            'parent_id'  => null,
            'name'       => 'Dhaka',
            'type'       => 'city',
            'slug'       => 'dhaka',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $areas = [
            'Gulshan-1', 'Gulshan-2', 'Banani', 'Dhanmondi', 'Mirpur DOHS',
            'Motijheel', 'Uttara', 'Bashundhara', 'Farmgate', 'Mohakhali',
            'Panthapath', 'Shahbag', 'Kawran Bazar', 'Tejgaon', 'Nikunja',
        ];

        $locationIds = [];
        foreach ($areas as $area) {
            $locationIds[$area] = DB::table('locations')->insertGetId([
                'parent_id'  => $dhakaId,
                'name'       => $area,
                'type'       => 'area',
                'slug'       => Str::slug($area),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // ── Parking Facilities ─────────────────────────────────────────
        $facilities = [
            [
                'area'        => 'Gulshan-1',
                'name'        => 'Gulshan Tower Automated Garage',
                'address'     => 'Plot 12, Road 103, Gulshan-1, Dhaka 1212',
                'lat'         => 23.7808000,
                'lng'         => 90.4178000,
                'description' => 'Multi-level automated parking garage in the heart of Gulshan-1 commercial area.',
                'types'       => [$vtCar, $vtSuv, $vtBike, $vtEv],
            ],
            [
                'area'        => 'Gulshan-2',
                'name'        => 'Gulshan Circle Premium Parking',
                'address'     => 'Gulshan-2 Circle, Gulshan Avenue, Dhaka 1212',
                'lat'         => 23.7935000,
                'lng'         => 90.4145000,
                'description' => 'Premium open-air and covered parking near Gulshan-2 Circle with 24/7 security.',
                'types'       => [$vtCar, $vtSuv, $vtEv],
            ],
            [
                'area'        => 'Banani',
                'name'        => 'Banani Central Smart Park',
                'address'     => 'Road 11, Block E, Banani, Dhaka 1213',
                'lat'         => 23.7937000,
                'lng'         => 90.4066000,
                'description' => 'Smart parking facility with app-based slot reservation in Banani commercial zone.',
                'types'       => [$vtCar, $vtBike, $vtEv],
            ],
            [
                'area'        => 'Dhanmondi',
                'name'        => 'Dhanmondi Square Parking Complex',
                'address'     => 'Road 27, Dhanmondi, Dhaka 1205',
                'lat'         => 23.7466000,
                'lng'         => 90.3738000,
                'description' => 'Multi-storey parking complex adjacent to Dhanmondi Lake with retail shops on ground floor.',
                'types'       => [$vtCar, $vtSuv, $vtBike],
            ],
            [
                'area'        => 'Mirpur DOHS',
                'name'        => 'DOHS Mirpur Secure Parking',
                'address'     => 'Avenue 3, Mirpur DOHS, Dhaka 1216',
                'lat'         => 23.8086000,
                'lng'         => 90.3675000,
                'description' => 'Secured gated parking inside Mirpur DOHS with CCTV surveillance.',
                'types'       => [$vtCar, $vtSuv],
            ],
            [
                'area'        => 'Motijheel',
                'name'        => 'Motijheel Business District Parking',
                'address'     => 'DIT Extension Road, Motijheel, Dhaka 1000',
                'lat'         => 23.7272000,
                'lng'         => 90.4195000,
                'description' => 'Underground parking serving Motijheel commercial and banking district.',
                'types'       => [$vtCar, $vtSuv, $vtBike, $vtEv],
            ],
            [
                'area'        => 'Uttara',
                'name'        => 'Uttara Sector 7 Park & Ride',
                'address'     => 'Sector 7, Uttara, Dhaka 1230',
                'lat'         => 23.8750000,
                'lng'         => 90.3918000,
                'description' => 'Park & ride facility near Uttara metro station with affordable rates.',
                'types'       => [$vtCar, $vtBike, $vtEv],
            ],
            [
                'area'        => 'Bashundhara',
                'name'        => 'Bashundhara City Parking Tower',
                'address'     => 'Block A, Bashundhara R/A, Dhaka 1229',
                'lat'         => 23.8139000,
                'lng'         => 90.4256000,
                'description' => 'Dedicated parking tower adjacent to Bashundhara City shopping mall.',
                'types'       => [$vtCar, $vtSuv, $vtBike],
            ],
            [
                'area'        => 'Farmgate',
                'name'        => 'Farmgate Flyover Parking Hub',
                'address'     => 'Kazi Nazrul Islam Avenue, Farmgate, Dhaka 1215',
                'lat'         => 23.7568000,
                'lng'         => 90.3904000,
                'description' => 'Covered parking hub under the Farmgate flyover, ideal for commuters.',
                'types'       => [$vtCar, $vtBike],
            ],
            [
                'area'        => 'Mohakhali',
                'name'        => 'Mohakhali DOHS Gate Parking',
                'address'     => 'Mohakhali DOHS Gate, Dhaka 1206',
                'lat'         => 23.7778000,
                'lng'         => 90.4039000,
                'description' => 'Open-air parking lot at the entrance of Mohakhali DOHS with attendant service.',
                'types'       => [$vtCar, $vtSuv, $vtBike, $vtEv],
            ],
            [
                'area'        => 'Panthapath',
                'name'        => 'Panthapath Signal Parking Plaza',
                'address'     => 'Green Road Intersection, Panthapath, Dhaka 1205',
                'lat'         => 23.7515000,
                'lng'         => 90.3867000,
                'description' => 'Multi-level parking plaza near Panthapath signal, close to shopping and dining.',
                'types'       => [$vtCar, $vtSuv, $vtBike],
            ],
            [
                'area'        => 'Shahbag',
                'name'        => 'Shahbag National Museum Parking',
                'address'     => 'Shahbag Square, Dhaka 1000',
                'lat'         => 23.7378000,
                'lng'         => 90.3953000,
                'description' => 'Surface parking lot serving Shahbag area, near the National Museum and university.',
                'types'       => [$vtCar, $vtBike],
            ],
            [
                'area'        => 'Kawran Bazar',
                'name'        => 'Kawran Bazar Trade Center Parking',
                'address'     => 'Kawran Bazar, Dhaka 1215',
                'lat'         => 23.7507000,
                'lng'         => 90.3933000,
                'description' => 'Basement parking under the trade center, popular with office workers.',
                'types'       => [$vtCar, $vtSuv, $vtBike, $vtEv],
            ],
            [
                'area'        => 'Tejgaon',
                'name'        => 'Tejgaon Industrial Zone Parking',
                'address'     => 'Tejgaon I/A, Dhaka 1208',
                'lat'         => 23.7645000,
                'lng'         => 90.3932000,
                'description' => 'Large open-lot parking for heavy and light vehicles in the Tejgaon industrial area.',
                'types'       => [$vtCar, $vtSuv, $vtBike],
            ],
            [
                'area'        => 'Nikunja',
                'name'        => 'Nikunja Airport Road Parking',
                'address'     => 'Nikunja-2, Airport Road, Dhaka 1229',
                'lat'         => 23.8292000,
                'lng'         => 90.4188000,
                'description' => 'Long-term and short-term parking near the airport road, with shuttle service.',
                'types'       => [$vtCar, $vtSuv, $vtEv],
            ],
        ];

        // Rate templates per vehicle type
        $rateTemplates = [
            $vtCar => [
                ['rate_amount' => 60.00,  'rate_unit' => 'hourly', 'min_charge_amount' => 60.00],
                ['rate_amount' => 500.00, 'rate_unit' => 'daily',  'min_charge_amount' => 500.00],
            ],
            $vtSuv => [
                ['rate_amount' => 80.00,  'rate_unit' => 'hourly', 'min_charge_amount' => 80.00],
                ['rate_amount' => 650.00, 'rate_unit' => 'daily',  'min_charge_amount' => 650.00],
            ],
            $vtBike => [
                ['rate_amount' => 20.00,  'rate_unit' => 'hourly', 'min_charge_amount' => 20.00],
            ],
            $vtEv => [
                ['rate_amount' => 100.00, 'rate_unit' => 'hourly', 'min_charge_amount' => 100.00],
                ['rate_amount' => 800.00, 'rate_unit' => 'daily',  'min_charge_amount' => 800.00],
            ],
        ];

        // Slot templates per vehicle type
        $slotTemplates = [
            $vtCar  => ['total' => [40, 60, 80, 100], 'fill' => [0.6, 0.75, 0.85]],
            $vtSuv  => ['total' => [15, 20, 30],      'fill' => [0.5, 0.65, 0.8]],
            $vtBike => ['total' => [50, 80, 100, 120], 'fill' => [0.4, 0.55, 0.7]],
            $vtEv   => ['total' => [5, 10, 15],        'fill' => [0.3, 0.5, 0.7]],
        ];

        foreach ($facilities as $index => $f) {
            $facilityId = DB::table('parking_facilities')->insertGetId([
                'location_id'  => $locationIds[$f['area']],
                'name'         => $f['name'],
                'slug'         => Str::slug($f['name']),
                'address_text' => $f['address'],
                'latitude'     => $f['lat'],
                'longitude'    => $f['lng'],
                'description'  => $f['description'],
                'is_active'    => true,
                'created_by'   => null,
                'updated_by'   => null,
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);

            // Insert rates and availability for each supported vehicle type
            foreach ($f['types'] as $vtId) {
                // Rates
                $rates = $rateTemplates[$vtId] ?? [];
                // Add slight variation per facility so rates aren't identical
                $variation = 1 + (($index % 5) - 2) * 0.05; // -10% to +10%

                foreach ($rates as $rate) {
                    DB::table('facility_vehicle_rates')->insert([
                        'parking_facility_id' => $facilityId,
                        'vehicle_type_id'     => $vtId,
                        'rate_amount'         => round($rate['rate_amount'] * $variation, 2),
                        'rate_unit'           => $rate['rate_unit'],
                        'min_charge_amount'   => round($rate['min_charge_amount'] * $variation, 2),
                        'currency'            => 'BDT',
                        'is_active'           => true,
                        'created_at'          => $now,
                        'updated_at'          => $now,
                    ]);
                }

                // Availability
                $template = $slotTemplates[$vtId];
                $totalSlots = $template['total'][$index % count($template['total'])];
                $fillRate = $template['fill'][$index % count($template['fill'])];
                $availableSlots = (int) round($totalSlots * (1 - $fillRate));

                DB::table('facility_availabilities')->insert([
                    'parking_facility_id' => $facilityId,
                    'vehicle_type_id'     => $vtId,
                    'total_slots'         => $totalSlots,
                    'available_slots'     => $availableSlots,
                    'last_updated_by'     => null,
                    'last_updated_at'     => $now,
                    'created_at'          => $now,
                    'updated_at'          => $now,
                ]);
            }
        }
    }
}
