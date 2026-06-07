<?php

namespace App\Services;

use App\Enums\DocumentType;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DocumentService
{
    /**
     * Create a document with the next global cumulative sequence and upload to S3.
     *
     * @param  array{type: DocumentType|string}  $data
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

            $directory = sprintf('documents/%s/%s/%s', $user->username, $type, $uploadDate);
            $s3Path = Storage::disk('s3')->putFile($directory, $file);

            return Document::create([
                'user_id' => $user->id,
                'type' => $type,
                'upload_date' => $uploadDate,
                'sequence' => $nextSequence,
                's3_path' => $s3Path,
                'original_filename' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
            ]);
        });
    }
}
