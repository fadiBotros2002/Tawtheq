<?php

namespace App\Models;

use App\Enums\TransactionCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Correspondence extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'serial_number',
        'category',
        'sender',
        'receiver',
        'subject',
        'content',
        'priority',
        'file_path',
        'status',
        'approved_at',
        'created_by',
    ];

    public const PRIORITY_LABELS = [
        'normal' => 'Normal',
        'urgent' => 'Urgent',
    ];

    public const STATUS_LABELS = [
        'pending' => 'Pending Review',
        'approved' => 'Approved',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category' => TransactionCategory::class,
            'approved_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isFrozen(): bool
    {
        return $this->isApproved();
    }

    public function getCategoryLabelAttribute(): string
    {
        return $this->category?->label() ?? '';
    }

    public function getPriorityLabelAttribute(): string
    {
        return self::PRIORITY_LABELS[$this->priority] ?? $this->priority;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['category'] ?? null, fn (Builder $q, string $category) => $q->where('category', $category))
            ->when($filters['serial_number'] ?? null, fn (Builder $q, string $serial) => $q->where('serial_number', 'like', '%'.$serial.'%'))
            ->when($filters['status'] ?? null, fn (Builder $q, string $status) => $q->where('status', $status));
    }

    public function scopeForRole(Builder $query, User $user): Builder
    {
        return match ($user->role) {
            'creator' => $query->where(function (Builder $q) use ($user) {
                $q->where('created_by', $user->id)->orWhere('status', 'approved');
            }),
            'checker', 'viewer' => $query,
            default => $query,
        };
    }
}
