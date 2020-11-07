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

Route::get('/', function () {
    //return view('welcome');
    if(auth()->check()){
        return redirect('home');
    }else {
        return redirect('login');
    }
})->name('web.basepath');

Route::get('/{optional?}', function ($optional) {

    if($optional === 'login' && auth()->check()){
        return redirect('home');
    }else if($optional !== 'login' && !auth()->check()){
        return redirect('login');
    }

    return view('app');
})->where('optional', '.*');
