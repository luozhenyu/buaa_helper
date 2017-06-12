<?php

$this->get('auth', 'MobileController@auth');


$this->get('register/{number?}', 'MobileController@showRegistrationForm');
$this->post('register/{number?}', 'MobileController@register');

$this->get('account', 'MobileController@account');
$this->get('account/profile', 'MobileController@showProfileForm');
$this->post('account/profile', 'MobileController@updateProfile');
$this->get('account/password', 'MobileController@showPasswordForm');
$this->post('account/password', 'MobileController@updatePassword');


$this->get('inquiry', 'MobileController@inquiryIndex');
$this->get('inquiry/create', 'MobileController@inquiryCreate');
$this->post('inquiry', 'MobileController@inquiryStore');

$this->get('inquiry/{id}', 'MobileController@inquiryShow');

