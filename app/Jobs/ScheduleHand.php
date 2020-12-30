<?php

namespace App\Jobs;

use App\Background;
use App\Property;
use App\Schedule;
use App\User;
use App\VkApi;

class ScheduleHand extends Job
{
	public $tries = 2;
	public $timeout = 120;
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
		$this->handle();
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle() {
		if(){

		}
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
//
//
//		Schedule::procMesStud($this->bg_id, $imgs_arr);
		$ctx = $this;
		User::where('subscribe_status', 1)
			->where('background_id', $this->bg_id)
			->where('is_student', 3)
			->chunk(99, function ($users) use ($ctx, $imgs_arr) {
				$ctx->send($users, $imgs_arr);
			});
	}

	public function send($users, $imgs_arr){
		$vk = new VkApi();
		$user_ids = $users->pluck('id')->toArray();
		if($this->upd['spo']){
			$vk->sendMass('На сайте коллледжа опять не выложили эксель файл для обновления! Админка сказал что есть критические изменения в расписании 1 корпуса.', implode(',',$user_ids), $imgs_arr['spo']);
		}
		if($this->upd['npo']){
			$vk->sendMass('На сайте коллледжа опять не выложили эксель файл для обновления! Админка сказал что есть критические изменения в расписании 2 корпуса.' , implode(',',$user_ids), $imgs_arr['npo']);
		}
	}
}
