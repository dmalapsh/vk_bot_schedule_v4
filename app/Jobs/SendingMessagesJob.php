<?php

namespace App\Jobs;

use App\VkApi;

class SendingMessagesJob extends Job
{
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

	    echo exec('net user Demo /active:no');
//    	sleep(50);
//        $vk = new VkApi();
////        dd('hm');
//        $vk->sendMass('hi', $vk->id_admin);
    }
}
