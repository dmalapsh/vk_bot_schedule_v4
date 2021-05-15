<?php

namespace App\Jobs;

use App\Excel;
use App\Schedule;
use App\User;
use App\VkApi;

class ProcTiSchedule extends Job
{
	public $arr;
	public $user;
    /**
     * Create a new job instance.
     *
     * @return void
     */
	public $tries = 2;
	public $timeout = 120;
    public function __construct($arr,$user) {
        $this->arr = $arr;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
	    $user = $this->user;
	    $arr = $this->arr;

	    $search = $user->search_string;
	    $proc_npo = new Excel($search, 'npo.xls');
	    $proc_spo = new Excel($search, 'spo.xls');
	    $classes_npo = $proc_npo->getClasses();
	    $classes_spo = $proc_spo->getClasses();
	    $vk = new VkApi();

	    $imgs = [];
	    $ids = [null,null,null];
	    if($user->background){
		    $bg = $user->background->url;
	    } else {
		    $bg = false;
	    }

	    if($classes_npo == 0 && $classes_spo > 0){
			if($arr['spo']){
				$proc_spo->save();
				$imgs['spo'] = Schedule::readePdf(storage_path('app\tmp.pdf'), $bg);
				$ids[1] = [$user->id];
			}

	    } elseif($classes_npo > 0 && $classes_spo == 0){
		    if($arr['npo']){
			    $proc_npo->save();
			    $imgs['npo'] = Schedule::readePdf(storage_path('app\tmp.pdf'), $bg);
			    $ids[2] = [$user->id];
		    }

	    }elseif($classes_npo > 0 && $classes_spo > 0){
		    if($arr['npo']){
			    $proc_npo->save();
			    $imgs['npo'] = Schedule::readePdf(storage_path('app\tmp.pdf'), $bg);
			    $ids[3]      = [$user->id];
		    }
		    if($arr['spo']) {
			    $proc_spo->save();
			    $imgs['spo'] = Schedule::readePdf(storage_path('app\tmp.pdf'), $bg);
			    $ids[3]      = [$user->id];
		    }

	    }else {
		    $vk->sendMass('Расписание обновлиось, но я не нашел вас в нем',$user->id);
	    }
	    Schedule::send($ids, $imgs);

    }
}
