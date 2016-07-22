<?php

Route::get('home', 'HomeController@index');

//auth
Route::get('/auth/logout','PhishingController@logout');
Route::get('/auth/login',function() {
	\Session::forget('loginRedirect'); //causes it to go to 500.blade but does log in
	$varToPass = array('errors'=>array());
	return view("auth.loginTest")->with($varToPass);
});
Route::post('/auth/login','PhishingController@postLogin');
Route::get('/auth/register',function() {
	return view("auth.regTest");
});
Route::post('/auth/register','PhishingController@postRegister');
Route::get('/auth/check','PhishingController@isUserAuth');


//form - csrf filter
Route::filter('csrf', function() {
	$token = Request::ajax() ? Request::header('X-CSRF-Token') : Input::get('_token');
	if (Session::token() != $token) {
		throw new \Illuminate\Session\TokenMismatchException;
	}
});


//controller routes
//email response control
Route::get('/breachReset','PhishingController@breachReset');
Route::get('/breachReset/verifyUser', 'PhishingController@breachVerify');

//json data
Route::get('/websitedata/json','PhishingController@postWebsiteJson');
Route::get('/emaildata/json','PhishingController@postEmailJson');
Route::get('/reportsdata/json','PhishingController@postReportsJson');

//webbug - update accordingly to be effective
Route::get('/email/images/{id}.png','PhishingController@webbugRedirect');
Route::get('/{id}','PhishingController@webbugRedirect');

//Send Email Nav - Start Project
Route::get('/email/generate','PhishingController@generateEmailForm');
Route::post('/email/generate','PhishingController@sendEmail');

//Send Email Nav - Settings
Route::post('/email/settings','PhishingController@updateDefaultEmailSettings');
Route::get('/email/settings','PhishingController@generateDefaultEmailSettingsForm');

//Templates Nav - New Template
Route::post('/templates/create','PhishingController@createNewTemplate');
Route::get('/templates/create',function() {
	if(\Session::get('authUserId')) {
		return view("forms.createNewTemplate");
	} else {
		\Session::put('loginRedirect',$_SERVER['REQUEST_URI']);
		return view('auth.loginTest');
	}
});

//Template HTML Returner
Route::get('/files/templates/{id}','PhishingController@htmlReturner');

//Projects Nav - New Project
Route::get('/projects/create',function() {
	if(\Session::get('authUserId')) {
		return view('forms.createNewProject');
	} else {
		\Session::put('loginRedirect',$_SERVER['REQUEST_URI']);
		return view('auth.loginTest');
	}
});
Route::post('/projects/create','PhishingController@createNewProject');

//Templates Nav - View Templates
Route::get('/templates/show','PhishingController@viewAllTemplates');
//Projects Nav - View Projects
Route::get('/projects/show','PhishingController@viewAllProjects');
//Results Nav
Route::get('',function() {
	if(\Session::get('authUserId')) {
		return view('displays.showReports');
	} else {
		\Session::put('loginRedirect',$_SERVER['REQUEST_URI']);
		return view('auth.loginTest');
	}
});