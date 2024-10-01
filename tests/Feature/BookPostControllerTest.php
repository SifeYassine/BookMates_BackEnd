<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\Genre;
use App\Models\Book;
use App\Models\BookPost;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookPostControllerTest extends TestCase
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
    }

    public function test_book_post_can_be_created()
    {
        $response = $this->post("/api/book_posts/create", [
            'offerer_id' => 1,
            'offeredBook_id' => 1,
            'wishedBook_id' => 2,
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Book post created successfully',
                 ]);

        $this->assertDatabaseHas('book_posts', [
            'offerer_id' => 1,
            'offeredBook_id' => 1,
            'wishedBook_id' => 2,
        ]);
    }

    public function test_can_get_all_book_posts()
    {
        BookPost::create([
            'offerer_id' => 1,
            'offeredBook_id' => 1,
            'wishedBook_id' => 2,
        ]);

        $response = $this->get("/api/book_posts/index");

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'All book posts',
                 ])
                 ->assertJsonStructure([
                     'bookPosts' => [
                         '*' => [
                             'offerer_id',
                             'offeredBook_id',
                             'wishedBook_id',
                         ]
                     ]
                 ]);
    }

    public function test_book_post_can_be_updated()
    {
        $bookPost = BookPost::create([
            'offerer_id' => 1,
            'offeredBook_id' => 1,
            'wishedBook_id' => 2,
        ]);

        $response = $this->put("/api/book_posts/update/{$bookPost->id}", [
            'offeredBook_id' => 2,
            'wishedBook_id' => 1,
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Book post updated successfully',
                 ]);

        $this->assertDatabaseHas('book_posts', [
            'id' => $bookPost->id,
            'offeredBook_id' => 2,
            'wishedBook_id' => 1,
        ]);
    }

    public function test_book_post_can_be_deleted()
    {
        $bookPost = BookPost::create([
            'offerer_id' => 1,
            'offeredBook_id' => 1,
            'wishedBook_id' => 2,
        ]);

        $response = $this->delete("/api/book_posts/delete/{$bookPost->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Book post deleted successfully',
                 ]);

        $this->assertDatabaseMissing('book_posts', [
            'id' => $bookPost->id,
        ]);
    }
}
