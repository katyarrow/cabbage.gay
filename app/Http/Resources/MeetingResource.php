<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeetingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'identifier' => $this->identifier,
            'created_at' => $this->created_at->toDateTimeString(),
            'encrypted_content' => $this->encrypted_content,
        ];
    }
}
