<?php

namespace App\Jobs;

use App\Models\Medicine;
use App\Models\User;
use App\Notifications\ExpiryAlertNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Notification;

class SendExpiryNotifications implements ShouldQueue
{
    use Queueable;

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
        $medicines = Medicine::where('expires_at', '<', now()->addDays(30))
            ->where('expires_at', '>', now())
            ->get();
        $admins = User::whereRelation('roles', 'name', '=', 'admin')->get();
        foreach ($medicines as $medicine) {
            Notification::send($admins, new ExpiryAlertNotification($medicine));
        }
    }
}
