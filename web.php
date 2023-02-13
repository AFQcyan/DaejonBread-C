<?php

use src\App\Route;

// 메인페이지
Route::get('/', 'ViewController@index');
Route::get('/login', 'ViewController@login');
Route::get('/logout', 'UserController@logout');

// 스토어

// 캡차 이미지

if(user()){
    // 마이페이지 -> 고객
    Route::get('/myPage', 'ViewController@myPage');
    Route::post('/insert/review', 'ReviewController@insert');
    Route::post('/insert/grade', 'ReviewController@grade');

    //마이페이지 -> 사장님
    Route::post('/accept', 'OrderController@accept');
    Route::post('/deny', 'OrderController@deny');
    // 전문가
    Route::get('/specia', 'ViewController@specia');

    // 시공 견적
    Route::get('/quote', 'ViewController@quote');
    Route::get('/quote/{idx}', 'ViewController@resp_quote');
    Route::get('/quote/update/{idx}', 'UpdateController@quote');

    Route::get('/user/logout', 'UserController@logout');
}
Route::post('/login_ok', 'UserController@login');