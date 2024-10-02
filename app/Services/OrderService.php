<?php

namespace App\Services;

use App\Models\Medicine;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function store($validated)
    {
        $totalPrice = 0;

        try {

            DB::beginTransaction();

            $order = Order::create([
                'orderer_id' => Auth::id(),
                'total_price' => $totalPrice, // Temporary, will be updated later
                'status' => 0,
                'payment_status' => 0,
            ]);

            foreach ($validated['medicines'] as $medicineData) {
                $medicine = Medicine::findOrFail($medicineData['id']);
                $quantity = $medicineData['quantity'];

                if ($quantity > $medicine->quantity) {
                    DB::rollBack();
                    throw new \Exception('Not enough medicines available for medicine with id '.$medicineData['id']);
                }

                $totalPrice += ($medicine->price - (($medicine->discount * $medicine->price) / 100.0)) * $quantity;

                $order->items()->create([
                    'medicine_id' => $medicine->id,
                    'quantity' => $quantity,
                    'price' => $medicine->price,
                    'scientific_name' => $medicine->scientific_name,
                    'trade_name' => $medicine->trade_name,
                ]);

                $medicine->quantity -= $medicineData['quantity'];
                $medicine->save();

            }
            ///

            $order->update(['total_price' => $totalPrice]);
            $order->save();
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $order;
    }
}
