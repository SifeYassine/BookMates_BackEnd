<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\Genre;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BookControllerTest extends TestCase
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
    }

    public function test_book_can_be_created()
    {
        $response = $this->post("/api/books/create", [
            'title' => 'Test Book',
            'author' => 'Test Author',
            'description' => 'Test Description',
            'cover_image' => UploadedFile::fake()->create('cover.jpg', 100),
            'page_count' => 200,
            'published_year' => 2024,
            'isbn' => '1234567890123',
            'user_id' => 1,
            'genre_id' => 1,
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Book created successfully',
                 ]);

        $this->assertDatabaseHas('books', [
            'title' => 'Test Book',
            'author' => 'Test Author',
        ]);
    }

    public function test_can_get_all_books()
    {
        Book::create([
            'title' => 'Book 1',
            'author' => 'Author 1',
            'description' => 'Description 1',
            'cover_image' => 'test_cover_1.jpg',
            'page_count' => 100,
            'published_year' => 2020,
            'isbn' => '1234567890',
            'user_id' => 1,
            'genre_id' => 1,
        ]);
        
        Book::create([
            'title' => 'Book 2',
            'author' => 'Author 2',
            'description' => 'Description 2',
            'cover_image' => 'test_cover_2.jpg',
            'page_count' => 200,
            'published_year' => 2021,
            'isbn' => '0987654321',
            'user_id' => 1,
            'genre_id' => 1,
        ]);

        $response = $this->get("/api/books/index");

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'All books',
                 ])
                 ->assertJsonStructure([
                     'books' => [
                         '*' => [
                             'title',
                             'author',
                             'description',
                             'cover_image',
                             'page_count',
                             'published_year',
                             'isbn',
                             'user_id',
                             'genre_id'
                         ]
                     ]
                 ]);
    }

    public function test_book_can_be_updated()
    {
        $book = Book::create([
            'title' => 'Old Book',
            'author' => 'Old Author',
            'description' => 'Old Description',
            'cover_image' => 'old_test_cover.jpg',
            'page_count' => 150,
            'published_year' => 2022,
            'isbn' => '1234567890123',
            'user_id' => 1,
            'genre_id' => 1,
        ]);

        $response = $this->post("/api/books/update/{$book->id}", [
            'title' => 'Updated Book',
            'author' => 'Updated Author',
            'description' => 'Updated Description',
            'cover_image' => UploadedFile::fake()->create('new_cover.jpg', 100),
            'page_count' => 250,
            'published_year' => 2023,
            'isbn' => '1234567890123',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Book updated successfully',
                 ]);

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => 'Updated Book',
            'author' => 'Updated Author',
        ]);
    }

    public function test_book_can_be_deleted()
    {
        $book = Book::create([
            'title' => 'Book to Delete',
            'author' => 'Author to Delete',
            'description' => 'Description to Delete',
            'cover_image' => 'test_cover_to_delete.jpg',
            'page_count' => 100,
            'published_year' => 2020,
            'isbn' => '1234567890',
            'user_id' => 1,
            'genre_id' => 1,
        ]);

        $response = $this->delete("/api/books/delete/{$book->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Book deleted successfully',
                 ]);

        $this->assertDatabaseMissing('books', [
            'id' => $book->id,
        ]);
    }
}