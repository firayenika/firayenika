<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Rute untuk halaman dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
});
Route::get('/notif', function () {
    return view('notification');
});

