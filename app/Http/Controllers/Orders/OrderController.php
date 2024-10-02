<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Jobs\SendNewOrderNotification;
use App\Jobs\SendOrderStatusUpdatedNotification;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\WordServices;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group Order
 */
class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Make a new Order
     *
     * @description This endpoint is accessible only to users  not admins
     *
     * @authenticated
     *
     * @header Authorization Bearer {access_token}
     *
     * @response  403 {
     * "message": "User does not have the right role ."
     * }
     * @response  401 {
     * "message": "Unauthenticated."
     * }
     * @response 422  if one of the medicines not found
     * {
     * "message": "The selected medicines.0.id is invalid.",
     * "errors": {
     * "medicines.0.id": [
     * "The selected medicines.0.id is invalid."
     * ]
     * }
     * }
     * @response  422  if no medicines passed
     * {
     * "message": "The medicines field is required.",
     * "errors": {
     * "medicines": [
     * "The medicines field is required."
     * ]
     * }
     * }
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
     */
    public function store(StoreOrderRequest $request)
    {

        $validated = $request->validated();

        try {
            $order = $this->orderService->store($validated);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }

        dispatch(new SendNewOrderNotification($order));

        return response()->json([
            'message' => 'Order placed successfully',
            'order' => new OrderResource($order),
            'download_word_invoice_url' => env('APP_URL').'/api/get_order_invoice/'.$order->id,
        ], 201);

    }

    /**
     * Showing the user 's orders and if he is admin showing all orders
     *
     * @authenticated
     *
     * @header Authorization Bearer {access_token}
     *
     * @queryParam payment_status 0 for unpaid and 1 for paid
     *
     * @response  401 {
     * "message": "Unauthenticated."
     * }
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
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $orders = $user->hasRole('admin') ? Order::query() : $user->orders();

        $filters = $request->only(['payment_status', 'status']);
        $orders = $orders->filter($filters)->paginate();

        return OrderResource::collection($orders);

    }

    /**
     * update  a status for a specific  Order
     *
     * @description This endpoint is accessible only to admins
     *
     * @authenticated
     *
     * @header Authorization Bearer {access_token}
     *
     * @response  403 {
     * "message": "User does not have the right role ."
     * }
     * @response  401 {
     * "message": "Unauthenticated."
     * }
     * @response 404 {
     * "message": " not found."
     * }
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
     */
    public function update(UpateOrderRequest $request, Order $order)
    {
        $validated = $request->validated();

        $order->update($validated);

        dispatch(new SendOrderStatusUpdatedNotification($order));

        return response()->json([
            'message' => 'Order status updated successfully',
            'order' => new OrderResource($order),
        ]);
    }

    public function get_order_invoice(WordServices $wordServices, $order)
    {

        $user = Auth::user();
        $order = $user->orders()->findOrFail($order);
        $filePath = $wordServices->generateInvoice($order, $user);

        return response()->download($filePath);

    }

    public function show(Order $order)
    {

        $user = Auth::user();
        $order = $user->orders()->findOrFail($order->id);
        $order->load('items');

        return new OrderResource($order);
    }
}
