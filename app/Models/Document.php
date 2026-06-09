<?php

namespace App\Models;

use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use Database\Factories\DocumentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    /** @use HasFactory<DocumentFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'name_slug',
        'reference_number',
        'type',
        'status',
        'upload_date',
        'sequence',
        's3_path',
        'original_filename',
        'mime_type',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => DocumentType::class,
            'status' => DocumentStatus::class,
            'sequence' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function formattedSequence(): string
    {
        return str_pad((string) $this->sequence, 4, '0', STR_PAD_LEFT);
    }

    public function isDraft(): bool
    {
        return $this->status === DocumentStatus::Draft;
    }

    public function isVerified(): bool
    {
        return $this->status === DocumentStatus::Verified;
    }

    public function hasFile(): bool
    {
        return $this->s3_path !== null;
    }

    /**
     * @return array{document_name: string, doctype: string, category: string, date: string, sequence: string}
     */
    public function verifyRouteParams(): array
    {
        $this->loadMissing('category');

        return [
            'document_name' => $this->name_slug,
            'doctype' => $this->type->value,
            'category' => $this->category->slug,
            'date' => $this->upload_date,
            'sequence' => $this->formattedSequence(),
        ];
    }

    public function verifyPath(): string
    {
        $params = $this->verifyRouteParams();

        return sprintf(
            '/%s/%s/%s/%s/%s',
            $params['document_name'],
            $params['doctype'],
            $params['category'],
            $params['date'],
            $params['sequence']
        );
    }

    public function verifyUrl(): string
    {
        return url($this->verifyPath());
    }
}
