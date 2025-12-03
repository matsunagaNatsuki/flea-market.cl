@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/purchase.css') }}">
@endsection

@section('content')
<div class="image-upload">
    @if (Str::startsWith($sell->image, ['http://', 'https://']))
        <img src="{{ $sell->image }}" class="card-img-top img-fluid custom-img" alt="{{ $sell->name }}">
    @else
        <img src="{{ Storage::url($sell->image) }}" class="card-img-top img-fluid custom-img" alt="{{ $sell->name }}">
    @endif
</div>
<form action=" {{ route('purchase.buy', ['item_id' => $sell->id]) }}" method="post">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <div class="purchase-form__group">
        <h1>{{ $sell->name }}</h1>
        <p class="price">￥{{ $sell->price }}</p>

        @php
        $selectedPaymentMethod = session('payment_method', '');
        @endphp

        <p>支払い方法:
            <select name="payment_method" id="payment_method">
                <option value="" {{ $selectedPaymentMethod == '' ? 'selected' : ''}}>選択してください</option>
                <option value="konbini" {{ session('payment_method') == 'convenience_store' ? 'selected' : '' }}>コンビニ払い</option>
                <option value="card" {{ session('payment_method') == 'credit_card' ? 'selected' : ''}}>カード払い</option>
            </select>
        </p>
    </div>
    @csrf


    <div class="subtotal">
        <h3>小計</h3>
        <p>商品代金: ￥{{ $sell->price }}</p>
        <div id="pay_confirm">支払い方法</div>
    </div>

    <div class="btn">
        <button type="submit">購入する</button>
    </div>

    <div class="purchase">
        <label for="postal_code">配送先</label>

        <p>郵便番号：
            <span id="postal_code">{{ $user->profile->postal_code ?? '未登録' }}</span>
        </p>
        <p>住所：
            <span id="address">{{ $user->profile->address ?? '未登録' }}</span>
        </p>
        <p>建物名：
            <span id="building">{{ $user->profile->building ?? '未登録' }}</span>
        </p>

        <input type="hidden" name="buy_postal_code" value="{{ $user->profile->postal_code }}">
        <input type="hidden" name="buy_address" value="{{ $user->profile->address }}">
        <input type="hidden" name="buy_building" value="{{ $user->profile->building }}">
    </div>
    <a href="/purchase/address/{{ $sell->id }}">変更する</a>
</form>
<script src="{{ asset('js/purchase.js') }}"></script>