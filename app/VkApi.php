<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VkApi {

	protected $token;
	public $id_admin = 137038675;
	public $group_id = 152828889;
	const BUTTON = [

		'none' =>'{"buttons":[]}',
		'start' => ["buttons" =>[
			[["action"=>['type'=> 'text', 'label'=>'🏢 1 корпус','payload' => ['button'=>"1"]],'color'=>'positive'],["action"=>['type'=> 'text', 'label'=>'🏢 2 корпус','payload' => ['button'=>"2"]],'color'=>'positive']],
			[["action"=>['type'=> 'text', 'label'=>'Подписаться на обн. распис.','payload' => ['button'=>"sub"]],'color'=>'primary']],
			[["action"=>['type'=> 'text', 'label'=>'Сменить фон','payload' => ['button'=>"bg"]],'color'=>'secondary']]
		]
		],
		'default' => ["buttons" =>[
			[["action"=>['type'=> 'text', 'label'=>'🏢 1 корпус','payload' => ['button'=>"1"]],'color'=>'positive'],["action"=>['type'=> 'text', 'label'=>'🏢 2 корпус','payload' => ['button'=>"2"]],'color'=>'positive']],
			[["action"=>['type'=> 'text', 'label'=>'Сменить фон','payload' => ['button'=>"bg"]],'color'=>'secondary'],["action"=>['type'=> 'text', 'label'=>'Отписаться','payload' => ['button'=>"unsub"]],'color'=>'negative']],
			[["action"=>['type'=> 'text', 'label'=>'Пожертвовать','payload' => ['button'=>"don"]],'color'=>'primary']],
		]
		],'{"buttons":[
	        [{"action":{"type":"text","label":"🏢 1 корпус"},"color":"negative","payload": "{\"button\"}"},{"action":{"type":"text","label":"🏢 2 корпус"},"color":"negative"}], 
	        [{"action":{"type":"text","label":"Подписаться на обн. распис."},"color":"positive"}]
	        ],"one_time":false}',
		'subscribed' => '{"buttons":[
	        [{"action":{"type":"text","label":"🏢 1 корпус"},"color":"negative"},{"action":{"type":"text","label":"🏢 2 корпус"},"color":"negative"}],
	        [{"action":{"type":"text","label":"Отписаться"},"color":"positive"}] 
	        ],"one_time":false}',
		'chat' =>'{"buttons":[
	        [{"action":{"type":"text","label":"🏢 1 корпус"},"color":"negative"},{"action":{"type":"text","label":"🏢 2 корпус"},"color":"negative"}]
	        ],"one_time":false}',
		'cancel' => ["buttons" =>[
				[["action"=>['type'=> 'text', 'label'=>'Отмена','payload' => ['button'=>"cancel"]],'color'=>'negative']]
			]
		],
//			'{"buttons":[
//	            [{"action":{"type":"text","label":"Отмена"},"color":"negative"}],
//	        ],"one_time":false}',
	];

	function __construct()
	{
		$this->token = env('VK_TOKEN');
	}


	public function setTypeStatus($user_id){
		$request_params = [
			'peer_id' => $user_id,
			'type'    => 'typing'
		];
		$this->apiRequest($request_params, 'messages.setActivity');
	}
	public function editMessage($text, $message_id, $peer_id){
		$request_params = [
			'message' => $text,
			'message_id'    => $message_id,
			'peer_id'    => $peer_id
		];
		$this->apiRequest($request_params, 'messages.edit');
	}

	public function sendMass($text, $user_id, $attach = null, $btn = false){

		if (strpos($user_id, ',')===false) {
			$type_id = 'peer_id';
		}else{
			$type_id = 'user_ids';
		}
		if(gettype($btn) == 'array'){
			$btn = json_encode($btn);
		}
		$request_params = [
			'message' => $text,
			$type_id => $user_id,
			'attachment'  => $attach,
			'keyboard' => $btn,
			];
		$resp = $this->apiRequest($request_params, 'messages.send');
	}

	public function isMember($user_id){

		$request_params = [
			'group_id' => $this->group_id,
			'user_id'  => $user_id
		];
		$resp = $this->apiRequest($request_params, 'groups.isMember');
		return $resp;

	}


	public function createPhoto($src){

		$img_name = storage_path('app/'.$src);
		$response = $this->apiRequest(["peer_id" => $this->id_admin], 'photos.getMessagesUploadServer');
		$url = $response->upload_url;
		$curl_file = curl_file_create($img_name, 'mimetype' , 'image.jpeg');
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, array('photo' => $curl_file));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		$res = json_decode(curl_exec($ch));
		curl_close($ch);
		$request_params = [
			"photo" => $res->photo,
			"server" =>$res->server,
			"hash" =>$res->hash,
		];
		$photo = $this->apiRequest($request_params, 'photos.saveMessagesPhoto')[0];
		return "photo".$photo->owner_id."_".$photo->id."_".$photo->access_key;
	}

	protected function apiRequest($request, $method){
		 $request['access_token'] = $this->token;
		 $request['v'] = '5.50';

		 $url = sprintf( 'https://api.vk.com/method/%s', $method);

		 $ch = curl_init();
//		 dd($url);
		 curl_setopt_array( $ch, [
			 CURLOPT_POST    => TRUE,            // это именно POST запрос!
			 CURLOPT_RETURNTRANSFER  => TRUE,    // вернуть ответ ВК в переменную
			 CURLOPT_SSL_VERIFYPEER  => FALSE,   // не проверять https сертификаты
			 CURLOPT_SSL_VERIFYHOST  => FALSE,
			 CURLOPT_POSTFIELDS      => $request,
			 CURLOPT_URL             => $url,    // веб адрес запроса
		 ]);
		 $rest = curl_exec($ch); // запрос выполняется и всё возвращает в переменную
		 curl_close( $ch);
		 $rest = json_decode($rest);
		 if(isset($rest->response)){
			 $rest = $rest->response;
		 } else {
		 	dd($rest);
		 }
		 return $rest;
	 }
}
