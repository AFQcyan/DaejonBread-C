<?php

use src\App\Route;

// 메인페이지 / 헤더
Route::get('/', 'ViewController@index');
Route::get('/login', 'ViewController@login');
Route::get('/logout', 'UserController@logout');

//대전빵집 페이지
Route::get('/sub', 'ViewController@sub');
Route::post('/sub_search', 'BreadListController@search');

if(user()){
    // 마이페이지 -> 고객
    Route::get('/myPage', 'ViewController@myPage');
    Route::post('/insert/review', 'ReviewController@insert');
    Route::post('/insert/grade', 'ReviewController@grade');

    //마이페이지 -> 사장님
    Route::post('/accept', 'OrderController@accept');
    Route::post('/deny', 'OrderController@deny');
    Route::post('/discount', 'StoreController@dcUpdt');
    //마이페이지 -> 라이더
    Route::post('/update/trans', 'RiderController@infoInit');
    Route::post('/update/accept', 'RiderController@taking');
    Route::post('/update/complete', 'RiderController@complete');
    // 대전 빵집 페이지
    // 주문페이지
    Route::post('/order', 'ViewController@order');
    Route::post('/like' ,'ReviewController@like');
    Route::get('/user/logout', 'UserController@logout');
    Route::post('/order/order', 'OrderController@order');
    Route::post('/order/reserve', 'ReserveController@insert');

    //할인 이벤트
    Route::get('/discount', 'ViewController@discount');
}
Route::post('/login_ok', 'UserController@login');