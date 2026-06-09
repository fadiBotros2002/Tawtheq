<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_verify_url_includes_document_name_slug(): void
    {
        $user = User::factory()->create(['username' => 'ahmad']);
        $category = Category::factory()->create([
            'user_id' => $user->id,
            'slug' => 'finance',
        ]);
        $document = Document::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'name' => 'Invoice',
            'name_slug' => 'invoice',
            'reference_number' => 'invoice-inbound-finance-08062026-0001',
            'type' => 'inbound',
            'upload_date' => '08062026',
            'sequence' => 1,
        ]);

        $this->assertStringContainsString('/invoice/inbound/finance/08062026/0001', $document->verifyUrl());
    }

    public function test_reference_number_is_generated_on_register(): void
    {
        Storage::fake('s3');

        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id, 'slug' => 'electronics']);
        $file = UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf');

        $this->actingAs($user)->post('/documents', [
            'name' => 'Invoice',
            'type' => 'inbound',
            'category_id' => $category->id,
            'file' => $file,
        ]);

        $document = Document::query()->first();
        $this->assertNotNull($document);
        $this->assertStringStartsWith('invoice-inbound-electronics-', $document->reference_number);
        $this->assertStringEndsWith('-'.$document->formattedSequence(), $document->reference_number);
    }

    public function test_same_category_slug_can_exist_for_different_users(): void
    {
        $ahmad = User::factory()->create(['username' => 'ahmad']);
        $sara = User::factory()->create(['username' => 'sara']);

        Category::factory()->create(['user_id' => $ahmad->id, 'slug' => 'finance']);
        Category::factory()->create(['user_id' => $sara->id, 'slug' => 'finance']);

        $this->assertDatabaseCount('categories', 2);
    }

    public function test_public_verify_page_loads_with_document_name_in_url(): void
    {
        Storage::fake('s3');

        $user = User::factory()->create(['username' => 'ahmad']);
        $category = Category::factory()->create([
            'user_id' => $user->id,
            'slug' => 'finance',
        ]);
        $s3Path = 'documents/ahmad/inbound/finance/08062026/test.pdf';
        Storage::disk('s3')->put($s3Path, 'pdf-content');

        Document::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'name' => 'Invoice',
            'name_slug' => 'invoice',
            'reference_number' => 'invoice-inbound-finance-08062026-0001',
            'type' => 'inbound',
            'upload_date' => '08062026',
            'sequence' => 1,
            's3_path' => $s3Path,
            'original_filename' => 'test.pdf',
            'mime_type' => 'application/pdf',
        ]);

        $this->get('/invoice/inbound/finance/08062026/0001')
            ->assertOk()
            ->assertSee(__('diwan.verify.verified'), false);
    }

    public function test_verify_returns_not_found_with_wrong_document_name_slug(): void
    {
        $user = User::factory()->create(['username' => 'ahmad']);
        $category = Category::factory()->create([
            'user_id' => $user->id,
            'slug' => 'finance',
        ]);

        Document::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'name' => 'Invoice',
            'name_slug' => 'invoice',
            'reference_number' => 'invoice-inbound-finance-08062026-0001',
            'type' => 'inbound',
            'upload_date' => '08062026',
            'sequence' => 1,
        ]);

        $this->get('/contract/inbound/finance/08062026/0001')->assertNotFound();
    }

    public function test_user_can_register_document_with_own_category(): void
    {
        Storage::fake('s3');

        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id, 'slug' => 'finance']);
        $file = UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf');

        $response = $this->actingAs($user)->post('/documents', [
            'name' => 'Invoice',
            'type' => 'inbound',
            'category_id' => $category->id,
            'file' => $file,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('documents', [
            'user_id' => $user->id,
            'category_id' => $category->id,
            'name' => 'Invoice',
            'name_slug' => 'invoice',
            'type' => 'inbound',
        ]);
    }

    public function test_user_cannot_register_with_another_users_category(): void
    {
        Storage::fake('s3');

        $owner = User::factory()->create();
        $other = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $owner->id, 'slug' => 'finance']);
        $file = UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf');

        $response = $this->actingAs($other)->post('/documents', [
            'name' => 'Invoice',
            'type' => 'inbound',
            'category_id' => $category->id,
            'file' => $file,
        ]);

        $response->assertSessionHasErrors('category_id');
    }

    public function test_register_requires_category(): void
    {
        Storage::fake('s3');

        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf');

        $response = $this->actingAs($user)->post('/documents', [
            'name' => 'Invoice',
            'type' => 'inbound',
            'file' => $file,
        ]);

        $response->assertSessionHasErrors('category_id');
    }

    public function test_register_requires_name(): void
    {
        Storage::fake('s3');

        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id, 'slug' => 'finance']);
        $file = UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf');

        $response = $this->actingAs($user)->post('/documents', [
            'type' => 'inbound',
            'category_id' => $category->id,
            'file' => $file,
        ]);

        $response->assertSessionHasErrors('name');
    }
}
