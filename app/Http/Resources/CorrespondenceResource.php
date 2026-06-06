<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CorrespondenceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'serial_number' => $this->serial_number,
            'category' => $this->category?->value,
            'category_label' => $this->category_label,
            'sender' => $this->sender,
            'receiver' => $this->receiver,
            'subject' => $this->subject,
            'content' => $this->content,
            'priority' => $this->priority,
            'priority_label' => $this->priority_label,
            'file_path' => $this->file_path,
            'file_url' => $this->file_path ? route('correspondences.download', $this->resource) : null,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'is_frozen' => $this->isFrozen(),
            'created_at' => $this->created_at?->format('Y-m-d H:i'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i'),
            'approved_at' => $this->approved_at?->format('Y-m-d H:i'),
            'verify_url' => url('/verify/'.$this->uuid),
            'qr_code_url' => 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data='.urlencode(url('/verify/'.$this->uuid)),
        ];
    }
}
