<?php


namespace App;


use App\Jobs\ProcScheduleJob;
use App\Jobs\ProcTiSchedule;
use Carbon\Carbon;
use Imagick;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Schedule {
	public static function checkUpdate($name){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://rasp.vksit.ru/$name.xls");
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'HEAD');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$content = curl_exec ($ch);
		curl_close ($ch);
		$last = explode ("\r\n",$content)[3];
		$updated_at = Property::getValue('updated_at_'.$name);
		Property::setValue('updated_at_date_'.$name, Carbon::now());
//		dd('ok');
		Property::setValue('updated_at_'.$name, $last);
		self::checkUpdatePdf($name);
		return $last != $updated_at;
	}
	public static function checkUpdatePdf($name){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://rasp.vksit.ru/$name.pdf");
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'HEAD');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$content = curl_exec ($ch);
		curl_close ($ch);
		$last = explode ("\r\n",$content)[3];
		$updated_at = Property::getValue('updated_at_'.$name);
		Property::setValue('updated_at_pdf_'.$name, $last);
		return $last != $updated_at;
	}

	public static function clearBG(){
//		return ['npo'=>false, 'spo'=>true];

		$upd = [
			'npo' => 	self::checkUpdate('npo'),
			'spo' => 	self::checkUpdate('spo')
		];
		if($upd['npo']){
			Background::query()->update(['npo_imgs'=>null]);
		}

		if($upd['spo']){
			Background::query()->update(['spo_imgs'=>null]);
		}

		return $upd;
	}

	public static function checkSchedule(){

		$bgs    = self::getBg();
		$bgs[0] = null;
		$upd    = self::clearBG();

		if($upd['spo'] || $upd['npo']){
			//для студентов
			foreach($bgs as $id => $url) {
				dispatch(new ProcScheduleJob($id, $upd, $url));
//			$imgs_arr = [];
//			foreach(['npo', 'spo'] as $item){
//				if ($upd[$item]) {
//					$imgs = self::readePdf("http://rasp.kolledgsvyazi.ru/$item.pdf", $url);
//					$imgs_arr[$item] = $imgs;
//					if($url){
//						Background::find($id)->update([$item . '_imgs' => $imgs]);
//					} else {
//						Property::setValue('imgs_' . $item, $imgs);
//					}
//				}
//			}
//
//			self::procMesStud($id, $imgs_arr);

			}
//			$arr = $upd;
			//для преподов
			$users = User::where('subscribe_status', 1)
				->where('is_student', 0)
				->with('background')
				->get();
			foreach($users as $user){
				dispatch(new ProcTiSchedule($upd, $user));
			}
		}

	}
	public static function procMesTi(){
		$users = User::where('subscribe_status', 1)
			->where('is_student', 0)
			->get();
		foreach($users as $user){
			$search = $user->search_string;
			$proc_npo = new Excel($search, 'npo.xls');
			$proc_spo = new Excel($search, 'spo.xls');
			$classes_npo = $proc_npo->getClasses();
			$classes_spo = $proc_spo->getClasses();
			$result = 0;
//			$imgs = self::readePdf("http://356476-cj50427.tmweb.ru", null);
			$vk = new VkApi();
//			$vk->sendMass('ok',$vk->id_admin, $imgs);
			$imgs = [];
			if($classes_npo == 0 && $classes_spo > 0){

				$proc_spo->save();
				$imgs['spo'] = self::readePdf("http://356476-cj50427.tmweb.ru", null);
				$ids[1] = [$user->id];

			} elseif($classes_npo > 0 && $classes_spo == 0){

				$proc_npo->save();
				$imgs['npo'] = self::readePdf("http://356476-cj50427.tmweb.ru", null);
				$ids[2] = [$user->id];

			}elseif($classes_npo > 0 && $classes_spo > 0){

				$proc_npo->save();
				$imgs['npo'] = self::readePdf("http://356476-cj50427.tmweb.ru", null);
				$proc_spo->save();
				$imgs['spo'] = self::readePdf("http://356476-cj50427.tmweb.ru", null);
				$ids[3] = [$user->id];

			}else {
				$vk->sendMass('Расписание обновлиось, но я не нашел вас в нем');
			}
			self::send($ids, $imgs);

		}
	}
	public static function procMesStud($id, $imgs){

		$users = User::where('subscribe_status', 1)
			->where('background_id',$id)
			->where('is_student', 1)
			->chunk(99, function ($users) use ($imgs) {
				$ids = [];
				foreach($users as $user){
					$build = self::search($user->search_string);
					$ids[$build][] = $user->id;
				}
				self::send($ids,$imgs);
			});

	}
	public static function send($ids, $imgs){
//		return;
		$vk     = new VkApi();

		if(isset($ids[0])){
			if(isset($imgs['npo'])){
				$vk->sendMass('Расписание обновилось. Я не нашел вас в расписании, поэтому буду скидывать обновления обоих корпусов. Вот 2 корпус', implode(',',$ids[0]), $imgs['npo']);
			}
			if(isset($imgs['spo'])){
				$vk->sendMass('Расписание обновилось. Я не нашел вас в расписании, поэтому буду скидывать обновления обоих корпусов. Вот 1 корпус', implode(',',$ids[0]), $imgs['spo']);
			}
		}
		if(isset($ids[1]) && isset($imgs['spo'])){
			$vk->sendMass('Расписание обновилось. Пары в первом корпусе', implode(',',$ids[1]), $imgs['spo']);
		}
		if(isset($ids[2]) && isset($imgs['npo'])){
			$vk->sendMass('Расписание обновилось. Пары во втором корпусе', implode(',',$ids[2]), $imgs['npo']);
		}
		if(isset($ids[3])){
			if(isset($imgs['npo'])){
				$vk->sendMass('Расписание обновилось. Пары в двух корпусах. Вот 2 корпус', implode(',',$ids[3]), $imgs['npo']);
			}
			if(isset($imgs['spo'])){
				$vk->sendMass('Расписание обновилось. Пары в двух корпусах. Вот 1 корпус', implode(',',$ids[3]), $imgs['spo']);
			}
		}
	}

	public static function updateBg(Background $bg){
		foreach(['npo', 'spo'] as $item){
			$prop = $item . '_imgs';
			if(!$bg->$prop){
				$imgs = self::readePdf("http://rasp.vksit.ru/$item.pdf", $bg->url);
				$bg->$prop = $imgs;
				$bg->save();
			}
		}

	}

	public static function readePdf($url, $bg_url){
		$path = storage_path('app/tmp_img.jpg');
		$vk = new VkApi();



		copy($url, storage_path('app/tmp.pdf'));
		$fp_pdf = fopen(storage_path('app/tmp.pdf'), 'rb');

		$_img = new imagick(); // [0] can be used to set page number
		$_img->setResolution(300, 300);
		$_img->readImageFile($fp_pdf);
		$page = array();
		foreach($_img as $img) {
			$img->setImageFormat("jpg");
			$img->setImageCompression(imagick::COMPRESSION_JPEG);
			$img->setImageCompressionQuality(90);
			$img->setImageUnits(imagick::RESOLUTION_PIXELSPERINCH);
			$img->adaptiveResizeImage(2560, 1810);
//						dd($img->getImageBackgroundColor());
			if($bg_url){
				copy($bg_url, storage_path('app/tmp_bg_img.jpg'));
				$bg = new imagick(storage_path('app/tmp_bg_img.jpg'));
			} else {
				//белый фон... здраьсте костыли
//				$bg = new imagick('http://panceramic.ru/image/cache/siena-ivory/catalog/li/siena-ivory-200-200-m-750x750.jpeg');
				$bg = new imagick('C:\OpenServer\domains\bot\storage\app\bg.jpeg');
			}
			$bg->adaptiveResizeImage(2560, 1810);
			$bg->compositeImage($img, imagick::COMPOSITE_MULTIPLY, 0, 0);
			$bg->writeImage($path);
			array_push($page, $vk->createPhoto('tmp_img.jpg'));
		}
		$img->destroy();
		return implode(',', $page);
	}

	public static function getBg(){

		$bgs = \App\Background::pluck('url','id');
		$render_bg = [];
		foreach($bgs as $id => $url){
			if(\App\User::where('background_id', $id)->count()){
				$render_bg[$id] = $url;
			}
		}
		return $render_bg;
	}



	public static function search($search){
		$proc_npo = new Excel($search, 'npo.xls');
		$proc_spo = new Excel($search, 'spo.xls');
		$classes_npo = $proc_npo->getClasses();
		$classes_spo = $proc_spo->getClasses();

		if($classes_npo == 0 && $classes_spo > 0){
			return 1;
		} elseif($classes_npo > 0 && $classes_spo == 0){
			return 2;
		}elseif($classes_npo > 0 && $classes_spo > 0){
			return 3;
		} else {
			return 0;
		}
//		$path = storage_path('app/temp.xlsx');
//		copy('http://rasp.kolledgsvyazi.ru/'. $load, $path);         //загрузка на сервер файла второго корпуса
//
//		$reader = IOFactory::createReader('Xls');//объект читателя
//		$spreadsheet = $reader->load($path);                           //чтение скаченного файла
//
//		$cells_format = [];
//		$classes = 0;
//		$worksheet = $spreadsheet->getActiveSheet();
//		foreach ($worksheet->getRowIterator() as $row) {
//			$cellIterator = $row->getCellIterator();
//			foreach ($cellIterator as $cell) {
//				$value =  $cell->getValue();
//				if(stripos($value, $string) !== false){
//					$row = $cell->getRow();
//					$rowUP = $row - 1;
//					$cells_format[] = $cell->getColumn() . $rowUP;
//					$cells_format[] = $cell->getColumn() . $row;
//					$classes++;
//				}
//			}
//		}
//		return $classes;
//		$this->cells_format = $cells_format;
	}
}