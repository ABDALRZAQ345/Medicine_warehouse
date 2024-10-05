<?php

namespace App\Console\Commands;

use App\Models\Medicine;
use Illuminate\Console\Command;

class delete_expired_medicines extends Command
{
    protected $signature = 'delete_expired_medicines';

    protected $description = 'soft deleting expired medicines ';

    public function handle()
    {

        Medicine::where('expired_at', '<', now())->delete();
        $this->info('expired medicines deleted successfully');

    }
}
