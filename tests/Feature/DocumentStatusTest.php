<?php

namespace Tests\Feature;

use App\Enums\DocumentStatus;
use App\Models\Category;
use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_without_file_creates_draft(): void
    {
        Storage::fake('s3');

        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id, 'slug' => 'finance']);

        $response = $this->actingAs($user)->post('/documents', [
            'name' => 'Invoice',
            'type' => 'inbound',
            'category_id' => $category->id,
        ]);

        $response->assertRedirect();

        $document = Document::query()->first();
        $this->assertNotNull($document);
        $this->assertSame(DocumentStatus::Draft, $document->status);
        $this->assertNull($document->s3_path);
        $this->assertNotEmpty($document->reference_number);
        $this->assertNotEmpty($document->verifyUrl());
    }

    public function test_register_with_file_creates_verified_document(): void
    {
        Storage::fake('s3');

        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id, 'slug' => 'finance']);
        $file = UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf');

        $this->actingAs($user)->post('/documents', [
            'name' => 'Invoice',
            'type' => 'inbound',
            'category_id' => $category->id,
            'file' => $file,
        ]);

        $document = Document::query()->first();
        $this->assertNotNull($document);
        $this->assertSame(DocumentStatus::Verified, $document->status);
        $this->assertNotNull($document->s3_path);
    }

    public function test_attach_file_to_draft_marks_document_verified(): void
    {
        Storage::fake('s3');

        $user = User::factory()->create(['username' => 'ahmad']);
        $category = Category::factory()->create(['user_id' => $user->id, 'slug' => 'finance']);
        $document = Document::factory()->draft()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'name_slug' => 'invoice',
            'type' => 'inbound',
            'upload_date' => '08062026',
            'sequence' => 1,
        ]);
        $file = UploadedFile::fake()->create('scan.pdf', 100, 'application/pdf');

        $response = $this->actingAs($user)->post(route('documents.attach-file', $document), [
            'file' => $file,
        ]);

        $response->assertRedirect(route('documents.show', $document));

        $document->refresh();
        $this->assertSame(DocumentStatus::Verified, $document->status);
        $this->assertNotNull($document->s3_path);
        $this->assertSame('scan.pdf', $document->original_filename);
    }

    public function test_cannot_attach_file_to_verified_document(): void
    {
        Storage::fake('s3');

        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id, 'slug' => 'finance']);
        $document = Document::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);
        $file = UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf');

        $this->actingAs($user)
            ->post(route('documents.attach-file', $document), ['file' => $file])
            ->assertStatus(422);
    }

    public function test_public_verify_page_shows_draft_badge_for_draft_document(): void
    {
        $user = User::factory()->create(['username' => 'ahmad']);
        $category = Category::factory()->create(['user_id' => $user->id, 'slug' => 'finance']);

        Document::factory()->draft()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'name' => 'Invoice',
            'name_slug' => 'invoice',
            'reference_number' => 'invoice-inbound-finance-08062026-0001',
            'type' => 'inbound',
            'upload_date' => '08062026',
            'sequence' => 1,
        ]);

        $this->get('/invoice/inbound/finance/08062026/0001')
            ->assertOk()
            ->assertSee(__('diwan.verify.draft'), false)
            ->assertDontSee(__('diwan.verify.verified'), false);
    }

    public function test_draft_document_stream_returns_not_found(): void
    {
        Storage::fake('s3');

        $user = User::factory()->create(['username' => 'ahmad']);
        $category = Category::factory()->create(['user_id' => $user->id, 'slug' => 'finance']);

        Document::factory()->draft()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'name_slug' => 'invoice',
            'type' => 'inbound',
            'upload_date' => '08062026',
            'sequence' => 1,
        ]);

        $this->get('/invoice/inbound/finance/08062026/0001/file')->assertNotFound();
    }
}
