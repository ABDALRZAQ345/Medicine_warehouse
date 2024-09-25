<?php

namespace App\Http\Resources;

use App\OrderStatus;
use App\personal;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data=parent::toArray($request);

        $data['status'] = personal::$order_status[$data['status']];
        $data['payment_status'] = personal::$payment_status[$data['payment_status']];
        return  $data;
    }
}
