<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    // Set up the necessary roles before each test
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        Role::create(['name' => 'Admin', 'description' => 'Administrator role']);
        Role::create(['name' => 'User', 'description' => 'Regular user role']);
    }

    public function test_user_can_register()
    {
        $response = $this->post("/api/auth/register", [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'latitude' => 123.456,
            'longitude' => 456.789,
            'city' => 'Test City',
            'country' => 'Test Country',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'User registered successfully',
                 ]);

        // Assert that the user was added to the database
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }

    public function test_user_can_login()
    {
        // Create the user with all necessary fields
        User::create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
            'latitude' => 123.456,
            'longitude' => 456.789,
            'city' => 'Test City',
            'country' => 'Test Country',
            'role_id' => Role::where('name', 'User')->first()->id,
        ]);

        // Attempt to login
        $response = $this->post("/api/auth/login", [
            'email' => 'user@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'User logged in successfully',
                 ])
                 ->assertJsonStructure([
                     'access_token',
                     'token_type',
                     'expires_in',
                 ]);
    }

    public function test_user_can_logout()
    {
        // Create the user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
            'latitude' => 123.456,
            'longitude' => 456.789,
            'city' => 'Test City',
            'country' => 'Test Country',
            'role_id' => Role::where('name', 'User')->first()->id,
        ]);

        // Log the user in to obtain the token
        $loginResponse = $this->post("/api/auth/login", [
            'email' => 'user@example.com',
            'password' => 'password123',
        ]);

        $token = $loginResponse['access_token'];

        // Logout request with the token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post("/api/auth/logout");

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'User logged out successfully',
                 ]);
    }
}
