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
    Route::post('/sell', [SellController::class, 'store'])->name('sells.store');
    Route::get('/sell', [SellController::class, 'sell'])->name('sells.create');
    Route::get('/purchase/{item_id}', [SellController::class,'purchase'])->name('purchase.show');
    Route::post('/purchase/{item_id}', [SellController::class, 'buy'])->name('purchase.buy');
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
    // 取引チャット画面
    Route::get('/mypage?page=trade', [ProfileController::class, 'trade']);
    Route::get('/chat', [ProfileController::class, 'chat']);
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
