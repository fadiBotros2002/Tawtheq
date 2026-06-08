<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_own_categories(): void
    {
        $user = User::factory()->create();
        Category::factory()->create([
            'user_id' => $user->id,
            'name_ar' => 'مالية',
            'slug' => 'finance',
        ]);

        $this->actingAs($user)
            ->get(route('categories.index'))
            ->assertOk()
            ->assertSee('finance', false)
            ->assertSee('مالية', false);
    }

    public function test_user_does_not_see_other_users_categories(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        Category::factory()->create([
            'user_id' => $other->id,
            'name_ar' => 'سرية',
            'slug' => 'secret',
        ]);

        $this->actingAs($user)
            ->get(route('categories.index'))
            ->assertOk()
            ->assertDontSee('secret', false)
            ->assertDontSee('سرية', false);
    }

    public function test_user_can_create_category(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('categories.store'), [
            'name_ar' => 'الشؤون المالية',
            'name_en' => 'Finance',
            'slug' => 'finance',
        ]);

        $response->assertRedirect(route('categories.index'));

        $this->assertDatabaseHas('categories', [
            'user_id' => $user->id,
            'slug' => 'finance',
            'name_ar' => 'الشؤون المالية',
        ]);
    }

    public function test_slug_must_be_unique_per_user(): void
    {
        $user = User::factory()->create();
        Category::factory()->create(['user_id' => $user->id, 'slug' => 'finance']);

        $response = $this->actingAs($user)->post(route('categories.store'), [
            'name_ar' => 'تصنيف آخر',
            'name_en' => 'Other',
            'slug' => 'finance',
        ]);

        $response->assertSessionHasErrors('slug');
    }

    public function test_different_users_can_use_same_slug(): void
    {
        $first = User::factory()->create();
        $second = User::factory()->create();

        Category::factory()->create(['user_id' => $first->id, 'slug' => 'finance']);

        $response = $this->actingAs($second)->post(route('categories.store'), [
            'name_ar' => 'مالية',
            'name_en' => 'Finance',
            'slug' => 'finance',
        ]);

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseCount('categories', 2);
    }
}
