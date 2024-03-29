<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Libraries\RBMQReceiver;

class EmailCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email to user';

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
        // \Log::info("Cron is working fine!");

        // $objReceiver = new RBMQReceiver();
        // $objReceiver->sendMail();

        // $this->info('Email:Cron Command Run successfully!');

    }
}
