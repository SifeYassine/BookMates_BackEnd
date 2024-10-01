<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GenreControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->withoutMiddleware();
    }

    public function test_genre_can_be_created()
    {
        $response = $this->post("/api/genres/create", [
            'name' => 'Test Genre',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Genre created successfully',
                 ]);

        // Assert that the genre was added to the database
        $this->assertDatabaseHas('genres', [
            'name' => 'Test Genre',
        ]);
    }

    public function test_can_get_all_genres()
    {
        Genre::create(['name' => 'Genre 1']);
        Genre::create(['name' => 'Genre 2']);

        $response = $this->get("/api/genres/index");

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'All genres',
                 ])
                 ->assertJsonStructure(['genres' => [['name']]]);
    }

    public function test_genre_can_be_updated()
    {
        $genre = Genre::create(['name' => 'Old Genre']);

        $response = $this->put("/api/genres/update/{$genre->id}", [
            'name' => 'Updated Genre',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Genre updated successfully',
                 ]);

        // Assert that the genre was updated in the database
        $this->assertDatabaseHas('genres', [
            'id' => $genre->id,
            'name' => 'Updated Genre',
        ]);
    }

    public function test_genre_can_be_deleted()
    {
        $genre = Genre::create(['name' => 'Genre to Delete']);

        $response = $this->delete("/api/genres/delete/{$genre->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Genre deleted successfully',
                 ]);

        // Assert that the genre was deleted from the database
        $this->assertDatabaseMissing('genres', [
            'id' => $genre->id,
        ]);
    }
}
