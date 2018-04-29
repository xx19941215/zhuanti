<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/shop/{shop_id}/participate', 'Api\ShopController@participate')->middleware('web');
Route::post('/login', 'Api\LoginController@login')->middleware('web');
Route::post('/goods/{id}/vote', 'Api\VoteController@postVoteGoods')->middleware('web');
Route::get('/goods/search', 'Api\GoodsController@search')->middleware('web');
Route::get('/goods/baokuan/search', 'Api\GoodsController@baokuanSearch')->middleware('web');
Route::get('/stats', 'Api\StatsController@index')->middleware('web');
Route::get('/goods/competition/all', 'Api\GoodsController@all')->middleware('web');
Route::get('/goods/competition/new', 'Api\GoodsController@new')->middleware('web');
Route::get('/goods/competition/hot', 'Api\GoodsController@hot')->middleware('web');
Route::get('/shop/{shop_id}/goods', 'Api\ShopController@listGoods')->middleware('web');
Route::get('/upToken', 'Api\UpTokenController@show')->middleware('web');
