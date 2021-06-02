<?php

namespace App\Jobs;

use App\Background;
use App\Property;
use App\Schedule;

class ProcScheduleJob extends Job
{
	public $tries = 1;
	public $timeout = 60;
	public $bg_id;
	public $upd;
	public $url;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($bg_id,$upd,$url)
    {
        $this->bg_id = $bg_id;
        $this->upd = $upd;
        $this->url = $url;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
	    $imgs_arr = [];
	    foreach(['npo', 'spo'] as $item) {
		    if($this->upd[$item]) {
			    $imgs            = Schedule::readePdf("http://rasp.kolledgsvyazi.ru/$item.pdf", $this->url);
			    $imgs_arr[$item] = $imgs;
			    if($this->url) {
				    Background::find($this->bg_id)->update([$item . '_imgs' => $imgs]);
			    } else {
				    Property::setValue('imgs_' . $item, $imgs);
			    }
		    }
	    }

	    Schedule::procMesStud($this->bg_id, $imgs_arr);
    }
}
