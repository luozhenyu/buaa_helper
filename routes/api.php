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

$this->any('/', function () {
    return 'Welcome to API of ourbuaa.com!';
});

$this->any('login', 'APIController@login');

$this->any('user/info', 'APIController@userInfo');
$this->any('user/modify', 'APIController@modifyUserInfo');

$this->any('notification', 'APIController@listNotification');
$this->any('notification/{id}', 'APIController@showNotification');

$this->any('notification/{id}/read', 'APIController@read');
$this->any('notification/{id}/star', 'APIController@star');
$this->any('notification/{id}/unstar', 'APIController@unstar');
