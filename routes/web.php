<?php

use App\Http\Controllers\MalWatchlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

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
Route::get('/auth/oauth', [MalWatchlist::class, 'codeGrant']);
Route::get('/auth/expired_tokens', [MalWatchlist::class, 'refreshToken']);
Route::get('/user', function (Request $request) {
    $user_id = $request->user_id;
    return App::call('App\Http\Controllers\MalWatchlist@getWatchlist', ['user_id' => $user_id]);
});
