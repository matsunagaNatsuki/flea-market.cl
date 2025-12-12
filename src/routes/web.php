<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\SellController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LoginController;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

Route::get('/',[SellController::class, 'index'])->name('items.list');
Route::get('/item/{item_id}', [SellController::class, 'item']);

Route::middleware(['auth', 'verified',])->group(function () {
    Route::delete('/chat/{message}', [ProfileController::class, 'destroy'])->name('chat.destroy');
    Route::post('/sell', [SellController::class, 'store'])->name('sells.store');
    Route::get('/sell', [SellController::class, 'sell'])->name('sells.create');
    Route::get('/purchase/{item_id}', [SellController::class,'purchase'])->name('purchase.show');
    Route::post('/purchase/{item_id}', [SellController::class, 'buy'])->name('purchase.buy');
    // 購入者が取引開始
    Route::post('/purchase/{sellId}/start', [ProfileController::class, 'startTrade'])->name('trade.start');
    Route::get('/purchase/{item_id}/success', [SellController::class, 'success'])->name('purchase.success');
    Route::get('/purchase/address/{item_id}', [SellController::class,'address'])->name('purchase.address');
    Route::post('/purchase/address/{item_id}', [SellController::class,'UpdateAddress'])->name('purchase.address.update');
    Route::post('/sell/{item_id}/like', [SellController::class, 'like'])->middleware('auth')->name('sell.like');
    Route::post('sell/{item_id}/comment', [SellController::class, 'comment'])->middleware('auth')->name('sell.comment');

    Route::get('/mypage', [ProfileController::class, 'mypage']);
    Route::get('/mypage/profile', [ProfileController::class, 'showProfile']);
    Route::post('/mypage/profile', [ProfileController::class, 'editProfile']);
    Route::get('/mypage?tab=buy', [ProfileController::class, 'buyList']);
    Route::get('/mypage?tab=sell', [ProfileController::class, 'sellList']);
    Route::get('/mypage?page=trade', [ProfileController::class, 'trade']);
    // 取引チャット(出品者)
    Route::get('/chat/seller/{tradeId}', [ProfileController::class, 'getSeller'])->name('get.seller');
    Route::post('/chat/seller/{tradeId}', [ProfileController::class, 'postSeller'])->name('post.seller');
    // 取引チャット（購入者）
    Route::get('/chat/buyer/{tradeId}', [ProfileController::class, 'getBuyer'])->name('get.buyer');
    Route::post('/chat/buyer/{tradeId}', [ProfileController::class, 'postBuyer'])->name('post.buyer');
});

Route::post('/login', [LoginController::class, 'store']);

Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('email');

Route::get('/email/verify', function () {
    return view('auth.verify');
})->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request){
    $request->fulfill();
    return redirect('/mypage/profile');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function () {
    request()->user()->sendEmailVerificationNotification();
    session()->put('resent', true);
    return back()->with('message', '確認メールを送信しました！');
})->middleware(['auth'])->name('verification.send');
