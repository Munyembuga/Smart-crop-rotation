<?php

use App\Models\Farm;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FarmController;
use App\Http\Controllers\UserController;


Route::get('/', function () {
    // $farms = Farm::all();
    return view('home');
});

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/register', function () {
    return view('register');
})->name('register');

Route::post('/register', [UserController::class,'register']);

Route::post('/logout', [UserController::class, 'logout']);

Route::post('/login', [UserController::class, 'login']);


//Routes for farm management
Route::post('/addfarm', [FarmController::class, 'addFarm']);

Route::get('/addfarm', function () {
    return view('addfarm');
})->name('addfarm');

Route::get('/farm', function () {
    $farms = Farm::all();
    return view('farm', ['farms'=> $farms]);
})->name('farm');
 
//Routes for Device management
Route::get('/userdevice', function () {
    return view('userdevice');
})->name('userdevice');

Route::get('/backtohome', function () {
    return view('home');
})->name('backtohome');