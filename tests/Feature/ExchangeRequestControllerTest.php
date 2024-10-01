<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\Genre;
use App\Models\Book;
use App\Models\BookPost;
use App\Models\ExchangeRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExchangeRequestControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware();

        // Create roles
        Role::create(['name' => 'Admin', 'description' => 'Administrator role']);
        Role::create(['name' => 'User', 'description' => 'Regular user role']);

        // Create user
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'latitude' => 123.456,
            'longitude' => 456.789,
            'city' => 'Test City',
            'country' => 'Test Country',
            'role_id' => 1,
        ]);

        // Create genre
        Genre::create([
            'name' => 'Test Genre',
        ]);

        // Create books
        Book::create([
            'title' => 'Offered Book',
            'author' => 'Author 1',
            'description' => 'A book for offering',
            'cover_image' => 'book_covers/offered_book.jpg',
            'page_count' => 100,
            'published_year' => 2020,
            'isbn' => '1234567890',
            'user_id' => 1,
            'genre_id' => 1,
        ]);

        Book::create([
            'title' => 'Wished Book',
            'author' => 'Author 2',
            'description' => 'A book for wishing',
            'cover_image' => 'book_covers/wished_book.jpg',
            'page_count' => 200,
            'published_year' => 2021,
            'isbn' => '0987654321',
            'user_id' => 1,
            'genre_id' => 1,
        ]);

        // Create a book post
        BookPost::create([
            'offerer_id' => 1,
            'offeredBook_id' => 1,
            'wishedBook_id' => 2,
        ]);
    }

    public function test_exchange_request_can_be_created()
    {
        $response = $this->post("/api/exchange_requests/create", [
            'status' => 'pending',
            'notification_sent' => 0,
            'requester_id' => 1,
            'bookPost_id' => 1,
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Exchange request created successfully',
                 ]);

        $this->assertDatabaseHas('exchange_requests', [
            'status' => 'pending',
            'notification_sent' => 0,
            'requester_id' => 1,
            'bookPost_id' => 1,
        ]);
    }

    public function test_can_get_all_exchange_requests()
    {
        ExchangeRequest::create([
            'status' => 'pending',
            'notification_sent' => 0,
            'requester_id' => 1,
            'bookPost_id' => 1,
        ]);

        $response = $this->get("/api/exchange_requests/index");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                             'status',
                             'notification_sent',
                             'requester_id',
                             'bookPost_id',
                         ]
                     ]
                 ]);
    }

    public function test_exchange_request_can_be_updated()
    {
        $exchangeRequest = ExchangeRequest::create([
            'status' => 'pending',
            'notification_sent' => 0,
            'requester_id' => 1,
            'bookPost_id' => 1,
        ]);

        $response = $this->put("/api/exchange_requests/update/{$exchangeRequest->id}", [
            'status' => 'accepted',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Exchange request updated successfully',
                 ]);

        $this->assertDatabaseHas('exchange_requests', [
            'id' => $exchangeRequest->id,
            'status' => 'accepted',
            'notification_sent' => 1,
        ]);
    }

    public function test_exchange_request_can_be_deleted()
    {
        $exchangeRequest = ExchangeRequest::create([
            'status' => 'pending',
            'notification_sent' => 0,
            'requester_id' => 1,
            'bookPost_id' => 1,
        ]);

        $response = $this->delete("/api/exchange_requests/delete/{$exchangeRequest->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Exchange request deleted successfully',
                 ]);

        $this->assertDatabaseMissing('exchange_requests', [
            'id' => $exchangeRequest->id,
        ]);
    }
}
