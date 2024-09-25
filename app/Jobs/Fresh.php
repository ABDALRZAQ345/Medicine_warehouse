<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Fresh implements ShouldQueue
{
    use Queueable,Dispatchable, InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected  $number;
    public function __construct($number)
    {
        //
        $this->number = $number;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        User::take($this->number)->delete();
        //
    }
}
