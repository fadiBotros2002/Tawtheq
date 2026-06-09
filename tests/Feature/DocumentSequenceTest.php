<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentSequenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_global_sequence_increments_across_uploads(): void
    {
        Storage::fake('s3');

        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id, 'slug' => 'finance']);
        $file = UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf');

        $this->actingAs($user)->post('/documents', [
            'name' => 'First',
            'type' => 'inbound',
            'category_id' => $category->id,
            'file' => $file,
        ]);

        $this->actingAs($user)->post('/documents', [
            'name' => 'Second',
            'type' => 'outbound',
            'category_id' => $category->id,
            'file' => UploadedFile::fake()->create('doc2.pdf', 100, 'application/pdf'),
        ]);

        $sequences = Document::query()->orderBy('sequence')->pluck('sequence')->all();

        $this->assertSame([1, 2], $sequences);
    }

    public function test_invalid_name_slug_uses_doc_sequence_fallback(): void
    {
        Storage::fake('s3');

        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id, 'slug' => 'finance']);
        $file = UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf');

        $this->actingAs($user)->post('/documents', [
            'name' => '123',
            'type' => 'inbound',
            'category_id' => $category->id,
            'file' => $file,
        ]);

        $document = Document::query()->first();

        $this->assertNotNull($document);
        $this->assertSame('doc-1', $document->name_slug);
        $this->assertStringStartsWith('doc-1-inbound-finance-', $document->reference_number);
    }

    public function test_s3_path_includes_username_type_category_and_date(): void
    {
        Storage::fake('s3');

        $user = User::factory()->create(['username' => 'ahmad']);
        $category = Category::factory()->create(['user_id' => $user->id, 'slug' => 'finance']);
        $file = UploadedFile::fake()->create('scan.pdf', 100, 'application/pdf');

        $this->actingAs($user)->post('/documents', [
            'name' => 'Invoice',
            'type' => 'inbound',
            'category_id' => $category->id,
            'file' => $file,
        ]);

        $document = Document::query()->first();

        $this->assertNotNull($document);
        $this->assertStringStartsWith(
            sprintf('documents/ahmad/inbound/finance/%s/', $document->upload_date),
            $document->s3_path
        );
        Storage::disk('s3')->assertExists($document->s3_path);
    }
}
