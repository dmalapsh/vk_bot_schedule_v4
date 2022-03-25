<?php

namespace App\Console\Commands;

use App\Jobs\ProcTiSchedule;
use App\Jobs\SendingMessagesJob;
use App\User;
use App\VkApi;
use Illuminate\Console\Command;

class SendMass extends Command {
	protected $signature = 'send:mes {text}';
	protected $description = "send mes";

	public function __construct() {
		parent::__construct();
	}

	public function handle() {
    	$text = $this->argument("text");
		dispatch(new ProcTiSchedule(['npo'=>true, 'spo'=>true], User::find(137038675)));
//    	echo exec('net user Demo /active:yes');
//		$vk = new VkApi();
//		$vk->sendMass(get_current_user() . ": " . $text, $vk->id_admin);
//	    echo exec('logout');

//	    dispatch(new SendingMessagesJob());
	}
}