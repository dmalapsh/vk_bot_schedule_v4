<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RunCron extends Command {
    protected $signature = 'run:cron';
	protected $description = "run cron";

    public function __construct() {
        parent::__construct();
    }

    public function handle() {
	    \App\Schedule::checkSchedule();
        echo 'successful run';
    }
}
