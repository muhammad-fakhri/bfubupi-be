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

$router->group(['prefix' => 'user'], function ($router) {
    $router->group(['prefix' => 'email'], function ($router) {
        $router->get('verify', 'AuthController@verifyEmail');
        $router->post('resend', 'AuthController@resendVerificationEmail');
    });

    $router->group(['prefix' => 'password'], function ($router) {
        $router->post('forgot', 'AuthController@requestForgotPassword');
        $router->post('reset', 'AuthController@resetPassword');
    });

    $router->post('login', 'AuthController@login');
    $router->post('register', 'AuthController@register');
});

$router->get('/link/{code}', 'LinkController@getByCode');
$router->get('/link', 'LinkController@getAll');
