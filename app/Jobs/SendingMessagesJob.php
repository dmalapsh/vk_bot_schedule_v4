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
//    	sleep(10);
        $vk = new VkApi();
        $vk->sendMass('hi', $vk->id_admin);
    }
}
