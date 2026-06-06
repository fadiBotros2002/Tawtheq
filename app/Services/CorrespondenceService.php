<?php

namespace App\Services;

use App\Models\Correspondence;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class CorrespondenceService
{
    /**
     * Create a new correspondence record with optional file upload.
     *
     * @param  array<string, mixed>  $data
     */
    public function createCorrespondence(array $data, ?User $user = null): Correspondence
    {
        if (isset($data['file_path']) && $data['file_path'] instanceof UploadedFile) {
            $data['file_path'] = $data['file_path']->store('correspondences', 'public');
        } else {
            unset($data['file_path']);
        }

        $data['uuid'] = (string) Str::uuid();
        $data['status'] = 'pending';
        $data['created_by'] = $user?->id;

        return Correspondence::create($data);
    }

    /**
     * Approve a correspondence, freeze it, and assign a serial number.
     */
    public function approveCorrespondence(Correspondence $correspondence): Correspondence
    {
        if ($correspondence->isFrozen()) {
            throw new InvalidArgumentException('This correspondence has already been approved and cannot be modified.');
        }

        return DB::transaction(function () use ($correspondence) {
            $serialNumber = $this->generateNextSerialNumber($correspondence);

            $correspondence->update([
                'status' => 'approved',
                'serial_number' => $serialNumber,
                'approved_at' => now(),
            ]);

            return $correspondence->fresh();
        });
    }

    /**
     * Generate the next serial number: {TYPE}-{SUBJECT}-{DATE}-{NUMBER}
     * Example: REQ-APPOINTMENT-REQUEST-2026-06-06-0001
     */
    public function generateNextSerialNumber(Correspondence $correspondence): string
    {
        $prefix = $correspondence->category->serialPrefix();
        $date = now()->format('Y-m-d');
        $subjectSlug = Str::upper(Str::slug(Str::limit($correspondence->subject, 40, ''))) ?: 'DOC';
        $pattern = "{$prefix}-%-{$date}-%";

        $lastSerial = Correspondence::query()
            ->where('category', $correspondence->category)
            ->where('serial_number', 'like', $pattern)
            ->lockForUpdate()
            ->orderByDesc('serial_number')
            ->value('serial_number');

        $nextSequence = 1;

        if ($lastSerial) {
            $parts = explode('-', $lastSerial);
            $nextSequence = ((int) end($parts)) + 1;
        }

        return sprintf('%s-%s-%s-%04d', $prefix, $subjectSlug, $date, $nextSequence);
    }
}
