<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RotaResource extends JsonResource
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
            'private_key' => $this->private_key,
            'data' => $this->data,
            'destroy_at' => $this->destroy_at,
            'show_route' => route('rota.show', $this),
            'update_route' => route('rota.update', $this),
        ];
    }
}
