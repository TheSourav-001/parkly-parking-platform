<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DhakaLocationsSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // 1. Division: Dhaka
        $divisionId = DB::table('locations')->insertGetId([
            'parent_id'  => null,
            'name'       => 'Dhaka Division',
            'name_en'    => 'Dhaka Division',
            'name_bn'    => 'ঢাকা বিভাগ',
            'type'       => 'division',
            'slug'       => 'dhaka-division',
            'latitude'   => 23.8103,
            'longitude'  => 90.4125,
            'is_active'  => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Optional other division for coming soon demonstration
        DB::table('locations')->insert([
            ['parent_id' => null, 'name' => 'Chittagong Division', 'name_en' => 'Chittagong Division', 'name_bn' => 'চট্টগ্রাম বিভাগ', 'type' => 'division', 'slug' => 'chittagong-division', 'latitude' => 22.3569, 'longitude' => 91.7832, 'is_active' => false, 'created_at' => $now, 'updated_at' => $now],
            ['parent_id' => null, 'name' => 'Sylhet Division', 'name_en' => 'Sylhet Division', 'name_bn' => 'সিলেট বিভাগ', 'type' => 'division', 'slug' => 'sylhet-division', 'latitude' => 24.8949, 'longitude' => 91.8687, 'is_active' => false, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 2. District: Dhaka
        $districtId = DB::table('locations')->insertGetId([
            'parent_id'  => $divisionId,
            'name'       => 'Dhaka District',
            'name_en'    => 'Dhaka District',
            'name_bn'    => 'ঢাকা জেলা',
            'type'       => 'district',
            'slug'       => 'dhaka-district',
            'latitude'   => 23.8103,
            'longitude'  => 90.4125,
            'is_active'  => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 3. Thanas in Dhaka
        $thanas = [
            ['name' => 'Gulshan', 'name_en' => 'Gulshan', 'name_bn' => 'গুলশান', 'slug' => 'gulshan', 'lat' => 23.7925, 'lng' => 90.4078],
            ['name' => 'Banani', 'name_en' => 'Banani', 'name_bn' => 'বনানী', 'slug' => 'banani', 'lat' => 23.7937, 'lng' => 90.4066],
            ['name' => 'Dhanmondi', 'name_en' => 'Dhanmondi', 'name_bn' => 'ধানমন্ডি', 'slug' => 'dhanmondi', 'lat' => 23.7461, 'lng' => 90.3742],
            ['name' => 'Mirpur', 'name_en' => 'Mirpur', 'name_bn' => 'মিরপুর', 'slug' => 'mirpur', 'lat' => 23.8069, 'lng' => 90.3687],
            ['name' => 'Uttara', 'name_en' => 'Uttara', 'name_bn' => 'উত্তরা', 'slug' => 'uttara', 'lat' => 23.8759, 'lng' => 90.3795],
            ['name' => 'Motijheel', 'name_en' => 'Motijheel', 'name_bn' => 'মতিঝিল', 'slug' => 'motijheel', 'lat' => 23.7330, 'lng' => 90.4172],
            ['name' => 'Tejgaon', 'name_en' => 'Tejgaon', 'name_bn' => 'তেজগাঁও', 'slug' => 'tejgaon', 'lat' => 23.7598, 'lng' => 90.3912],
            ['name' => 'Mohammadpur', 'name_en' => 'Mohammadpur', 'name_bn' => 'মোহাম্মদপুর', 'slug' => 'mohammadpur', 'lat' => 23.7658, 'lng' => 90.3584],
            ['name' => 'Badda', 'name_en' => 'Badda', 'name_bn' => 'বাড্ডা', 'slug' => 'badda', 'lat' => 23.7805, 'lng' => 90.4267],
            ['name' => 'Khilgaon', 'name_en' => 'Khilgaon', 'name_bn' => 'খিলগাঁও', 'slug' => 'khilgaon', 'lat' => 23.7516, 'lng' => 90.4223],
        ];

        $thanaIds = [];
        foreach ($thanas as $t) {
            $thanaIds[$t['slug']] = DB::table('locations')->insertGetId([
                'parent_id'  => $districtId,
                'name'       => $t['name'],
                'name_en'    => $t['name_en'],
                'name_bn'    => $t['name_bn'],
                'type'       => 'thana',
                'slug'       => $t['slug'],
                'latitude'   => $t['lat'],
                'longitude'  => $t['lng'],
                'is_active'  => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 4. Areas under Thanas
        $areas = [
            // Gulshan
            ['thana' => 'gulshan', 'name' => 'Gulshan 1', 'name_en' => 'Gulshan 1', 'name_bn' => 'গুলশান ১', 'slug' => 'gulshan-1', 'lat' => 23.7808, 'lng' => 90.4178],
            ['thana' => 'gulshan', 'name' => 'Gulshan 2', 'name_en' => 'Gulshan 2', 'name_bn' => 'গুলশান ২', 'slug' => 'gulshan-2', 'lat' => 23.7935, 'lng' => 90.4145],
            ['thana' => 'gulshan', 'name' => 'Gulshan South Avenue', 'name_en' => 'Gulshan South Avenue', 'name_bn' => 'গুলশান সাউথ এভিনিউ', 'slug' => 'gulshan-south-ave', 'lat' => 23.7780, 'lng' => 90.4160],

            // Banani
            ['thana' => 'banani', 'name' => 'Banani Road 11', 'name_en' => 'Banani Road 11', 'name_bn' => 'বনানী ১১ নম্বর রোড', 'slug' => 'banani-road-11', 'lat' => 23.7937, 'lng' => 90.4066],
            ['thana' => 'banani', 'name' => 'Kemal Ataturk Avenue', 'name_en' => 'Kemal Ataturk Avenue', 'name_bn' => 'কামাল আতাতুর্ক এভিনিউ', 'slug' => 'kemal-ataturk', 'lat' => 23.7940, 'lng' => 90.4040],

            // Dhanmondi
            ['thana' => 'dhanmondi', 'name' => 'Dhanmondi 27 (Satmasjid Road)', 'name_en' => 'Dhanmondi 27', 'name_bn' => 'ধানমন্ডি ২৭', 'slug' => 'dhanmondi-27', 'lat' => 23.7466, 'lng' => 90.3738],
            ['thana' => 'dhanmondi', 'name' => 'Dhanmondi Shimanto Square (Road 2)', 'name_en' => 'Dhanmondi Shimanto Square', 'name_bn' => 'ধানমন্ডি সীমান্ত স্কয়ার', 'slug' => 'dhanmondi-shimanto', 'lat' => 23.7364, 'lng' => 90.3775],

            // Mirpur
            ['thana' => 'mirpur', 'name' => 'Mirpur 10 Circle', 'name_en' => 'Mirpur 10', 'name_bn' => 'মিরপুর ১০', 'slug' => 'mirpur-10', 'lat' => 23.8069, 'lng' => 90.3687],
            ['thana' => 'mirpur', 'name' => 'Mirpur DOHS', 'name_en' => 'Mirpur DOHS', 'name_bn' => 'মিরপুর ডিওএইচএস', 'slug' => 'mirpur-dohs', 'lat' => 23.8086, 'lng' => 90.3675],
            ['thana' => 'mirpur', 'name' => 'Mirpur 2 (Stadium Area)', 'name_en' => 'Mirpur 2', 'name_bn' => 'মিরপুর ২', 'slug' => 'mirpur-2', 'lat' => 23.8020, 'lng' => 90.3615],

            // Uttara
            ['thana' => 'uttara', 'name' => 'Uttara Sector 3', 'name_en' => 'Uttara Sector 3', 'name_bn' => 'উত্তরা সেক্টর ৩', 'slug' => 'uttara-sec-3', 'lat' => 23.8640, 'lng' => 90.3989],
            ['thana' => 'uttara', 'name' => 'Uttara Sector 7', 'name_en' => 'Uttara Sector 7', 'name_bn' => 'উত্তরা সেক্টর ৭', 'slug' => 'uttara-sec-7', 'lat' => 23.8720, 'lng' => 90.3980],

            // Motijheel
            ['thana' => 'motijheel', 'name' => 'Motijheel Commercial Area', 'name_en' => 'Motijheel C/A', 'name_bn' => 'মতিঝিল বাণিজ্যিক এলাকা', 'slug' => 'motijheel-ca', 'lat' => 23.7294, 'lng' => 90.4175],
            ['thana' => 'motijheel', 'name' => 'Dilkusha Commercial Area', 'name_en' => 'Dilkusha C/A', 'name_bn' => 'দিলকুশা বাণিজ্যিক এলাকা', 'slug' => 'dilkusha-ca', 'lat' => 23.7272, 'lng' => 90.4195],

            // Tejgaon
            ['thana' => 'tejgaon', 'name' => 'Tejgaon Commercial Hub (Panthapath/Bashundhara)', 'name_en' => 'Panthapath', 'name_bn' => 'পান্থপথ', 'slug' => 'panthapath', 'lat' => 23.7513, 'lng' => 90.3906],
            ['thana' => 'tejgaon', 'name' => 'Kawran Bazar TCB Bhaban Strip', 'name_en' => 'Kawran Bazar', 'name_bn' => 'কাওরান বাজার', 'slug' => 'kawran-bazar', 'lat' => 23.7507, 'lng' => 90.3933],
            ['thana' => 'tejgaon', 'name' => 'Farmgate Transit Hub', 'name_en' => 'Farmgate', 'name_bn' => 'ফার্মগেট', 'slug' => 'farmgate', 'lat' => 23.7568, 'lng' => 90.3904],

            // Badda
            ['thana' => 'badda', 'name' => 'Pragati Sarani (Kuril/Jamuna)', 'name_en' => 'Kuril Pragati Sarani', 'name_bn' => 'কুড়িল প্রগতি সরণি', 'slug' => 'kuril-pragati-sarani', 'lat' => 23.8111, 'lng' => 90.4247],

            // Mohammadpur
            ['thana' => 'mohammadpur', 'name' => 'Mohammadpur Town Hall Area', 'name_en' => 'Mohammadpur Town Hall', 'name_bn' => 'মোহাম্মদপুর টাউন হল', 'slug' => 'mohammadpur-town-hall', 'lat' => 23.7658, 'lng' => 90.3584],

            // Khilgaon
            ['thana' => 'khilgaon', 'name' => 'Taltola Market Strip', 'name_en' => 'Khilgaon Taltola', 'name_bn' => 'খিলগাঁও তালতলা', 'slug' => 'khilgaon-taltola', 'lat' => 23.7516, 'lng' => 90.4223],
        ];

        foreach ($areas as $a) {
            DB::table('locations')->insert([
                'parent_id'  => $thanaIds[$a['thana']],
                'name'       => $a['name'],
                'name_en'    => $a['name_en'],
                'name_bn'    => $a['name_bn'],
                'type'       => 'area',
                'slug'       => $a['slug'],
                'latitude'   => $a['lat'],
                'longitude'  => $a['lng'],
                'is_active'  => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}

