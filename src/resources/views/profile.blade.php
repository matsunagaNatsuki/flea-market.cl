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

    <div class="border">
        <ul class="tab__list">
            <li><a href="/mypage?page=sell">出品した商品</a></li>
            <li><a href="/mypage?page=buy">購入した商品</a></li>
            <li><a href="/mypage?page=trade">
                    取引中の商品
                    @if(($tradeMessageCount ?? 0) > 0)
                    <span class="count-badge">{{ $tradeMessageCount }}</span>
                    @else
                    <span class="count-badge" style="display:none;">0</span>
                    @endif
                </a>
            </li>
        </ul>
    </div>

    {{-- 出品タブ --}}
    @if ($page === 'sell')
    <div class="items">
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
    </div>
    @endif


    {{-- 購入タブ --}}
    @if ($page === 'buy')
    <div class="items">
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
    </div>
    @endif

    {{-- 取引タブ --}}
    @if ($page === 'trade')
    <div class="items">
        @foreach ($items as $trade)

        @php
        $isSeller = $trade->seller_profile_id === $profile->id;
        @endphp

        <div class="item">
            <a href="{{ $isSeller ? route('get.seller', $trade->id) : route('get.buyer', $trade->id) }}">
                <div class="item__img--container" data-trade-id="{{ $trade->id }}">
                    <img src="{{ $trade->sell->image }}" class="item__img" alt="商品画像">

                    <span class="item__badge {{ ($trade->unread_count ?? 0) > 0 ? '' : 'is-hidden' }}">
                        {{ (int) ($trade->unread_count ?? 0) }}
                    </span>
                </div>

                <p class="item__name">{{ $trade->sell->name }}</p>
            </a>
        </div>

        @endforeach
    </div>
    @endif


    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const updateTotalBadge = () => {
                return fetch('/unread-count', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        cache: 'no-store'
                    })
                    .then(r => r.json())
                    .then(data => {
                        const badge = document.querySelector('.count-badge');
                        if (!badge) return;

                        const count = Number(data.count || 0);
                        badge.textContent = count;
                        badge.style.display = count === 0 ? 'none' : 'inline-block';
                    })
                    .catch(() => {});
            };

            const updateTradeBadges = () => {
                return fetch('/unread-count/trades', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        cache: 'no-store'
                    })
                    .then(r => r.json())
                    .then(data => {
                        const counts = data.counts || {};

                        document.querySelectorAll('.item__img--container[data-trade-id]').forEach(container => {
                            const tradeId = container.dataset.tradeId;
                            const badge = container.querySelector('.item__badge');
                            if (!badge) return;

                            const count = Number(counts[tradeId] || 0);
                            badge.textContent = count;
                            badge.style.display = count === 0 ? 'none' : 'inline-block';
                        });
                    })
                    .catch(() => {});
            };

            const updateAll = () => {
                updateTotalBadge();
                updateTradeBadges();
            };

            updateAll();

            window.addEventListener('pageshow', updateAll);
            window.addEventListener('load', updateAll);
        });
    </script>
    @endsection