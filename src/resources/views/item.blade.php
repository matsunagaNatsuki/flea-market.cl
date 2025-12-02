@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/item.css') }}">
@endsection

@section('content')
<div class="item-detail__container">
    <div class="item-detail__inner">

        <div class="item-detail__image-area">
            @if ($sell->sold())
            <div class="item__img sold">
                <img src="{{ $sell->image }}" alt="{{ $sell->name }}">
            </div>
            @else
            <div class="item__img">
                <img src="{{ $sell->image }}" alt="{{ $sell->name }}">
            </div>
            @endif
        </div>

        <div class="item-detail__info-area" id="scroll__item__info">
            <div class="item-detail__header">
                <h1 class="sell__name">{{ $sell->name }}</h1>
                <p class="sell__brand">{{ $sell->brand }}</p>

                <p class="sell__price">
                    ￥ {{ number_format($sell->price) }}<span class="sell__price-tax">(税込)</span>
                </p>
            </div>

            <div class="item-detail__icon">
                <form action="{{ route('sell.like', ['item_id' => $sell->id]) }}" method="post" class="sell__like">
                    @csrf
                    <button type="submit" class="sell__like-button">
                        <span class="sell__like-icon">
                            {{ $sell->liked() ? '❤' : '♡' }}
                        </span>
                        <span class="sell__like-count">
                            {{ $sell->likes()->count() }}
                        </span>
                    </button>
                </form>
                <div class="sell__comment-count">
                    <span class="sell__comment-icon">💬</span>
                    <span class="sell__comment-number">
                        {{ $sell->comments_count ?? $sell->comments->count() }}
                    </span>
                </div>
                <a href="{{ url('/purchase/' . $sell->id) }}" class="purchase-button">購入手続きへ</a>
            </div>
            <h3 class="item__section">商品説明</h3>
            <p class="item__description">{{$sell->description}}
            <p>
            <h3 class="item__section">商品の情報</h3>
            <table class="item__table">
                <tr>
                    <th>カテゴリー</th>
                    <td>
                        <ul class="item__table-category">
                            @foreach ($sell->categories as $category)
                            <li class="category__btn">{{$category->name}}</li>
                            @endforeach
                        </ul>
                    </td>
                </tr>
                <tr>
                    <th>商品の状態</th>
                    <td class="item__table-condition">{{$sell->condition->condition_name}}</td>
                </tr>
            </table>

            <div id="comment" class="comment_section">
                <h3 id="count__title">コメント({{$sell->getComments()->count()}})</h3>
                <div class="comments" id="comments__list">
                    @foreach ($sell->comments as $comment)
                    <div class="comment">
                        <div class="comment__user">
                            <div class="user__img">
                                @php
                                    $user = $comment->user ?? null;
                                    $profile = $user ? $user->profile ?? null : null;
                                @endphp

                                <img src="{{ \Storage::url($profile->image) }}" alt="">
                            </div>
                            <p class="user_name">{{$comment->user->name}}</p>
                        </div>
                        <p class="comment__content">{{$comment->content}}</p>
                    </div>
                    @endforeach
                </div>

                <form action="{{ route('sell.comment', ['item_id' => $sell->id]) }}" method="post" class="sell__comment">
                    @csrf
                    <textarea name="content" rows="4" placeholder="こちらにコメントを入力してください">{{ old('content') }}</textarea>
                    <div class="form__error">
                        @error('content')
                        {{ $message }}
                        @enderror
                    </div>
                    <button type="submit">コメントを送信する</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection