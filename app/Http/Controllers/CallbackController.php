<?php

namespace App\Http\Controllers;

use App\MessageFunction;
use App\Property;
use App\User;
use App\VkApi;
use Illuminate\Http\Request;

class CallbackController extends Controller
{
	public $user_id;
    public function index(Request $request){
    	$data = $request->all();
	    switch ($request->type) {
		    case 'confirmation':
			    return response(env('CONFORMATION_TOKEN'));
			    break;
		    //Если это уведомление о новом сообщении...
		    case 'message_new':
//		    	$this->processingMessage();
			    $this->user_id = $request->object['peer_id'];
		    	new MessageFunction($data['object']);
			    break;
		    //если запрет на отправку сообщений
		    case 'message_deny':
			    $user_id = $data->object->user_id;
			    subscribe($user_id, 0);
	    }
    	return response('ok');
    }

    protected function processingMessage($data){
    	$vk = new VkApi();
    	switch($data['text']){
		    case '/1':
		    	$imgs = Property::getValue('imgs_spo');
			    $vk->sendMass('1 корпус', $data['peer_id'], $imgs);
			    break;
		    case '/2':
			    $imgs = Property::getValue('imgs_npo');
			    $vk->sendMass('2 корпус', $data['peer_id'], $imgs);
			    break;
		    case '?':
			    $vk->sendMass('start', $data['peer_id'], null, VkApi::BUTTON['start']);
			    break;
		    case '//':
			    $vk->sendMass('убрано', $data['peer_id'], null, VkApi::BUTTON['none']);
			    break;
		    case 'unsub':
		    	$this->subscribe(0);
			    $vk->sendMass('unsubscribe', $data['peer_id'], null, VkApi::BUTTON['subscribed']);
			    break;
		    default:
		    	$text = $this->processingCustomText($data['text']);
			    $vk->sendMass($text, $data['peer_id']);
	    }

    }

    protected function processingCustomText($text){
	    if($text{0} == "!"){
	    	$str = substr($text, 1);
		    $this->subscribe(1, $str);
	    	return 'subscribed';
	    } else {
	    	return $text;
	    }
    }

    protected function subscribe($status, $str = null){
    	$user = User::find($this->user_id);
    	if($user){
    		$user->update([
    			'subscribe_status' => $status,
    			'search_string' => $str,
		    ]);
	    } else {
//    		dd($this->user_id);
		    $user = new User([
    			'id'=> $this->user_id,
			    'subscribe_status' => $status,
			    'search_string' => $str,
		    ]);
		    $user->save();
	    }
    }

}
