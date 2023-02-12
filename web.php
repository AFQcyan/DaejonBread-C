<?php

use src\App\Route;

// 메인페이지
Route::get('/', 'ViewController@index');
Route::get('/login', 'ViewController@login');
Route::get('/logout', 'UserController@logout');

// 스토어
Route::get('/store', 'ViewController@store');

// 캡차 이미지
Route::get('/captcha', 'ViewController@captcha');

if(user()){
    // 온라인 집들이
    Route::get('/myPage', 'ViewController@myPage');
    Route::get('/houses/list', 'ViewController@houses_list');

    // 전문가
    Route::get('/specia', 'ViewController@specia');

    // 시공 견적
    Route::get('/quote', 'ViewController@quote');
    Route::get('/quote/{idx}', 'ViewController@resp_quote');
    Route::get('/quote/update/{idx}', 'UpdateController@quote');

    Route::get('/user/logout', 'UserController@logout');
}
Route::post('/login_ok', 'UserController@login');

Route::post('/house/insert', 'InsertController@house');
Route::post('/house/review', 'InsertController@house_review');

Route::post('/review/insert', 'InsertController@review');

Route::post('/req_quote/insert', 'InsertController@req_quote');
Route::post('/resp_quote/insert', 'InsertController@resp_quote');