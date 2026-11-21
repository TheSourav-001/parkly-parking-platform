<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class GoogleAuthDatabasePreparationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_users_table_has_google_auth_columns(): void
    {
        $this->assertTrue(Schema::hasColumn('users', 'google_id'));
        $this->assertTrue(Schema::hasColumn('users', 'avatar'));
    }

    public function test_user_model_has_expected_fillable_and_cast_properties(): void
    {
        $user = new User();

        $this->assertContains('google_id', $user->getFillable());
        $this->assertContains('avatar', $user->getFillable());
        $this->assertContains('email', $user->getFillable());
        $this->assertContains('password', $user->getFillable());
        $this->assertContains('role', $user->getFillable());
        $this->assertContains('is_active', $user->getFillable());

        // Password remains hidden
        $this->assertContains('password', $user->getHidden());
    }

    public function test_multiple_users_can_have_null_google_id(): void
    {
        $user1 = User::create([
            'name' => 'User One',
            'email' => 'user_one_' . uniqid() . '@example.com',
            'password' => 'secret1234',
            'google_id' => null,
            'avatar' => null,
            'role' => 'driver',
        ]);

        $user2 = User::create([
            'name' => 'User Two',
            'email' => 'user_two_' . uniqid() . '@example.com',
            'password' => 'secret5678',
            'google_id' => null,
            'avatar' => null,
            'role' => 'driver',
        ]);

        $this->assertNull($user1->google_id);
        $this->assertNull($user2->google_id);
        $this->assertDatabaseHas('users', ['id' => $user1->id, 'google_id' => null]);
        $this->assertDatabaseHas('users', ['id' => $user2->id, 'google_id' => null]);
    }

    public function test_duplicate_non_null_google_id_is_rejected(): void
    {
        $googleId = 'google_uid_' . uniqid();

        User::create([
            'name' => 'First Google User',
            'email' => 'first_google_' . uniqid() . '@example.com',
            'password' => 'secure_random_pwd',
            'google_id' => $googleId,
            'avatar' => 'https://lh3.googleusercontent.com/a/avatar1',
            'role' => 'driver',
        ]);

        $this->expectException(QueryException::class);

        User::create([
            'name' => 'Duplicate Google User',
            'email' => 'second_google_' . uniqid() . '@example.com',
            'password' => 'secure_random_pwd_2',
            'google_id' => $googleId, // Should trigger unique constraint violation
            'avatar' => 'https://lh3.googleusercontent.com/a/avatar2',
            'role' => 'driver',
        ]);
    }

    public function test_user_can_be_created_with_avatar_and_google_id(): void
    {
        $avatarUrl = 'https://lh3.googleusercontent.com/a/ACg8ocL' . uniqid();
        $googleId = '10987654321_' . uniqid();

        $user = User::create([
            'name' => 'OAuth User',
            'email' => 'oauth_' . uniqid() . '@example.com',
            'password' => 'generated_secure_hash',
            'google_id' => $googleId,
            'avatar' => $avatarUrl,
            'role' => 'driver',
        ]);

        $this->assertEquals($googleId, $user->google_id);
        $this->assertEquals($avatarUrl, $user->avatar);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'google_id' => $googleId,
            'avatar' => $avatarUrl,
        ]);
    }
}

