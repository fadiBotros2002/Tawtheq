<?php

namespace App\Models;

use App\Enums\DocumentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'type',
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
            'sequence' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function formattedSequence(): string
    {
        return str_pad((string) $this->sequence, 4, '0', STR_PAD_LEFT);
    }

    public function verifyPath(): string
    {
        return sprintf(
            '/%s/%s/%s/%s',
            $this->user->username,
            $this->type->value,
            $this->upload_date,
            $this->formattedSequence()
        );
    }

    public function verifyUrl(): string
    {
        return url($this->verifyPath());
    }
}
