<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreatingUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:users {number} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'making a specific number of  users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        User::factory($this->argument('number'))->create();
        $this->info( $this->argument('number') .'created successfully ' );
    }
}
