<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;

class DeployController extends Controller
{

	public function index(){
		//берем файлы из гита
		$result = console_run("cd /var/www/bot && git pull");
		echo "<br>";
 		//перезапускаем очереди
		console_run("supervisorctl restart laravel-worker");
		//пишем что задиплоили
		Artisan::call("send:mes deploy");
	}
}
