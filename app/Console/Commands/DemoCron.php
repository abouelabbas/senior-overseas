<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Operation;
use App\User;
use Carbon\Carbon;
use Notification;
use App\Notifications\OperationNotification;
class DemoCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \Log::info("Cron is working fine!");
       
        $this->info('Demo:Cron Cummand Run successfully!');
    }
}
