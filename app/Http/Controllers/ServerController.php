<?php


namespace App\Http\Controllers;


use App\VkApi;
use Illuminate\Http\Request;

class ServerController {
	public function index(){

		$pin = random_int(0,9999);
		$pin = sprintf("%'.04d", $pin);
//		echo $pin;
		header('Content-Type: text/plain;'); //Мы будем выводить простой текст
		set_time_limit(0); //Скрипт должен работать постоянно
		ob_implicit_flush(); //Все echo должны сразу же выводиться
		$address = '91.202.196.145'; //Адрес работы сервера
		$port = 9090; //Порт работы сервера (лучше какой-нибудь редкоиспользуемый)
		if (($socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) < 0) {
			echo "Ошибка создания сокета";
		}
		else {
//			echo "Сокет создан\n";
		}
		$result = socket_connect($socket, $address, $port);
		if ($result === false) {
			echo "Ошибка при подключении к сокету";
		} else {
//			echo "Подключение к сокету прошло успешно\n";
		}
		socket_write($socket, $pin, strlen($pin)); //Отправляем серверу сообщение
		$out = socket_read($socket, 1024); //Читаем сообщение от сервера
//		echo "Сообщение от сервера: $out.\n"; //Выводим сообщение от сервера
		$vk = new VkApi();

//		$vk->info("ok");
		return response($pin);
	}
	public function auth(Request $request){
		return 0;
	}
}