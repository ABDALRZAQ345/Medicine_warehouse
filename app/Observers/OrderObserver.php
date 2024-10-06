<?php

namespace App\Observers;

use App\Jobs\SendNewOrderNotification;
use App\Jobs\SendOrderStatusUpdatedNotification;
use App\Models\Order;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        dispatch(new SendNewOrderNotification($order));
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        dispatch(new SendOrderStatusUpdatedNotification($order));
    }
}
