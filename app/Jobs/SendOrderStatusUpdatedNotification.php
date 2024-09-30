<?php

namespace App\Jobs;

use App\Notifications\OrderStatusUpdatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendOrderStatusUpdatedNotification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    protected $order;
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        \Notification::send($this->order->user, new OrderStatusUpdatedNotification($this->order));
    }
}
