<?php

namespace App\Console\Commands;

use App\Models\Medicine;
use Illuminate\Console\Command;

class delete_expired_medicines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete_expired_medicines';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'delete expired medicines but note that there data will be loos and will not be calculated as looses any more';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        Medicine::where('expired_at', '<', now())->delete();
        $this->info('expired medicines deleted successfully');

    }

}
