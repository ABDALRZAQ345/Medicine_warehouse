<?php

namespace App\Jobs;

use App\Models\Medicine;
use App\Models\User;
use App\Notifications\QuantityAlertNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Notification;

class SendQuantityNotification implements ShouldQueue
{
    use Queueable;

    protected const Quantity = 100;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        $medicines = Medicine::where('quantity', '<=', self::Quantity)->get();
        $admins = User::whereRelation('roles', 'name', '=', 'admin')->get();
        foreach ($medicines as $medicine) {
            Notification::send($admins, new QuantityAlertNotification($medicine));
        }
    }
}
