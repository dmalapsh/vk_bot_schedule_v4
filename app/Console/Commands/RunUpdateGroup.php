<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RunUpdateGroup extends Command {
    protected $signature = 'run:update-group';
	protected $description = "run update group";

    public function __construct() {
        parent::__construct();
    }

    public function handle() {
    	$users = User::where("is_student", 1)->where("subscribe_status", 1)->get();
	    $bar = $this->output->createProgressBar($users->count());

	    $this->line("Create backup users");
    	DB::statement("CREATE TABLE users_beta_backup SELECT * FROM users_beta");

	    $bar->start();
    	foreach($users as $user){
		    if(preg_match("/\d+/", $user->search_string, $matches) !== false){
			    $number = $matches[0] + 100;
			    if($number > 500){
				    $user->subscribe_status = 0;
			    }
			    $new_group = str_replace($matches[0], $number, $user->search_string);
			    $user->search_string = $new_group;
			    $user->save();
		    } else {
			    $this->line("Trouble with" . $user->search_string);
		    }
		    $bar->advance();
	    }
	    $bar->finish();
    }
}
