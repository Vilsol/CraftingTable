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

//auth index
Route::group(array('before' => 'auth'), function() {
	Route::get('/', function() {
		return View::make('index');
	});
});

//auth nodes
Route::group(array('before' => 'auth'), function() {
	Route::model('node', 'Node', function() {
	});
	Route::get('/nodes', 'NodeController@getNodes');
	Route::post('/nodes/add', 'NodeController@postNode');
	Route::put('/nodes/{node}', 'NodeController@putNode');
	Route::delete('/nodes/{node}', 'NodeController@deleteNode');
});

//auth users
Route::group(array('before' => 'auth'), function() {
	Route::model('user', 'Toddish\Verify\Models\User', function() {
	});
	Route::get('/logout', 'UserController@getLogout');
	Route::get('/users', 'UserController@getUsers');
	Route::post('/users/add', 'UserController@postUser');
	Route::get('/users/{user}/{edit?}', 'UserController@getUser');
	Route::put('/users/{user}/{edit?}', 'UserController@putUser');
	Route::delete('/users/{user}', 'UserController@deleteUser');
});

//auth groups
Route::group(array('before' => 'auth'), function() {
	Route::model('group', 'Toddish\Verify\Models\Role', function() {
	});
	Route::get('/groups', 'GroupController@getGroups');
	Route::post('/groups/add', 'GroupController@postGroup');
	Route::put('/groups/{group}', 'GroupController@putGroup');
	Route::delete('/groups/{group}', 'GroupController@deleteGroup');
});

//auth networks
Route::group(array('before' => 'auth'), function() {
	Route::model('network', 'Network', function() {
	});
	Route::post('/networks/add', array('uses' => 'NetworkController@postNetwork'));
	Route::put('/networks/{network}', array('uses' => 'NetworkController@putNetwork'));
	Route::delete('/networks/{network}', array('uses' => 'NetworkController@deleteNetwork'));
});

//auth plugins
Route::group(array('before' => 'auth'), function() {
	Route::model('plugin', 'Plugin', function() {
	});
	Route::model('version', 'PluginVersion', function() {
	});
	Route::get('/plugins', 'PluginController@getPlugins');
	Route::post('/plugins/add', array('uses' => 'PluginController@postPlugin'));
	Route::put('/plugins/{plugin}', array('uses' => 'PluginController@putPlugin'));
	Route::delete('/plugins/{plugin}', array('uses' => 'PluginController@deletePlugin'));
	Route::post('/plugins/{plugin}/versions/add', array('uses' => 'PluginController@postVersion'));
	Route::delete('/plugins/{plugin}/versions/{version}', array('uses' => 'PluginController@deleteVersion'));
});

//no auth
Route::group(array('before' => 'auth.guest'), function() {
	Route::get('/register', 'UserController@getRegister');
	Route::post('/register', 'UserController@postRegister');
	Route::get('/login', 'UserController@getLogin');
	Route::post('/login', 'UserController@postLogin');
});

