<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\WordServices;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

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

        return response()->json([
            'message' => 'Order placed successfully',
            'order' => new OrderResource($order),
            'download_word_invoice_url' => env('APP_URL').'/api/get_order_invoice/'.$order->id,
        ], 201);

    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $orders = $user->hasRole('admin') ? Order::query() : $user->orders();

        $filters = $request->only(['payment_status', 'status']);
        $orders = $orders->filter($filters)->paginate();

        return OrderResource::collection($orders);

    }

    public function update(UpateOrderRequest $request, Order $order)
    {
        $validated = $request->validated();

        $order->update($validated);

        return response()->json([
            'message' => 'Order status updated successfully',
            'order' => new OrderResource($order),
        ]);
    }

    public function get_order_invoice(WordServices $wordServices, $order)
    {

        $user = Auth::user();
        if ($user->hasRole('admin')) {
            $order = Order::findOrFail($order);
        } else {
            $order = $user->orders()->findOrFail($order);
        }
        $filePath = $wordServices->generateInvoice($order, $user);

        return response()->download($filePath);

    }

    public function show(Order $order)
    {

        $user = Auth::user();
        if ($user->hasRole('admin')) {
            $order = Order::findOrfail($order->id);
        } else {
            $order = $user->orders()->findOrFail($order->id);
        }

        $order->load('items');

        return new OrderResource($order);
    }
}
