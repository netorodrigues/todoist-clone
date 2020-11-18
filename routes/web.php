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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group([
    'prefix' => 'api/users',
], function () use ($router) {
    $router->post('', 'UserController@create');
    $router->post('login', 'SessionController@create');

    $router->group([
        'middleware' => ['auth-jwt'],
    ], function () use ($router) {
        $router->get('', 'UserController@get');
        $router->put('', 'UserController@edit');
        $router->delete('', 'UserController@delete');
    });
});

$router->group([
    'middleware' => ['auth-jwt'],
    'prefix' => 'api/projects',
], function () use ($router) {
    $router->get('', 'ProjectController@get');
    $router->post('', 'ProjectController@create');
    $router->put('', 'ProjectController@edit');
    $router->delete('', 'ProjectController@delete');
});

$router->group([
    'middleware' => ['auth-jwt'],
    'prefix' => 'api/tasks',
], function () use ($router) {
    $router->get('', 'TaskController@get');
    $router->post('', 'TaskController@create');
    $router->put('', 'TaskController@edit');
    $router->delete('', 'TaskController@delete');
});
