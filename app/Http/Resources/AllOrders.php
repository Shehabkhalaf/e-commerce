<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AllOrders extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'address' => $this->address,
            'governorate' => $this->governorate,
            'city' => $this->city,
            'postal' => $this->postal,
            'phone' => $this->phone,
            'status' => $this->status,
            'promocode' => $this->promocode,
            'total_price' => $this->total_price,
            'payment_method' => 'cash',
            'created_at' => $this->created_at->format('Y-m-d'),
            /*'pending_date' => $this->orderStatus->pending->format('Y-m-d - H:i'),
            'processing_date' => $this->orderStatus->processing->format('Y-m-d - H:i'),
            'delivered_date' => $this->orderStatus->delivered->format('Y-m-d - H:i'),*/
        ];
    }
}
