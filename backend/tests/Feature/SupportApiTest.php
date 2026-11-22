<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\NewsletterSubscriber;
use App\Models\OperatorEnquiry;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SupportApiTest extends TestCase
{
    use DatabaseTransactions;

    public function test_visitor_can_submit_contact_message(): void
    {
        $payload = [
            'name' => 'Fahim Ahmed',
            'email' => 'fahim.ahmed@example.com',
            'phone' => '+8801711223344',
            'topic' => 'report_discrepancy',
            'facility_reference' => 'Gulshan-2 Smart Zone',
            'message' => 'The listed rate shows 80 BDT but on-site board says 100 BDT.',
            'website' => '', // honeypot empty
        ];

        $response = $this->postJson('/api/v1/support/contact', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'fahim.ahmed@example.com',
            'topic' => 'report_discrepancy',
            'status' => 'pending',
        ]);
    }

    public function test_contact_submission_fails_with_honeypot_filled(): void
    {
        $payload = [
            'name' => 'Spam Bot',
            'email' => 'spambot@example.com',
            'topic' => 'general',
            'message' => 'Spam message content here for test.',
            'website' => 'http://spamlink.com',
        ];

        $response = $this->postJson('/api/v1/support/contact', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['website']);
    }

    public function test_operator_enquiry_can_be_submitted_successfully(): void
    {
        $payload = [
            'facility_name' => 'Dhanmondi Tower Underground Garage',
            'contact_name' => 'Kazi Nazmul',
            'email' => 'nazmul.garage@example.com',
            'phone' => '+8801819001122',
            'location_zone' => 'Dhanmondi 27',
            'address' => 'Road 27 (Old), Dhanmondi, Dhaka',
            'capacity' => 45,
            'notes' => 'Basement 1 & 2 parking facility with security staff.',
            'website' => '',
        ];

        $response = $this->postJson('/api/v1/operators/enquiry', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('operator_enquiries', [
            'facility_name' => 'Dhanmondi Tower Underground Garage',
            'email' => 'nazmul.garage@example.com',
            'capacity' => 45,
        ]);
    }

    public function test_newsletter_subscription_saves_unique_email(): void
    {
        $payload = [
            'email' => 'driver.dhaka@example.com',
            'website' => '',
        ];

        $response = $this->postJson('/api/v1/subscribers', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('newsletter_subscribers', [
            'email' => 'driver.dhaka@example.com',
            'is_active' => true,
        ]);

        // Duplicate attempt should return validation error
        $secondResponse = $this->postJson('/api/v1/subscribers', $payload);
        $secondResponse->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
}
