<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavouriteResource extends JsonResource
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
            'creator_id' => $this->creator_id,
            'manufacturer_id' => $this->manufacturer_id,
            'scientific_name' => $this->scientific_name,
            'trade_name' => $this->trade_name,
            'type' => $this->type,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'expires_at' => $this->expires_at,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'discount' => $this->discount,
            'photo' => $this->photo,
        ];
    }
}
