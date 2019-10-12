<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Libraries\FirebaseNotification;

class FirebaseNotifycron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pushnotification:cron';

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
        \Log::info("Push Cron is working fine!");

        $objpush = new FirebaseNotification();
        $objpush->pushNotification();

        $this->info('Push:Cron Command Run successfully!');
    }
}
