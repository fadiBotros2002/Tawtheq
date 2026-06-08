<?php

namespace App\Services;

use App\Enums\DocumentType;
use App\Models\Category;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentService
{
    /**
     * Create a document with the next global cumulative sequence and upload to S3.
     *
     * @param  array{type: DocumentType|string, category_id: int, name: string}  $data
     */
    public function createDocument(array $data, User $user, UploadedFile $file): Document
    {
        return DB::transaction(function () use ($data, $user, $file) {
            $lastDocument = Document::query()
                ->orderByDesc('sequence')
                ->lockForUpdate()
                ->first();

            $nextSequence = ($lastDocument?->sequence ?? 0) + 1;
            $uploadDate = now()->format('dmY');
            $type = $data['type'] instanceof DocumentType
                ? $data['type']->value
                : $data['type'];
            $category = Category::query()
                ->where('id', $data['category_id'])
                ->where('user_id', $user->id)
                ->firstOrFail();

            $nameSlug = $this->makeNameSlug($data['name'], $nextSequence);
            $formattedSequence = str_pad((string) $nextSequence, 4, '0', STR_PAD_LEFT);
            $referenceNumber = sprintf(
                '%s-%s-%s-%s-%s',
                $nameSlug,
                $type,
                $category->slug,
                $uploadDate,
                $formattedSequence
            );

            $directory = sprintf(
                'documents/%s/%s/%s/%s',
                $user->username,
                $type,
                $category->slug,
                $uploadDate
            );
            $s3Path = Storage::disk('s3')->putFile($directory, $file);

            return Document::create([
                'user_id' => $user->id,
                'category_id' => $category->id,
                'name' => $data['name'],
                'name_slug' => $nameSlug,
                'reference_number' => $referenceNumber,
                'type' => $type,
                'upload_date' => $uploadDate,
                'sequence' => $nextSequence,
                's3_path' => $s3Path,
                'original_filename' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
            ]);
        });
    }

    /**
     * Build a URL-safe slug from the document display name.
     */
    public function makeNameSlug(string $name, int $sequence): string
    {
        $slug = Str::slug($name);

        if ($slug === '' || ! preg_match('/^[a-z][a-z0-9-]*$/', $slug)) {
            return 'doc-'.$sequence;
        }

        return $slug;
    }
}
