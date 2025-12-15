@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/profile.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="profile-container">
        <div class="profile-image">
            <img id="profile_preview"
                src="{{ optional($profile)->image ? asset('storage/' . optional($profile)->image) : asset('images/cat_default_avatar.png') }}"
                alt="{{ optional($profile)->name }}">
        </div>
        <h2 class="profile-name">{{ optional($profile)->name }}</h2>
        @if(!is_null($reviewAvg))
            <div class="profile-rating">
                <span class="profile-rating__stars" aria-label="評価 {{ $reviewAvg }} / 5">
                    @for($i=1; $i<=5; $i++)
                        <span class="{{ $i <= $reviewAvg ? 'is-on' : '' }}">★</span>
                    @endfor
                </span>
            </div>
        @endif

        <div class="btn">
            <a href="mypage/profile">プロフィールを編集</a>
        </div>
    </div>

    <div class="tab__list">
        <li class="sell__item"><a href="/mypage?page=sell">出品した商品</a></li>
        <li class="buy__item"><a href="/mypage?page=buy">購入した商品</a></li>
        <li class="trade__item">
            <a href="/mypage?page=trade">
                取引中の商品 ({{ $tradeMessageCount }})
            </a>
        </li>
    </div>
    <div class="silver-line">

        {{-- 出品タブ --}}
        @if ($page === 'sell')
        @foreach ($items as $sell)
        <div class="item">
            <a href="/item/{{ $sell->id }}">
                <div class="item__img--container">
                    <img src="{{ $sell->image }}" class="item__img" alt="商品画像">
                </div>
                <p class="item__name">{{ $sell->name }}</p>
            </a>
        </div>
        @endforeach
        @endif


        {{-- 購入タブ --}}
        @if ($page === 'buy')
        @foreach ($items as $buy)
        <div class="item">
            <a href="/item/{{ $buy->sell->id }}">
                <div class="item__img--container">
                    <img src="{{ $buy->sell->image }}" class="item__img" alt="商品画像">
                </div>
                <p class="item__name">{{ $buy->sell->name }}</p>
            </a>
        </div>
        @endforeach
        @endif

        {{-- 取引タブ --}}
        @if ($page === 'trade')
        @foreach ($items as $trade)
        <div class="item">
            <a href="{{ route('get.seller', $trade->id) }}">
                <div class="item__img--container">
                    <img src="{{ $trade->sell->image }}" class="item__img" alt="商品画像">
                </div>
                <p class="item__name">{{ $trade->sell->name }}</p>
            </a>
        </div>
        @endforeach
        @endif

    </div>
    @endsection