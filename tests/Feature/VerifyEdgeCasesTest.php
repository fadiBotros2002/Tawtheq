<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerifyEdgeCasesTest extends TestCase
{
    use RefreshDatabase;

    private function createDocument(array $overrides = []): Document
    {
        $user = User::factory()->create(['username' => 'ahmad']);

        $category = Category::factory()->create([
            'user_id' => $user->id,
            'slug' => 'finance',
        ]);

        return Document::factory()->create(array_merge([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'name' => 'Invoice',
            'name_slug' => 'invoice',
            'type' => 'inbound',
            'upload_date' => '08062026',
            'sequence' => 1,
        ], $overrides));
    }

    public function test_verify_returns_not_found_for_invalid_doctype(): void
    {
        $this->createDocument();

        $this->get('/invoice/unknown/finance/08062026/0001')->assertNotFound();
    }

    public function test_verify_returns_not_found_for_invalid_date_format(): void
    {
        $this->createDocument();

        $this->get('/invoice/inbound/finance/2026-06-08/0001')->assertNotFound();
    }

    public function test_verify_returns_not_found_for_wrong_sequence(): void
    {
        $this->createDocument();

        $this->get('/invoice/inbound/finance/08062026/0009')->assertNotFound();
    }


}
