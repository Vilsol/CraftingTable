<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', array('before'=>'auth', function()
{
	return View::make('index');
}));

Route::get('/register', array('before'=>'auth.guest', function()
{
	return View::make('register');
}));

Route::post('/register', array('before'=>'auth.guest', function()
{
	$user = new User;
	$user->email = Input::get('email');
	$user->username = Input::get('username');
	$user->password = Hash::make(Input::get('password'));

	$validator = Validator::make(
		array('email'=>$user->email,
			'username'=>$user->username,
			'password'=>Input::get('password')),
		array('email'=>'required|email|unique:users',
			'username'=>'required|unique:users',
			'password'=>'required')
	);

	$environment = App::environment();

	if (strcmp($environment, 'demo')) {
		return View::make('register')->with('success', 'Please login with the email demo@minestack.io and password demo');
	}

	if ($validator->fails()) {
		return View::make('register')->with('error', $validator->messages());
	} else {
		$user->save();
		$theEmail = Input::get('email');
		return View::make('register')->with('success', 'Thank you '.$theEmail.' for registering.');
	}

}));

Route::get('/login', array('before'=>'auth.guest', function()
{
	return View::make('login');
}));

Route::post('/login', array('before'=>'auth.guest', function()
{
	$email = Input::get('email');
	$password = Input::get('password');

	if (Auth::attempt(array('email'=>$email, 'password'=>$password))) {
		return Redirect::intended('/');
	}

	return View::make('login')->with('error', 'Invalid Username or password');
}));

Route::get('/logout', array('before'=>'auth', function()
{
	Auth::logout();

	return View::make('logout');
}));