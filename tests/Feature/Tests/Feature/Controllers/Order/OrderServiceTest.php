<?php

namespace Tests\Feature\Controllers\Order;

use App\Models\Medicine;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $orderService;

    public function setUp(): void
    {
        parent::setUp();
        $this->orderService = new OrderService;
    }

    public function test_it_creates_an_order_with_medicines()
    {
        $this->actingAs(User::factory()->create());

        $medicine1 = Medicine::factory()->create(['quantity' => 50, 'price' => 100, 'discount' => 10]);
        $medicine2 = Medicine::factory()->create(['quantity' => 30, 'price' => 200, 'discount' => 20]);

        $validated = [
            'medicines' => [
                ['id' => $medicine1->id, 'quantity' => 5],
                ['id' => $medicine2->id, 'quantity' => 3],
            ],
        ];

        $order = $this->orderService->store($validated);

        $this->assertDatabaseHas('orders', [
            'orderer_id' => Auth::id(),
            'total_price' => 930, // Calculated price after discount
            'status' => 0,
        ]);

        $this->assertDatabaseHas('order_items', [
            'medicine_id' => $medicine1->id,
            'quantity' => 5,
            'price' => $medicine1->price,
        ]);

        $this->assertDatabaseHas('medicines', [
            'id' => $medicine1->id,
            'quantity' => 45, // Updated quantity after order
        ]);
    }

    public function test_it_throws_exception_when_not_enough_stock()
    {
        $this->expectException(\Exception::class);
        $this->actingAs(User::factory()->create());

        $medicine = Medicine::factory()->create(['quantity' => 5]);

        $validated = [
            'medicines' => [
                ['id' => $medicine->id, 'quantity' => 10],
            ],
        ];

        $this->orderService->store($validated);
    }
}
