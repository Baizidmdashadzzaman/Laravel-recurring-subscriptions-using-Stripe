<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubscriptionController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/subscribe', [SubscriptionController::class, 'showForm'])->name('subscribe.form');
Route::post('/subscribe', [SubscriptionController::class, 'processForm'])->name('subscribe.process');
