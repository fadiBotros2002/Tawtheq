<?php

namespace App\Services;

use App\Enums\DocumentStatus;
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
     * Register a document with the next global cumulative sequence.
     * When a file is provided the document is verified immediately; otherwise it stays draft.
     *
     * @param  array{type: DocumentType|string, category_id: int, name: string}  $data
     */
    public function registerDocument(array $data, User $user, ?UploadedFile $file = null): Document
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

            $attributes = [
                'user_id' => $user->id,
                'category_id' => $category->id,
                'name' => $data['name'],
                'name_slug' => $nameSlug,
                'reference_number' => $referenceNumber,
                'type' => $type,
                'upload_date' => $uploadDate,
                'sequence' => $nextSequence,
                'status' => $file ? DocumentStatus::Verified : DocumentStatus::Draft,
                's3_path' => null,
                'original_filename' => null,
                'mime_type' => null,
            ];

            if ($file) {
                $attributes = array_merge($attributes, $this->uploadFileAttributes($user, $type, $category->slug, $uploadDate, $file));
            }

            return Document::create($attributes);
        });
    }

    /**
     * Attach a file to a draft document and mark it as verified.
     */
    public function attachFile(Document $document, User $user, UploadedFile $file): Document
    {
        return DB::transaction(function () use ($document, $user, $file) {
            $document = Document::query()
                ->whereKey($document->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless($document->user_id === $user->id || $user->isAdmin(), 403);
            abort_unless($document->status === DocumentStatus::Draft, 422);

            $document->loadMissing('category');

            $document->update(array_merge(
                [
                    'status' => DocumentStatus::Verified,
                ],
                $this->uploadFileAttributes(
                    $user,
                    $document->type->value,
                    $document->category->slug,
                    $document->upload_date,
                    $file
                )
            ));

            return $document->fresh();
        });
    }

    /**
     * @return array{s3_path: string, original_filename: string, mime_type: string|null}
     */
    private function uploadFileAttributes(
        User $user,
        string $type,
        string $categorySlug,
        string $uploadDate,
        UploadedFile $file
    ): array {
        $directory = sprintf(
            'documents/%s/%s/%s/%s',
            $user->username,
            $type,
            $categorySlug,
            $uploadDate
        );
        $s3Path = Storage::disk('s3')->putFile($directory, $file);

        return [
            's3_path' => $s3Path,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
        ];
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
