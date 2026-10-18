<?php

namespace Tests\Feature;

use App\Models\AdminNotification;
use App\Models\AuditLog;
use App\Models\AvailabilityUpdate;
use App\Models\Booking;
use App\Models\FacilityAvailability;
use App\Models\FacilityVehicleRate;
use App\Models\Invoice;
use App\Models\Location;
use App\Models\ParkingFacility;
use App\Models\Payment;
use App\Models\User;
use App\Models\VehicleType;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DatabaseLayerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_location_hierarchy_and_composite_unique_constraint(): void
    {
        $division = Location::create([
            'parent_id' => null,
            'name'      => 'Dhaka Division',
            'type'      => 'division',
            'slug'      => 'dhaka-division',
        ]);

        $district = Location::create([
            'parent_id' => $division->id,
            'name'      => 'Dhaka District',
            'type'      => 'district',
            'slug'      => 'dhaka-district',
        ]);

        $this->assertEquals($division->id, $district->parent->id);
        $this->assertTrue($division->children->contains($district));

        // Test composite unique constraint: duplicate slug under the same parent must fail
        $this->expectException(QueryException::class);
        Location::create([
            'parent_id' => $division->id,
            'name'      => 'Duplicate District',
            'type'      => 'district',
            'slug'      => 'dhaka-district',
        ]);
    }

    public function test_parking_facility_and_relations(): void
    {
        $admin = User::create([
            'name'      => 'Test Admin',
            'email'     => 'testadmin_' . uniqid() . '@example.com',
            'phone'     => '01700000000',
            'password'  => bcrypt('secret'),
            'role'      => 'admin',
            'is_active' => true,
        ]);

        $division = Location::create([
            'name' => 'Chittagong',
            'type' => 'division',
            'slug' => 'ctg-' . uniqid(),
        ]);

        $facility = ParkingFacility::create([
            'location_id'  => $division->id,
            'name'         => 'Agrabad Safe Park',
            'slug'         => 'agrabad-park-' . uniqid(),
            'address_text' => 'Agrabad Commercial Area, Chittagong',
            'latitude'     => 22.3245000,
            'longitude'    => 91.8123000,
            'description'  => 'Covered parking with security guards',
            'is_active'    => true,
            'created_by'   => $admin->id,
            'updated_by'   => $admin->id,
        ]);

        $vehicle = VehicleType::create([
            'name'      => 'Private Car',
            'slug'      => 'car-' . uniqid(),
            'is_active' => true,
        ]);

        $rate = FacilityVehicleRate::create([
            'parking_facility_id' => $facility->id,
            'vehicle_type_id'     => $vehicle->id,
            'rate_amount'         => 60.00,
            'rate_unit'           => 'hour',
            'min_charge_amount'   => 60.00,
            'currency'            => 'BDT',
            'is_active'           => true,
        ]);

        $availability = FacilityAvailability::create([
            'parking_facility_id' => $facility->id,
            'vehicle_type_id'     => $vehicle->id,
            'total_slots'         => 25,
            'available_slots'     => 25,
            'last_updated_by'     => $admin->id,
            'last_updated_at'     => now(),
        ]);

        $this->assertEquals($admin->id, $facility->creator->id);
        $this->assertEquals($division->id, $facility->location->id);
        $this->assertTrue($facility->vehicleRates->contains($rate));
        $this->assertTrue($facility->availabilities->contains($availability));

        // Soft delete facility test
        $facility->delete();
        $this->assertSoftDeleted('parking_facilities', ['id' => $facility->id]);
    }

    public function test_facility_vehicle_rate_unique_constraint(): void
    {
        $admin = User::create([
            'name'     => 'Admin User',
            'email'    => 'admin_' . uniqid() . '@example.com',
            'password' => bcrypt('secret'),
        ]);

        $facility = ParkingFacility::create([
            'name'         => 'Facility 1',
            'slug'         => 'fac-rate-' . uniqid(),
            'address_text' => 'Address 1',
            'latitude'     => 23.8103000,
            'longitude'    => 90.4125000,
            'created_by'   => $admin->id,
        ]);

        $vehicle = VehicleType::create([
            'name' => 'Motorcycle',
            'slug' => 'bike-' . uniqid(),
        ]);

        FacilityVehicleRate::create([
            'parking_facility_id' => $facility->id,
            'vehicle_type_id'     => $vehicle->id,
            'rate_amount'         => 20.00,
            'rate_unit'           => 'hour',
            'currency'            => 'BDT',
        ]);

        // Duplicate (facility, vehicle_type, rate_unit) must fail
        $this->expectException(QueryException::class);
        FacilityVehicleRate::create([
            'parking_facility_id' => $facility->id,
            'vehicle_type_id'     => $vehicle->id,
            'rate_amount'         => 30.00,
            'rate_unit'           => 'hour',
            'currency'            => 'BDT',
        ]);
    }

    public function test_booking_payment_and_invoice_lifecycle_relations(): void
    {
        $user = User::create([
            'name'     => 'Driver John',
            'email'    => 'driver_' . uniqid() . '@example.com',
            'phone'    => '01811111111',
            'password' => bcrypt('password'),
            'role'     => 'driver',
        ]);

        $facility = ParkingFacility::create([
            'name'         => 'Gulshan Tower Parking',
            'slug'         => 'gulshan-' . uniqid(),
            'address_text' => 'Gulshan 2, Dhaka',
            'latitude'     => 23.7925000,
            'longitude'    => 90.4078000,
        ]);

        $vehicle = VehicleType::create([
            'name' => 'SUV',
            'slug' => 'suv-' . uniqid(),
        ]);

        $booking = Booking::create([
            'booking_reference'    => 'PKY-' . strtoupper(uniqid()),
            'user_id'              => $user->id,
            'parking_facility_id'  => $facility->id,
            'vehicle_type_id'      => $vehicle->id,
            'vehicle_plate_number' => 'DHK-METRO-GA-11-2233',
            'customer_name'        => 'Driver John',
            'customer_phone'       => '01811111111',
            'planned_start_at'     => now(),
            'duration_minutes'     => 120,
            'planned_end_at'       => now()->addMinutes(120),
            'unit_rate'            => 80.00,
            'rate_unit'            => 'hour',
            'base_amount'          => 160.00,
            'total_amount'         => 160.00,
            'currency'             => 'BDT',
            'booking_status'       => 'pending_payment',
            'payment_status'       => 'initiated',
            'capacity_held'        => true,
            'hold_expires_at'      => now()->addMinutes(15),
        ]);

        $payment = Payment::create([
            'booking_id'            => $booking->id,
            'transaction_reference' => 'TXN-' . strtoupper(uniqid()),
            'provider'              => 'sslcommerz_sandbox',
            'amount'                => 160.00,
            'currency'              => 'BDT',
            'payment_status'        => 'paid',
            'payment_method'        => 'BKASH-BKash',
            'initiated_at'          => now(),
            'verified_at'           => now(),
        ]);

        $invoice = Invoice::create([
            'booking_id'     => $booking->id,
            'invoice_number' => 'INV-' . strtoupper(uniqid()),
            'amount_paid'    => 160.00,
            'currency'       => 'BDT',
            'billing_name'   => 'Driver John',
            'billing_phone'  => '01811111111',
            'issued_at'      => now(),
        ]);

        $notification = AdminNotification::create([
            'recipient_user_id' => null,
            'booking_id'        => $booking->id,
            'type'              => 'new_booking',
            'title'             => 'New Confirmed Booking',
            'message'           => 'Booking ' . $booking->booking_reference . ' confirmed.',
        ]);

        $this->assertEquals($booking->id, $payment->booking->id);
        $this->assertEquals($booking->id, $invoice->booking->id);
        $this->assertEquals($invoice->id, $booking->invoice->id);
        $this->assertTrue($booking->payments->contains($payment));
        $this->assertTrue($booking->adminNotifications->contains($notification));

        // Invoices 1-to-1 unique booking_id constraint
        $this->expectException(QueryException::class);
        Invoice::create([
            'booking_id'     => $booking->id, // Duplicate booking_id must fail
            'invoice_number' => 'INV-DUPLICATE-' . uniqid(),
            'amount_paid'    => 160.00,
            'currency'       => 'BDT',
            'billing_name'   => 'Driver John',
            'billing_phone'  => '01811111111',
            'issued_at'      => now(),
        ]);
    }
}
