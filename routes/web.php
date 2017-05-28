<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//$this->get('/test', 'TestController@test');


$this->get('/', 'HomeController@viewIndex');
$this->get('/account', 'HomeController@viewAccount');
$this->post('/account/profile', 'HomeController@updateProfile');
$this->post('/account/password', 'HomeController@updatePassword');


//account_manager
$this->get('/account_manager', 'AccountManagerController@index')->name('accountManager');
$this->get('/account_manager/create', 'AccountManagerController@create');
$this->post('/account_manager', 'AccountManagerController@store');

$this->get('/account_manager/import', 'AccountManagerController@getImportTemplate');
$this->post('/account_manager/import', 'AccountManagerController@import');

$this->get('/account_manager/{id}', 'AccountManagerController@show');
$this->put('/account_manager/{id}', 'AccountManagerController@update');
$this->delete('/account_manager/{id}', 'AccountManagerController@destroy');


//Notification
$this->get('/notification', 'NotificationController@index')->name('notification');
$this->get('/notification/manage', 'NotificationController@manage');
$this->get('/notification/stared', 'NotificationController@stared');

$this->get('/notification/create', 'NotificationController@create');
$this->post('/notification', 'NotificationController@store');

//$this->post('/notification/search_user', 'NotificationController@ajaxSearchUser'); //获得可推送的用户列表

$this->get('/notification/{id}', 'NotificationController@show');
$this->delete('/notification/{id}', 'NotificationController@delete');
$this->get('/notification/{id}/modify', 'NotificationController@modify');
$this->put('/notification/{id}', 'NotificationController@update');

//$this->get('/notification/{id}/push', 'NotificationController@selectPush');
//$this->post('/notification/{id}/push', 'NotificationController@push');

$this->post('/notification/{id}/star', 'NotificationController@star');
$this->post('/notification/{id}/unstar', 'NotificationController@unstar');
$this->post('/notification/{id}/read', 'NotificationController@read');

$this->post('/notification/{id}/statistic', 'NotificationController@statistic');
$this->get('/notification/{id}/statistic', 'NotificationController@statisticExcel');

//Inquiry
$this->get('/inquiry', 'InquiryController@index')->name('inquiry');
//$this->get('/inquiry/{id}', 'InquiryController@show');
//$this->post('/inquiry/{id}/update', 'InquiryController@update');
//$this->post('/inquiry/{id}/delete', 'InquiryController@delete');

//Login Logout
$this->get('login', 'Auth\LoginController@showLoginForm')->name('login');
$this->post('login', 'Auth\LoginController@login');
$this->post('logout', 'Auth\LoginController@logout');

// Registration Routes...
$this->get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
$this->get('register/{user_id}', 'Auth\RegisterController@showRegistrationForm');
$this->post('register', 'Auth\RegisterController@register');
$this->post('register/{user_id}', 'Auth\RegisterController@register');

// Password Reset Routes...
$this->get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm');
$this->post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
$this->get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm');
$this->post('password/reset', 'Auth\ResetPasswordController@reset');

//File upload
$this->post('file/upload', 'FileController@upload');
$this->get('file/download/{sha1}', 'FileController@download');