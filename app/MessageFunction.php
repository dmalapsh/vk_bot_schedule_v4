<?php


namespace App;


class MessageFunction {
	public $text;
	public $user_id;
	public $object;
	/**
	 * @var User
	 */
	public $user;
	public $function;
	public $is_user = false;
	public $payload;

	public function __construct($object) {
		$vk = new VkApi();
		$vk->setTypeStatus($object['peer_id']);
		$this->object = $object;
		if(isset($this->object['payload'])){
			$this->payload = json_decode($this->object['payload'])->button ?? null;
		}
		$this->text = $object['text'];
		$this->user_id = $object['peer_id'];
		$this->setUser();

		if(substr($this->user_id,0,5) != '20000' ){
			$this->is_user = true;
		}

		if($this->payload){
			if($this->payload == 'cancel'){
				$this->user->update(['function'=> 'default']);
				return $this->send('Отменено', null, $this->getDefaultBtn());
			}
			$function = $this->function . 'Payload';
		} else {
			$function = $this->function;
		}

		$this->$function();
	}

	public function setUser(){
		$this->user = User::with('background')->find($this->user_id);
		if($this->user && $this->user->function){
			$this->function = $this->user->function;
		} else {
			$this->function = 'default';
			$this->user = new User(['id'=>$this->user_id,]);
		}
	}

	public function default(){
		$vk = new VkApi();
		if($this->is_user){
			if(!$vk->isMember($this->user_id)){
				return $this->send('Beta тест только для учасников сообщества');
			}
		}

		if(isset($this->object['action'])){
			if($this->object['action']['type'] == 'chat_invite_user'){
				return $this->send('Всем привет)');
			}
		}
		switch($this->text){
			case "Начать":
				$this->send('Поздравляю, теперь вы являетесь учасником beta-теста',  null, $this->getDefaultBtn());
				break;
			case "/0":
				$this->send('Доступные фоны:',null, VkApi::BUTTON['cancel']);
				break;
			case '/1':
				if($this->user->background){
					$imgs = $this->user->background->spo_imgs;
				} else {
					$imgs = Property::getValue('imgs_spo');
				}
				if(!$imgs){
					return $this->send('Я обновляю расписание, спросите чуть позже');
				}
				$this->send('1 корпус', $imgs);
				break;
			case '/2':
				if($this->user->background){
					$imgs = $this->user->background->npo_imgs;
				} else {
					$imgs = Property::getValue('imgs_npo');
				}
				if(!$imgs){
					return $this->send('Я обновляю расписание, спросите чуть позже');
				}
				$this->send('2 корпус',  $imgs);
				break;
			case '/фон':
				$this->userFun('bgProcessing');
				$this->send('Отправьте номер фона после символа / (например /1). <br> Доступные фоны:','wall-152828889_15', VkApi::BUTTON['cancel']);
				break;
			case '/?':
				$this->send('start',  null, $this->getDefaultBtn());
				break;
			case '//':
				$this->send('убрано', null, VkApi::BUTTON['none']);
				break;
			case '/tableflip':
				$this->send('(╯°□°）╯︵ ┻━┻');
				break;
			case 'unsub':
				$this->user->subscribe(0);
				$this->send('unsubscribe', null, VkApi::BUTTON['subscribed']);
				break;
			case '/customBg':
				$this->loadBg();
				break;
			case "/3":
				$text = $this->text;
				if($text{0} == "/") {
					$str = substr($text, 1);
					$this->send($str);
				}
				break;
			case '/id':
				$this->send($this->user_id);
				break;
			default:
				if(!$this->is_user){
					return false;
				}
				$text = $this->defaultProcessingCustomText();
				if($text){
					$this->send($text);
				}
		}
	}

	public function defaultPayload(){
		switch($this->payload){
			case '1':
				if($this->user->background){
					$imgs = $this->user->background->spo_imgs;
				} else {
					$imgs = Property::getValue('imgs_spo');
				}
				if(!$imgs){
					return $this->send('Я обновляю расписание, спросите чуть позже');
				}
				$this->send('1 корпус', $imgs);
				break;
			case '2':
				if($this->user->background){
					$imgs = $this->user->background->npo_imgs;
				} else {
					$imgs = Property::getValue('imgs_npo');
				}
				if(!$imgs){
					return $this->send('Я обновляю расписание, спросите чуть позже');
				}
				$this->send('2 корпус',  $imgs);
				break;
			case 'bg':
				$this->userFun('bgProcessing');
				$this->send('Отправьте номер фона после символа / (например /1). <br> Доступные фоны:','wall-152828889_15', VkApi::BUTTON['cancel']);
				break;
			case 'unsub':
				$this->user->subscribe(0);
				$this->send('Вы отписаны', null, $this->getDefaultBtn());
				break;
			case 'sub':
				$this->send('Если вы являетесь студентом, то отправьте название группы как указано в расписании(например ОИБ-219)
 								<br> Если вы являетесь преподавателем, отправьте свою фамилию', null, VkApi::BUTTON['cancel']);
				$this->userFun('sub');
				break;
			default:
				$this->send('Нажата неизвесная кнопка, скорее всего это баг, обратитесь к разработчику');
		}
	}

	private function defaultProcessingCustomText(){
		$text = $this->text;
		if($text{0} == "!"){
			$str = substr($text, 1);
			$str = mb_strtolower($str);
			$this->text = $str;
//			$this->user->subscribe(1, $str);
			return $this->sub();
		}
	}

	private function bgProcessing(){
//		$this->send(json_encode($this->payload));
		if($this->payload == 'cancel'){
			$this->user->update(['function'=> 'default']);
			return $this->send('Устновка фона отменена', null, $this->getDefaultBtn());
		}
		$bg_id = $this->text;
		if($bg_id{0} == "/") {
			$bg_id = substr($bg_id, 1);
		}
		if(is_numeric($bg_id)){
			$bg = Background::find($bg_id);

//			return $this->send(json_encode($bg));
			if($bg || $bg_id == 0){
				if($bg_id) Schedule::updateBg($bg);
				$this->user->update(['function'=> 'default', 'background_id'=> $bg_id]);
				$this->send('Установлен', null, $this->getDefaultBtn());
			} else {
				$this->send('Такого фона не существует');
			}

		} else {
			$this->send('Ожидалось чиcло');
		}
	}

	private function bgProcessingPayload(){
		if($this->payload == 'cancel'){
			$this->user->update(['function'=> 'default']);
			return $this->send('Устновка фона отменена', null, $this->getDefaultBtn());
		}
	}

	public function sub(){
		$builder = Schedule::search($this->text);
		$is_student = preg_match('/[0-9]+/', $this->text);
		$is_student_str = $is_student ? 'студентом': 'преподавателем';
		switch($builder){
			case 0:
				return $this->send('Я не нашел Вас в расписании, проверьте правильность ввода или обратитесь к разработчику');
			case 1:
				$this->send("Вы подписаны и являетесь $is_student_str. Пары на сегодня я нашел в первом корпусе, если это не так - обратитесь к разработчику", null, VkApi::BUTTON['default']);
				break;
			case 2:
				$this->send("Вы подписаны и являетесь $is_student_str. Пары на сегодня я нашел во втором корпусе, если это не так - обратитесь к разработчику", null, VkApi::BUTTON['default']);
				break;
			case 3:
				$this->send("Вы подписаны и являетесь $is_student_str. Пары на сегодня я нашел как в первом, так и во втором корпусе, если это не так - обратитесь к разработчику", null, VkApi::BUTTON['default']);
				break;
		}
		$this->user->subscribe(1, $this->text, $is_student);
		$this->userFun('default');
	}

	public function loadBg(){
		if(!isset($this->object['attachments'][0])){
			return $this->send('С этой командой нужно сразу отправлять изоббражение');
		}
		$attachments = $this->object['attachments'][0]['photo'];
//		return $this->send(json_encode($attachments));
		$bg_url = null;
		foreach($attachments['sizes'] as $photo){
			if($photo['type'] == 'w'){
				$bg_url = $photo['url'];
			}
		}
		if(!$bg_url){
			return $this->send('У изображения маленькое разрешение');
		}
		$bg = new Background(['url'=>$bg_url]);
		$bg->save();
		$this->send('Фон зарегистрирован в системе, теперь его можно подключить используя этот код: ' . $bg->id);
	}

	private function send($text, $imgs = null, $btn = null){
		$vk = new VkApi();
		$vk->sendMass($text ,$this->user_id, $imgs, $btn);

	}

	public function userFun($fun){
		$this->user->function = $fun;
		$this->user->save();
	}
	public function getDefaultBtn(){
		if($this->user->subscribe_status){
			return VkApi::BUTTON['default'];
		} else {
			return VkApi::BUTTON['start'];
		}
	}
}