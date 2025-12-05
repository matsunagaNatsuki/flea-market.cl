<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SellController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LoginController;

Route::get('/',[SellController::class, 'index'])->name('items.list');
Route::get('/?tab=myList', [SellController::class, 'myList']);
Route::get('/item/{item_id}', [SellController::class, 'item']);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/purchase/{item_id}', [SellController::class,'purchase'])->name('purchase.show');
    Route::post('/purchase/{item_id}', [SellController::class, 'buy'])->name('purchase.buy');
    Route::get('/purchase/{item_id}/success', [SellController::class, 'success'])->name('purchase.success');
    Route::get('/purchase/address/{item_id}', [SellController::class,'address'])->name('purchase.address');
    Route::post('/purchase/address/{item_id}', [SellController::class,'UpdateAddress'])->name('purchase.address.update');
    Route::get('/sell', [SellController::class, 'sell']);
    Route::post('/sell', [SellController::class, 'store']);

    Route::post('/sell/{item_id}/like', [SellController::class, 'like'])->middleware('auth')->name('sell.like');
    Route::post('sell/{item_id}/comment', [SellController::class, 'comment'])->middleware('auth')->name('sell.comment');
    Route::get('/mypage', [ProfileController::class, 'mypage']);
    Route::get('/mypage/profile', [ProfileController::class, 'showProfile']);
    Route::post('/mypage/profile', [ProfileController::class, 'editProfile']);
    Route::get('/mypage?tab=buy', [ProfileController::class, 'buyList']);
    Route::get('/mypage?tab=sell', [ProfileController::class, 'sellList']);
});

Route::post('/login', [LoginController::class, 'store']);