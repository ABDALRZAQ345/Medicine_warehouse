<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Medicine;
use App\Models\Order;
use Illuminate\Http\Request;
/**
 * @group Order
 */
class OrderController extends Controller
{

    /**
     * Make a new Order
     *
     * @description This endpoint is accessible only to users  not admins
     * @authenticated
     * @header Authorization Bearer {access_token}
     * @response  403 {
     * "message": "User does not have the right role ."
     * }
     *
     * @response  401 {
     * "message": "Unauthenticated."
     * }
     *
     * @response 422  if one of the medicines not found
     * {
     * "message": "The selected medicines.0.id is invalid.",
     * "errors": {
     * "medicines.0.id": [
     * "The selected medicines.0.id is invalid."
     * ]
     * }
     * }
     *
     * @response  422  if no medicines passed
     * {
     * "message": "The medicines field is required.",
     * "errors": {
     * "medicines": [
     * "The medicines field is required."
     * ]
     * }
     * }
     *
     * @response 201 {
     * "message": "Order placed successfully",
     * "order": {
     * "total_price": 23432,
     * "status": "repairing",
     * "payment_status": "unpaid",
     * "orderer_id": 6,
     * "updated_at": "2024-09-25T12:29:54.000000Z",
     * "created_at": "2024-09-25 12:09:54",
     * "id": 1
     * }
     * }
     *
     *
     */

    public function store(Request $request){

        $validated = $request->validate([
            'medicines' => 'required|array',
            'medicines.*.id' => 'required|exists:medicines,id',
            'medicines.*.quantity' => 'required|integer|min:1',
        ]);

        $totalPrice = 0;
        $orderItems = [];

        foreach ($validated['medicines'] as $medicineData) {
            $medicine = Medicine::find($medicineData['id']);
            $quantity = $medicineData['quantity'];

            if ($quantity > $medicine->quantity) {
                return response()->json(['error' => 'Not enough medicines available for medicine with id  ' . $medicineData['id']], 400);
            }

            $totalPrice += $medicine->price * $quantity;

            $orderItems[] = [
                'medicine_id' => $medicine->id,
                'quantity' => $quantity,
            ];


        }
        ///
        foreach ($validated['medicines'] as $medicineData) {
            $medicine=Medicine::find($medicineData['id']);
            $medicine->quantity -= $medicineData['quantity'];
            $medicine->save();
        }

        $order = \Auth::user()->orders()->create([
            'total_price' => $totalPrice,
            'status' => 0,
            'payment_status' => 0,
        ]);


        foreach ($orderItems as $orderItem) {
            $order->items()->create($orderItem);
        }

        return response()->json([
            'message' => 'Order placed successfully',
            'order' => new OrderResource($order)
        ], 201);
    }

    /**
     * Showing the user 's orders and if he is admin showing all orders
     *

     * @authenticated
     * @header Authorization Bearer {access_token}
     *
     * @response  401 {
     * "message": "Unauthenticated."
     * }
     *
     * @response 200 {
     * "data": [
     * {
     * "id": 1,
     * "orderer_id": 6,
     * "total_price": 23432,
     * "status": "repairing",
     * "payment_status": "unpaid",
     * "created_at": "2024-09-25 12:09:54",
     * "updated_at": "2024-09-25T12:29:54.000000Z"
     * }
     * ],
     * "links": {
     * "first": "http://127.0.0.1:8000/api/orders?page=1",
     * "last": "http://127.0.0.1:8000/api/orders?page=1",
     * "prev": null,
     * "next": null
     * },
     * "meta": {
     * "current_page": 1,
     * "from": 1,
     * "last_page": 1,
     * "links": [
     * {
     * "url": null,
     * "label": "&laquo; Previous",
     * "active": false
     * },
     * {
     * "url": "http://127.0.0.1:8000/api/orders?page=1",
     * "label": "1",
     * "active": true
     * },
     * {
     * "url": null,
     * "label": "Next &raquo;",
     * "active": false
     * }
     * ],
     * "path": "http://127.0.0.1:8000/api/orders",
     * "per_page": 15,
     * "to": 1,
     * "total": 1
     * }
     * }
     *
     */

    public function index(){
        $user = \Auth::user();
        if($user->hasRole('user')){

            $orders = $user->orders()->paginate();
            return OrderResource::collection($orders);
        }
        else if($user->hasRole('admin')){

            $orders = Order::paginate();
            return OrderResource::collection($orders);

        }
      return response()->json(['error' => 'Unauthorized'], 401);

    }
    /**
     * update  a status for a specific  Order
     *
     * @description This endpoint is accessible only to admins
     * @authenticated
     * @header Authorization Bearer {access_token}
     * @response  403 {
     * "message": "User does not have the right role ."
     * }
     *
     * @response  401 {
     * "message": "Unauthenticated."
     * }
     *
     * @response 404 {
     * "message": " not found."
     * }
     *
     * @response  422 {
     * "message": "The status field must be between 0 and 2. (and 1 more error)",
     * "errors": {
     * "status": [
     * "The status field must be between 0 and 2."
     * ],
     * "payment_status": [
     * "The payment status field must be true or false."
     * ]
     * }
     * }
     *
     *
     */

    public function update(Request $request,  Order $order)
    {
        $request->validate([
            'status' => ['required',"integer",'between:0,2'],
            'payment_status' => ['required','boolean'],
        ]);
        $order->status  = $request->status;
        $order->payment_status = $request->payment_status;
        $order->save();
        return response()->json([
           'message' => 'Order status updated successfully',
            'order' => new OrderResource($order)
        ]);
    }

}
