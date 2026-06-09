<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_upload_rejects_invalid_file_type(): void
    {
        Storage::fake('s3');

        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id, 'slug' => 'finance']);
        $file = UploadedFile::fake()->create('script.exe', 100, 'application/octet-stream');

        $response = $this->actingAs($user)->post('/documents', [
            'name' => 'Invoice',
            'type' => 'inbound',
            'category_id' => $category->id,
            'file' => $file,
        ]);

        $response->assertSessionHasErrors('file');
    }

    public function test_upload_rejects_oversized_file(): void
    {
        Storage::fake('s3');

        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id, 'slug' => 'finance']);
        $file = UploadedFile::fake()->create('large.pdf', 51201, 'application/pdf');

        $response = $this->actingAs($user)->post('/documents', [
            'name' => 'Invoice',
            'type' => 'inbound',
            'category_id' => $category->id,
            'file' => $file,
        ]);

        $response->assertSessionHasErrors('file');
    }

    public function test_upload_requires_valid_document_type(): void
    {
        Storage::fake('s3');

        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id, 'slug' => 'finance']);
        $file = UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf');

        $response = $this->actingAs($user)->post('/documents', [
            'name' => 'Invoice',
            'type' => 'invalid',
            'category_id' => $category->id,
            'file' => $file,
        ]);

        $response->assertSessionHasErrors('type');
    }
}
