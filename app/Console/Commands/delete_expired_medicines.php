<?php

namespace App\Console\Commands;

use App\Models\Medicine;
use Illuminate\Console\Command;

class delete_expired_medicines extends Command
{
    protected $signature = 'delete_expired_medicines';

    protected $description = 'delete expired medicines but note that there data will be loos and will not be calculated as looses any more';

    public function handle()
    {

        Medicine::where('expired_at', '<', now())->delete();
        $this->info('expired medicines deleted successfully');

    }
}
