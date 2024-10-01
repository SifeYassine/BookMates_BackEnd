<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\Genre;
use App\Models\Book;
use App\Models\BookPost;
use App\Models\ExchangeRequest;
use App\Models\Rating;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RatingControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware();

        // Create roles
        Role::create(['name' => 'Admin', 'description' => 'Administrator role']);
        Role::create(['name' => 'User', 'description' => 'Regular user role']);

        // Create users
        User::create([
            'name' => 'Rater User',
            'email' => 'rater@example.com',
            'password' => 'password123',
            'latitude' => 123.456,
            'longitude' => 456.789,
            'city' => 'Test City',
            'country' => 'Test Country',
            'role_id' => 1,
        ]);

        User::create([
            'name' => 'Ratee User',
            'email' => 'ratee@example.com',
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

        // Create exchange request
        ExchangeRequest::create([
            'status' => 'pending',
            'notification_sent' => 0,
            'requester_id' => 1,
            'bookPost_id' => 1,
        ]);
    }

    public function test_rating_can_be_created()
    {
        $response = $this->post("/api/ratings/create", [
            'rating' => 5,
            'rater_id' => 1,
            'ratee_id' => 2,
            'exchangeRequest_id' => 1,
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Rating created successfully',
                 ]);

        $this->assertDatabaseHas('ratings', [
            'rating' => 5,
            'rater_id' => 1,
            'ratee_id' => 2,
            'exchangeRequest_id' => 1,
        ]);
    }

    public function test_can_get_all_ratings()
    {
        Rating::create([
            'rating' => 4,
            'rater_id' => 1,
            'ratee_id' => 2,
            'exchangeRequest_id' => 1,
        ]);

        $response = $this->get("/api/ratings/index");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                             'rating',
                             'rater_id',
                             'ratee_id',
                             'exchangeRequest_id',
                         ]
                     ]
                 ]);
    }

    public function test_rating_can_be_updated()
    {
        $rating = Rating::create([
            'rating' => 3,
            'rater_id' => 1,
            'ratee_id' => 2,
            'exchangeRequest_id' => 1,
        ]);

        $response = $this->put("/api/ratings/update/{$rating->id}", [
            'rating' => 5,
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Rating updated successfully',
                 ]);

        $this->assertDatabaseHas('ratings', [
            'id' => $rating->id,
            'rating' => 5,
        ]);
    }

    public function test_rating_can_be_deleted()
    {
        $rating = Rating::create([
            'rating' => 4,
            'rater_id' => 1,
            'ratee_id' => 2,
            'exchangeRequest_id' => 1,
        ]);

        $response = $this->delete("/api/ratings/delete/{$rating->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Rating deleted successfully',
                 ]);

        $this->assertDatabaseMissing('ratings', [
            'id' => $rating->id,
        ]);
    }
}
