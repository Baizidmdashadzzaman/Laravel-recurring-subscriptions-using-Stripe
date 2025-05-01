<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StripeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();
Route::group(['middleware' => ['auth']], function() {
    Route::get('plan', [StripeController::class, 'plan'])->name('plan');
    Route::get('check-out/{id}', [StripeController::class, 'checkOut'])->name('check-out');
    Route::post('pay', [StripeController::class, 'pay'])->name('pay');

    Route::get('update-plan', [StripeController::class, 'updatePlan'])->name('update-plan');
    Route::post('update-plan', [StripeController::class, 'updatePlan'])->name('update-plan');
    Route::post('check-coupon', [StripeController::class, 'checkcoupon'])->name('check-coupon');
    Route::get('card-list', [StripeController::class, 'getCradList'])->name('card-list');
    Route::get('my-plan', [StripeController::class, 'myPlan'])->name('myPlan');
    Route::post('cancel-subscription', [StripeController::class, 'cancelSubscription'])->name('cancel-subscription');
    Route::get('logout', [StripeController::class, 'logout'])->name('logout');
    Route::get('check-out/{id}', [StripeController::class, 'checkOut'])->name('check-out');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
