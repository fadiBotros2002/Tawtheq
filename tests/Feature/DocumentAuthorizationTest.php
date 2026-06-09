<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_sees_only_own_documents_on_index(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $ownCategory = Category::factory()->create(['user_id' => $user->id, 'slug' => 'finance']);
        $otherCategory = Category::factory()->create(['user_id' => $other->id, 'slug' => 'finance']);

        Document::factory()->create([
            'user_id' => $user->id,
            'category_id' => $ownCategory->id,
            'name' => 'My Invoice',
            'name_slug' => 'my-invoice',
        ]);
        Document::factory()->create([
            'user_id' => $other->id,
            'category_id' => $otherCategory->id,
            'name' => 'Secret Contract',
            'name_slug' => 'secret-contract',
        ]);

        $this->actingAs($user)
            ->get(route('documents.index'))
            ->assertOk()
            ->assertSee('My Invoice', false)
            ->assertDontSee('Secret Contract', false);
    }

    public function test_admin_sees_all_documents_on_index(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id, 'slug' => 'finance']);

        Document::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'name' => 'Staff Document',
            'name_slug' => 'staff-document',
        ]);

        $this->actingAs($admin)
            ->get(route('documents.index'))
            ->assertOk()
            ->assertSee('Staff Document', false);
    }

    public function test_user_can_view_own_document(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id, 'slug' => 'finance']);
        $document = Document::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'name' => 'My Invoice',
            'name_slug' => 'my-invoice',
        ]);

        $this->actingAs($user)
            ->get(route('documents.show', $document))
            ->assertOk()
            ->assertSee('My Invoice', false)
            ->assertSee($document->verifyUrl(), false);
    }

    public function test_user_cannot_view_another_users_document(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $owner->id, 'slug' => 'finance']);
        $document = Document::factory()->create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
        ]);

        $this->actingAs($other)
            ->get(route('documents.show', $document))
            ->assertForbidden();
    }

    public function test_admin_can_view_any_document(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id, 'slug' => 'finance']);
        $document = Document::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'name' => 'Staff Document',
        ]);

        $this->actingAs($admin)
            ->get(route('documents.show', $document))
            ->assertOk()
            ->assertSee('Staff Document', false);
    }

    public function test_dashboard_redirects_to_documents_index(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('documents.index'));
    }

    public function test_guest_is_redirected_from_documents(): void
    {
        $this->get(route('documents.index'))->assertRedirect(route('login'));
        $this->get(route('documents.create'))->assertRedirect(route('login'));
        $this->get(route('categories.index'))->assertRedirect(route('login'));
    }
}
