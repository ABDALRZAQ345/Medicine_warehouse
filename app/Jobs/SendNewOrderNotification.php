<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\NewOrderNotification;
use App\Notifications\OrderStatusUpdatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNewOrderNotification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    protected $order;
    public function __construct($order)
    {
        //
        $this->order=$order;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $admins = User::whereRelation('roles', 'name', '=', 'admin')->get();
        Notification::send($admins, new NewOrderNotification($this->order));
    }
}
