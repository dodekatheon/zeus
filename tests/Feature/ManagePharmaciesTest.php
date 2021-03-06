<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\PharmacyController;
use App\Http\Requests\CreateUpdatePharmacyRequest;
use App\Models\Pharmacy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManagePharmaciesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_admin_can_create_a_pharmacy()
    {
        $this->asAdmin();

        $this->get(route('admin.pharmacies.create'))->assertStatus(200);

        $attributes = [
            'name' => 'Test pharmacy',
            'region' => 'Test region',
            'area' => 'Test area',
            'address' => 'Test address',
            'additional_address' => 'Test additional',
            'phone' => '12345678',
            'am' => '1234'
        ];

        $this->followingRedirects()
            ->postJson(route('admin.pharmacies.store'), $attributes)
            ->assertSee($attributes);

        $this->assertDatabaseHas('pharmacies', $attributes);
    }

    /** @test */
    public function an_unauthenticated_user_cannot_manage_a_pharmacy()
    {
        $pharmacy = Pharmacy::factory()->create();
        $this->get($pharmacy->adminPath())->assertRedirect('/login');
        $this->get(route('admin.pharmacies.create'))->assertRedirect('/login');
        $this->post(route('admin.pharmacies.index'))->assertRedirect('/login');
        $this->get($pharmacy->adminPath() . '/edit')->assertRedirect('/login');
        $this->patch($pharmacy->adminPath())->assertRedirect('/login');
        $this->delete($pharmacy->adminPath())->assertRedirect('/login');

    }

    /** @test */
    public function a_user_cannot_manage_a_pharmacy()
    {
        $this->asAuthenticated();
        $pharmacy = Pharmacy::factory()->create();
        $this->get(route('admin.pharmacies.create'))->assertForbidden();
        $this->post(route('admin.pharmacies.index'))->assertForbidden();
        $this->get($pharmacy->adminPath() . '/edit')->assertForbidden();
        $this->patch($pharmacy->adminPath())->assertForbidden();
        $this->delete($pharmacy->adminPath())->assertForbidden();
    }

    /** @test */
    public function an_admin_can_update_a_pharmacy()
    {
        $this->asAdmin();

        $pharmacy = Pharmacy::factory()->create();

        $this->get($pharmacy->adminPath() . '/edit')->assertOk();

        $attributes = [
            'name' => 'Edited',
            'region' => 'Edited',
            'area' => 'Edited',
            'address' => 'Edited',
            'additional_address' => 'Edited',
            'phone' => '11111111',
            'am' => '1111'
        ];

        $this->followingRedirects()
            ->patch($pharmacy->adminPath(), $attributes)
            ->assertSee($attributes);

        $this->assertDatabaseHas('pharmacies', $attributes);
    }

    /** @test */
    public function a_user_can_view_a_pharmacy_created()
    {
        $this->asAuthenticated();

        $pharmacy = Pharmacy::factory()->create();

        $this->get($pharmacy->adminPath())
            ->assertStatus(200)
            ->assertSee([
                $pharmacy->name,
                $pharmacy->region,
                $pharmacy->area,
                $pharmacy->address,
                $pharmacy->additional_address,
                $pharmacy->phone,
                $pharmacy->am
            ]);

        $this->assertDatabaseHas('pharmacies', $pharmacy->getAttributes());
    }

    /** @test */
    public function an_admin_can_delete_a_pharmacy()
    {
        $this->asAdmin();

        $pharmacy = Pharmacy::factory()->create();

        $this->delete($pharmacy->adminPath())->assertRedirect(route('admin.pharmacies.index'));

        $this->assertSoftDeleted('pharmacies', [
            'name' => $pharmacy->name,
            'slug' => $pharmacy->slug,
            'region' => $pharmacy->region,
            'area' => $pharmacy->area,
            'address' => $pharmacy->address,
            'additional_address' => $pharmacy->additional_address,
            'phone' => $pharmacy->phone,
            'home_phone' => $pharmacy->home_phone,
            'am' => $pharmacy->am,
            'owner_id' => $pharmacy->owner_id,
            'lat' => $pharmacy->lat,
            'lng' => $pharmacy->lng,
        ]);
    }

    // form request validation

    /** @test */
    public function validation_for_pharmacy_creation()
    {
        $this->assertActionUsesFormRequest(
            PharmacyController::class,
            'store',
            CreateUpdatePharmacyRequest::class
        );

        $this->assertActionUsesFormRequest(
            PharmacyController::class,
            'update',
            CreateUpdatePharmacyRequest::class
        );
    }


    /** @test */
    public function an_admin_can_assign_owner_to_pharmacy()
    {
        $this->asAdmin();

        $owner = User::factory()->create();

        $pharmacy = Pharmacy::factory()->create();
        $attributes = [
            'name' => 'Edited',
            'region' => 'Edited',
            'area' => 'Edited',
            'address' => 'Edited',
            'additional_address' => 'Edited',
            'phone' => '11111111',
            'am' => '1111',
            'owner_id' => $owner->id
        ];

        $this->get($pharmacy->adminPath() . '/edit')->assertOk();

        $this->followingRedirects()
            ->patch($pharmacy->adminPath(), $attributes)
            ->assertSee($owner->name);

        $this->assertDatabaseHas('pharmacies', $attributes);
    }
}
