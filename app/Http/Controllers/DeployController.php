<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;

class DeployController extends Controller
{

	public function index(){
		//берем файлы
		console_run("git pull");
//		//перезапускаем очереди
		console_run("supervisorctl restart laravel-worker");

		Artisan::call("send:mes deploy");
	}
}
