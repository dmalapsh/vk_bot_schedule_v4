<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;

class DeployController extends Controller
{

	public function index(){
		//берем файлы
		console_run("ls");
		echo "<br>";
		console_run("cd /var/www/bot && git pull");
		echo "<br>";
//		//перезапускаем очереди
		console_run("supervisorctl restart laravel-worker");

		Artisan::call("send:mes deploy");
	}
}
