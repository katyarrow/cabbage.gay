<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PowCaptchaChallengeResource extends JsonResource
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
            'puzzles' => json_decode($this->puzzles_json),
            'difficulty' => $this->difficulty,
            'verify_route' => route('captcha.verify', $this),
        ];
    }
}
