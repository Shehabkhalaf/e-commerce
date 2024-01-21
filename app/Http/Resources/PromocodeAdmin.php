<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PromocodeAdmin extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "promocode" => $this->promocode,
            "started_at" => $this->started_at->format('Y-m-d'),
            "expired_at" => $this->expired_at->format('Y-m-d'),
            "discount" => $this->discount
        ];
    }
}
