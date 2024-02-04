<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProduct extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "product_id" => $this->id,
            "category_id" => $this->category_id,
            "product_name" => $this->title,
            "category_name" => $this->category->title,
            "description" => $this->description,
            "color" => $this->product_colors->pluck('color')->toArray(),
            "image" => $this->product_images->pluck('image')->toArray(),
            "stock" => $this->stock,
            "price" => $this->price,
            "discount" => $this->discount
        ];
    }
}
