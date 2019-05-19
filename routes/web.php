<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
//用户注册
Route::post('userindex','UserPortController@index');

//登录
Route::post('useradd','UserPortController@useradd');

Route::post('goodslist','GoodsController@goodsList');

Route::post('partigoods','GoodsController@partiGoods');


Route::post('addcart','GoodsController@addcart');

Route::post('cartlist','GoodsController@cartlist');

Route::post('cartdd','GoodsController@cartdd');

Route::post('orderlist','GoodsController@orderlist');



//阿里
Route::get('pay','AlipayController@pay');

Route::post('notify','AlipayController@notify');

Route::get('aliReturn','AlipayController@aliReturn');

//作废///////////////////////////////////////////////
Route::post('alipays','GoodsController@alipays');////
                                                 ///
Route::post('jk','GoodsController@jk');          ///
///////////////////////////////////////////////////


