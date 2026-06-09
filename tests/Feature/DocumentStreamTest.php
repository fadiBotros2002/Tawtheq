<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentStreamTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_stream_own_document(): void
    {
        Storage::fake('s3');

        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id, 'slug' => 'finance']);
        $s3Path = 'documents/test/inbound/finance/08062026/test.pdf';
        Storage::disk('s3')->put($s3Path, 'pdf-content');

        $document = Document::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            's3_path' => $s3Path,
            'original_filename' => 'test.pdf',
            'mime_type' => 'application/pdf',
        ]);

        $this->actingAs($user)
            ->get(route('documents.stream', $document))
            ->assertOk();
    }

    public function test_user_cannot_stream_another_users_document(): void
    {
        Storage::fake('s3');

        $owner = User::factory()->create();
        $other = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $owner->id, 'slug' => 'finance']);
        $s3Path = 'documents/test/inbound/finance/08062026/test.pdf';
        Storage::disk('s3')->put($s3Path, 'pdf-content');

        $document = Document::factory()->create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            's3_path' => $s3Path,
        ]);

        $this->actingAs($other)
            ->get(route('documents.stream', $document))
            ->assertForbidden();
    }

    public function test_public_verify_stream_serves_file(): void
    {
        Storage::fake('s3');

        $user = User::factory()->create(['username' => 'ahmad']);
        $category = Category::factory()->create(['user_id' => $user->id, 'slug' => 'finance']);
        $s3Path = 'documents/ahmad/inbound/finance/08062026/test.pdf';
        Storage::disk('s3')->put($s3Path, 'pdf-content');

        Document::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'name' => 'Invoice',
            'name_slug' => 'invoice',
            'type' => 'inbound',
            'upload_date' => '08062026',
            'sequence' => 1,
            's3_path' => $s3Path,
            'original_filename' => 'test.pdf',
            'mime_type' => 'application/pdf',
        ]);

        $this->get('/invoice/inbound/finance/08062026/0001/file')
            ->assertOk();
    }

    public function test_verify_stream_returns_not_found_when_file_missing(): void
    {
        Storage::fake('s3');

        $user = User::factory()->create(['username' => 'ahmad']);
        $category = Category::factory()->create(['user_id' => $user->id, 'slug' => 'finance']);

        Document::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'name' => 'Invoice',
            'name_slug' => 'invoice',
            'type' => 'inbound',
            'upload_date' => '08062026',
            'sequence' => 1,
            's3_path' => 'documents/ahmad/inbound/finance/08062026/missing.pdf',
        ]);

        $this->get('/invoice/inbound/finance/08062026/0001/file')->assertNotFound();
    }
}
