<?php

namespace Database\Factories;

use App\Enums\DocumentStatus;
use App\Models\Category;
use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    protected $model = Document::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $uploadDate = now()->format('dmY');
        $sequence = fake()->unique()->numberBetween(1, 9999);
        $type = fake()->randomElement(['inbound', 'outbound']);
        $nameSlug = 'invoice';
        $formattedSequence = str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);

        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'name' => 'Invoice',
            'name_slug' => $nameSlug,
            'reference_number' => sprintf('%s-%s-finance-%s-%s', $nameSlug, $type, $uploadDate, $formattedSequence),
            'type' => $type,
            'status' => DocumentStatus::Verified,
            'upload_date' => $uploadDate,
            'sequence' => $sequence,
            's3_path' => sprintf(
                'documents/%s/%s/%s/%s/%s.pdf',
                'testuser',
                $type,
                'finance',
                $uploadDate,
                fake()->uuid()
            ),
            'original_filename' => 'document.pdf',
            'mime_type' => 'application/pdf',
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'status' => DocumentStatus::Draft,
            's3_path' => null,
            'original_filename' => null,
            'mime_type' => null,
        ]);
    }

    public function verified(): static
    {
        return $this->state(fn () => [
            'status' => DocumentStatus::Verified,
        ]);
    }

    /**
     * Ensure the category belongs to the same user as the document.
     */
    public function configure(): static
    {
        return $this->afterMaking(function (Document $document) {
            if ($document->user_id && $document->category_id) {
                $category = Category::query()->find($document->category_id);
                if ($category && $category->user_id !== $document->user_id) {
                    $document->category_id = Category::factory()->create([
                        'user_id' => $document->user_id,
                    ])->id;
                }
            }
        })->afterCreating(function (Document $document) {
            if (! $document->reference_number) {
                $document->loadMissing('category');
                $document->update([
                    'reference_number' => sprintf(
                        '%s-%s-%s-%s-%s',
                        $document->name_slug,
                        $document->type,
                        $document->category->slug,
                        $document->upload_date,
                        str_pad((string) $document->sequence, 4, '0', STR_PAD_LEFT)
                    ),
                ]);
            }
        });
    }
}
