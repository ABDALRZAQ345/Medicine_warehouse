<?php

namespace App\Jobs;

use App\Models\Medicine;
use App\Models\User;
use App\Notifications\QuntitiyAlertNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class SendQuantityNotification implements ShouldQueue
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
        //
        $medicines = Medicine::where('quantity', '<=', 100)->get();
        $admins = User::whereRelation('roles', 'name', '=', 'admin')->get();
        foreach ($medicines as $medicine) {
            Notification::send($admins,new QuntitiyAlertNotification($medicine));
        }
    }
}
