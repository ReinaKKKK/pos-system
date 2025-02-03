<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ResponseController;

// Welcomeページ
Route::get('/', function () {
    return view('welcome');
});

// イベント作成
Route::post('/events', [EventController::class, 'store']);
Route::get('/events/{id}', [EventController::class, 'show']);

// 参加者
Route::post('/users', [UserController::class, 'store']);

// 回答（○×△）
Route::post('/responses', [ResponseController::class, 'store']);

// イベント一覧ページのルート
Route::get('/eventarrange', [EventController::class, 'index']);
