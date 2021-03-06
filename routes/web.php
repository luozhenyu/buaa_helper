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
$this->get('/test', 'TestController@test');


$this->get('/', 'HomeController@viewIndex');
$this->get('/account', 'HomeController@viewAccount');
$this->post('/account/profile', 'HomeController@updateProfile');
$this->post('/account/password', 'HomeController@updatePassword');


//account_manager
$this->get('/account_manager', 'AccountManagerController@index')->name('accountManager');
$this->post('/account_manager/ajax', 'AccountManagerController@ajaxIndex');
$this->get('/account_manager/create', 'AccountManagerController@create');
$this->post('/account_manager', 'AccountManagerController@store');

$this->get('/account_manager/import', 'AccountManagerController@getImportTemplate');
$this->post('/account_manager/import', 'AccountManagerController@import');

$this->get('/account_manager/{id}', 'AccountManagerController@show');
$this->put('/account_manager/{id}', 'AccountManagerController@update');
$this->delete('/account_manager/{id}', 'AccountManagerController@destroy');

//group
$this->get('/group', 'GroupController@index')->name('group');
$this->post('/group', 'GroupController@store');
$this->put('/group/{id}', 'GroupController@update');
$this->delete('/group/{id}', 'GroupController@delete');
$this->get('/group/{id}', 'GroupController@show');
$this->post('/group/{id}/insert', 'GroupController@insert');
$this->post('/group/{id}/erase', 'GroupController@erase');

//ajax
$this->get('/city', 'CityController@index');
$this->get('/city/{code}', 'CityController@show');
$this->get('/city/{code}/parent', 'CityController@parent');
$this->get('/city/{code}/children', 'CityController@children');


//Notification
$this->get('/notification', 'NotificationController@index')->name('notification');
$this->get('/notification/published', 'NotificationController@published');
$this->get('/notification/draft', 'NotificationController@draft');
$this->get('/notification/stared', 'NotificationController@stared');

$this->get('/notification/create', 'NotificationController@create');
$this->post('/notification', 'NotificationController@store');

$this->get('/notification/{id}', 'NotificationController@show');
$this->get('/notification/{id}/preview', 'NotificationController@preview');
$this->post('/notification/{id}/publish', 'NotificationController@publish');
$this->delete('/notification/{id}', 'NotificationController@delete');
$this->get('/notification/{id}/modify', 'NotificationController@modify');
$this->put('/notification/{id}', 'NotificationController@update');

//ajax
$this->post('/notification/{id}/star', 'NotificationController@star');
$this->post('/notification/{id}/unstar', 'NotificationController@unstar');
$this->post('/notification/{id}/read', 'NotificationController@read');

$this->post('/notification/{id}/statistic', 'NotificationController@statistic');
$this->get('/notification/{id}/statistic', 'NotificationController@statisticExcel');


//Inquiry
$this->get('/inquiry', 'InquiryController@index')->name('inquiry');

$this->get('/inquiry/{department_number}', 'InquiryController@department');
$this->post('/inquiry/{department_number}', 'InquiryController@create');

$this->get('/inquiry/{department_number}/{inquiry_id}', 'InquiryController@show');
$this->post('/inquiry/{department_number}/{inquiry_id}', 'InquiryController@reply');


//Login Logout
$this->get('/login', 'Auth\LoginController@showLoginForm')->name('login');
$this->post('/login', 'Auth\LoginController@login');
$this->get('/login/cas', 'Auth\LoginController@cas');
$this->post('/logout', 'Auth\LoginController@logout')->name('logout');


// Registration Routes...
$this->get('/register', 'Auth\RegisterController@showRegistrationForm')->name('register');
$this->get('/register/{user_id}', 'Auth\RegisterController@showRegistrationForm');
$this->post('/register', 'Auth\RegisterController@register');
$this->post('/register/{user_id}', 'Auth\RegisterController@register');


// Password Reset Routes...
$this->get('/password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm');
$this->post('/password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
$this->get('/password/reset/{token}', 'Auth\ResetPasswordController@showResetForm');
$this->post('/password/reset', 'Auth\ResetPasswordController@reset')->name('password.reset');


//File upload
$this->post('/file/upload', 'FileController@upload')->name('upload');
$this->get('/file/{sha1}', 'FileController@download');
