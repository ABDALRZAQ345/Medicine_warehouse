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

            // Create Order
            $order = $this->createOrder($totalPrice);

            foreach ($validated['medicines'] as $medicineData) {
                $medicine = Medicine::findOrFail($medicineData['id']);
                $quantity = $medicineData['quantity'];

                if ($quantity > $medicine->quantity) {
                    DB::rollBack();
                    throw new \Exception('Not enough medicines available for medicine with id ' . $medicineData['id']);
                }

                $totalPrice += ($medicine->price - (($medicine->discount * $medicine->price) / 100.0)) * $quantity;
                // Creating order items
                $this->creatingOrderItems($order, $medicine, $quantity);

            }
            ///

            //Update Order
            $this->updateOrder($order, $totalPrice);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $order;
    }

    public function creatingOrderItems($order, $medicine, mixed $quantity): void
    {
        $order->items()->create([
            'medicine_id' => $medicine->id,
            'quantity' => $quantity,
            'price' => $medicine->price,
            'scientific_name' => $medicine->scientific_name,
            'trade_name' => $medicine->trade_name,
        ]);

        $medicine->quantity -= $quantity;
        $medicine->save();
    }

    /**
     * @return mixed
     */
    public function createOrder(int $totalPrice)
    {
        $order = Order::create([
            'orderer_id' => Auth::id(),
            'total_price' => $totalPrice, // Temporary, will be updated later
            'status' => 0,
            'payment_status' => 0,
        ]);

        return $order;
    }

    public function updateOrder(mixed $order, float|int $totalPrice): void
    {
        $order->update(['total_price' => $totalPrice]);
        $order->save();
    }
}
