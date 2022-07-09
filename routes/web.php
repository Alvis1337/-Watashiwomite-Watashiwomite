<?php

use App\Http\Controllers\MalWatchlist;
use App\Http\Controllers\Sonarr;
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
Route::get('/sonarr/system', [Sonarr::class, 'getSystemStatus']);
Route::get('/grab-and-add', [Sonarr::class, 'grabAndAdd']);

Route::get('/user', function (Request $request) {
    $user_id = $request->user_id;
    return App::call('App\Http\Controllers\MalWatchlist@getWatchlist', ['user_id' => $user_id]);
});

//http://192.168.5.59:8989/api/series/lookup?term=The%20Blacklist&apikey=48e9ab7124c04a4092f803bdd78916f2
Route::get('/series-lookup', function (Request $request) {
    $anime = $request->anime;
    return App::call('App\Http\Controllers\Sonarr@seriesLookup', ['anime' => $anime]);
});
