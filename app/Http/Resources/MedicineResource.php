<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicineResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        $data['expires_at_human'] = Carbon::parse($data['expires_at'])->diffForHumans();

        return [
            'id' => $data['id'],
            'type' => $data['type'],
            'scientific_name' => $data['scientific_name'],
            'trade_name' => $data['trade_name'],
            'price' => $data['price'],
            'discount' => $data['discount'],
            'quantity' => $data['quantity'],
            'photo_url' => env('APP_URL').'/storage/'.$data['photo'],
            'manufacturer_id' => $data['manufacturer_id'],
            'expires_at' => $data['expires_at'],
            'expires_at_human' => $data['expires_at_human'], // Ensure this comes before manufacturer
            'manufacturer' => $data['manufacturer'],
        ];



    }
}
