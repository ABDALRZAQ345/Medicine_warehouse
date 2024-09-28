<?php

namespace App\Http\Resources;

use App\OrderStatus;
use App\Services\personal;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{


    public function scopeFilter(Builder $query, $filters)
    {
        foreach ($filters as $key => $value) {
            if (!is_null($value)) {
                $query->where($key, $value);
            }
        }

        return $query;
    }

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
