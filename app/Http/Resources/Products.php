<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Products extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'product_id' => $this->id,
            'product_name' => $this->title,
            'category_id' => $this->category_id,
            'category_name' => $this->category->title,
            'description' => $this->description,
            'price' => $this->price,
            'discount' => $this->discount,
            'stock' => $this->stock,
            'sold' => $this->sold,
            'barcode' => $this->barcode,
            'images' => $this->product_images->pluck('image')->toArray(),
            'colors' => $this->product_colors->pluck('color')->toArray(),
            'sizes' => $this->product_sizes->pluck('size')->toArray(),
            'deadline' => $this->deadline
        ];
    }
}
