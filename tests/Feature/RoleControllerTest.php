<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoleControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    
        $this->withoutMiddleware();
    }

    public function test_role_can_be_created()
    {
        $response = $this->post("/api/roles/create", [
            'name' => 'Test Role',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Role created successfully',
                 ]);

        // Assert that the role was added to the database
        $this->assertDatabaseHas('roles', [
            'name' => 'Test Role',
        ]);
    }

    public function test_can_get_all_roles()
    {
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'User']);

        $response = $this->get("/api/roles/index");

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Roles fetched successfully',
                 ])
                 ->assertJsonStructure(['roles' => [['name']]]);
    }

    public function test_role_can_be_updated()
    {
        $role = Role::create(['name' => 'Old Role']);

        $response = $this->put("/api/roles/update/{$role->id}", [
            'name' => 'Updated Role',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Role updated successfully',
                 ]);

        // Assert that the role was updated in the database
        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'Updated Role',
        ]);
    }

    public function test_role_can_be_deleted()
    {
        $role = Role::create(['name' => 'Role to Delete']);

        $response = $this->delete("/api/roles/delete/{$role->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Role deleted successfully',
                 ]);

        // Assert that the role was deleted from the database
        $this->assertDatabaseMissing('roles', [
            'id' => $role->id,
        ]);
    }
}
