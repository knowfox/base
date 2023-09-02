<?php

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
Auth::routes();

Route::get('/', function () {
    if (Auth::user()) {
	return redirect()->route('home');
    }
    return view('knowfox::welcome');
});

Auth::routes();

Route::get('/home', [Knowfox\Http\Controllers\HomeController::class, 'index'])->name('home');
