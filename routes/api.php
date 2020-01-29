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

$router->group(['prefix' => 'sub'], function () use ($router) {  // , 'middleware' => 'auth'

    // CLIENTS ROUTES
    $router->get('clients/', 'Client\ClientController@index');
    $router->get('clients/{client_id}/subscriptions', 'Client\ClientSubscriptionController@index');


    // PRODUCTS ROUTES
    $router->get('products/', 'Product\ProductController@index');
    $router->get('products/{id}', 'Product\ProductController@show');
    $router->get('products/{product_id}/subscriptions', 'Product\ProductSubscriptionController@index');


    // SUBSCRIPTIONS ROUTES
    $router->get('subscriptions/', 'Subscription\SubscriptionController@index');
    $router->get('subscriptions/{id}', 'Subscription\SubscriptionController@show');
    $router->post('subscriptions/', 'Subscription\SubscriptionController@store');
    $router->put('subscriptions/{id}', 'Subscription\SubscriptionController@update');
    $router->patch('subscriptions/{id}', 'Subscription\SubscriptionController@update');
    $router->delete('subscriptions/{id}', [
        'middleware' => ['auth'],
        'uses'       =>'Subscription\SubscriptionController@destroy'
    ]);
    $router->put('subscriptions/{id}/status', 'Subscription\SubscriptionController@status');

    // REPORT XLS
    $router->post('/report', 'Report\ReportController@report');

} );

