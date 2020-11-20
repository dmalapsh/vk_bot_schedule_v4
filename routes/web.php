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

$router->get('/cron_queue',function () use ($router) {
	exec('/opt/php72/bin/php /home/c/cm56270/bot/public_html/artisan queue:work --timeout=60 --tries=3  2>&1 &');
});
$router->get('/test',function () use ($router) {

//	\App\Schedule::procMesTi();
	dispatch(new \App\Jobs\SendingMessagesJob);
//	dispatch(new \App\Jobs\ProcTiSchedule(['npo'=>false, 'spo'=>true]));

//	return response()->json(\App\Schedule::getBg());
//	$dh = new \App\Jobs\ProcTiSchedule();
//	$dh->handle();
//	\App\Schedule::send(6,1);
//	$resp = \App\Schedule::search('Хоробрая');
//	dd($resp);
});
