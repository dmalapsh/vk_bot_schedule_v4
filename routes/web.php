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

$router->get('/test',function () use ($router) {
	\App\Schedule::send(6,1);
	$resp = \App\Schedule::search('Хоробрая');
	dd($resp);
});
