<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use App\User;
use App\VkApi;

$router->get('/', function () use ($router) {
	return 'hi';
    return $router->app->version();
});
$router->post('call',"CallbackController@index");

$router->get('/cron',function () use ($router) {
	return \App\Schedule::checkSchedule();
	return $router->app->version();
});
$router->get('/bg',function () use ($router) {
	$bgs = \App\Background::all();
	foreach($bgs as $bg){
		echo "<img src='$bg->url' alt='' style='width: 50%'> <h1>$bg->id</h1>";
	}
});
$router->get('/bg_link',function () use ($router) {
	$bgs = \App\Background::whereNotNull('spo_imgs')
		->with('users')
		->get();

	foreach($bgs as $bg){
		echo "<h1>$bg->id</h1>";
		if($bg->users){
			foreach($bg->users as $key=>$user){
				echo "$key - <a href='https://vk.com/id$user->id'>https://vk.com/id$user->id</a><br>";
			}
		}
		echo "<img src='$bg->url' alt='' style='width: 50%'>";
	}
});

$router->get('/server',"ServerController@index");
$router->post('/server',"ServerController@index");
$router->post('/auth',"ServerController@auth");

$router->get('/cron_queue',function () use ($router) {
	exec('/opt/php72/bin/php /home/c/cm56270/bot/public_html/artisan queue:work --timeout=60 --tries=3  2>&1 &');
});
$router->get('/test',function () use ($router) {

//	\App\Schedule::procMesTi();
//	$bgs = \App\Schedule::getBg();
//	foreach($bgs as $bg_id =>$url){
//		new \App\Jobs\ScheduleHand($bg_id,['npo' =>false, 'spo' =>true], null);
//	}
//	dispatch(new \App\Jobs\ProcTiSchedule(['npo'=>false, 'spo'=>true]));

//	return response()->json(\App\Schedule::getBg());
//	$dh = new \App\Jobs\ProcTiSchedule();
//	$dh->handle();
//	\App\Schedule::send(6,1);
//	$resp = \App\Schedule::search('Хоробрая');
//	dd($resp);
});
