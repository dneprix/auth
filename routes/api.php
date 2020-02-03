<?php

// Route group /api
$router->group(['prefix' => 'api'], function () use ($router) {
    // Route group /api/v1
    $router->group(['prefix' => 'v1'], function () use ($router) {
        // Route group /api/v1/user
        $router->group(['prefix' => 'user'], function () use ($router) {
            // POST /api/v1/user/register
            $router->post('register', 'UserController@register');
            // GET /api/v1/user/activate
            $router->post('activate', 'UserController@activate');
            // POST /api/v1/user/auth
            $router->post('auth', 'UserController@auth');
        });
    });
});
