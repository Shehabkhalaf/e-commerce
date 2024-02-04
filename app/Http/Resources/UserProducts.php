<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProducts extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "category_name" => $this->title,
            "category_id" => $this->id,
            "products" => $this->products->map(function ($product){
                return [
                  "product_id" => $product->id,
                  "category_id" => $this->id,
                  "product_name" => $product->title,
                  "description" => $product->description,
                  "color" => $product->product_colors->pluck('color')->toArray(),
                  "images" => $product->product_images->pluck('image')->toArray(),
                  "price" => $product->price,
                  "discount" => $product->discount,
                  "stock" => $product->stock
                ];
            })
        ];
    }
}
