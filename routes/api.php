<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$this->any('/', 'APIController@index');


$this->any('/login', 'APIController@login');
$this->any('/jwt/login', 'APIController@JWTLogin');
$this->any('/jwt/refresh', 'APIController@JWTRefresh');


$this->any('/device', 'APIController@listDevice');
$this->any('/device/create', 'APIController@createDevice');
$this->any('/device/delete', 'APIController@deleteDevice');
$this->any('/device/notify', 'APIController@notifyDevice');


$this->any('/user/info', 'APIController@userInfo');
$this->any('/user/avatar', 'APIController@modifyUserAvatar');
$this->any('/user/params', 'APIController@userParams');
$this->any('/user/cities', 'APIController@cities');
$this->any('/user/modify', 'APIController@modifyUserInfo');


$this->any('/notification', 'APIController@listNotification');
$this->any('/notification/{id}', 'APIController@showNotification');
$this->any('/notification/{id}/full', 'APIController@showFullNotification');

$this->any('/notification/{id}/delete', 'APIController@deleteNotification');
$this->any('/notification/{id}/restore', 'APIController@restoreNotification');

$this->any('/notification/{id}/read', 'APIController@readNotification');
$this->any('/notification/{id}/star', 'APIController@starNotification');
$this->any('/notification/{id}/unstar', 'APIController@unstarNotification');
