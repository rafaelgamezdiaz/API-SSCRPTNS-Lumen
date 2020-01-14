<?php

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

$router->group(['prefix' => 'ct'], function () use ($router) {

    // CLIENTS ROUTES
    $router->get('clients/', 'Client\ClientController@index');

    // PRODUCTS ROUTES
    $router->get('products/', 'Product\ProductController@index');

    // CONTRACT ROUTES
    $router->get('subscriptions/', 'Subscription\SubscriptionController@index');
    $router->post('subscriptions/', 'Subscription\SubscriptionController@store');
    $router->put('subscriptions/{id}', 'Subscription\SubscriptionController@update');
    $router->patch('subscriptions/{id}', 'Subscription\SubscriptionController@update');
    $router->delete('subscriptions/{id}', 'Subscription\SubscriptionController@destroy');
} );