@extends('layouts.auth')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/buyer.css') }}">
@endsection

@section('title', '購入者用チャット画面')

@section('content')
<div class="container">

    <!-- 取引開始ボタンを押下するとメールが送信され評価モーダルが表示されます -->
    <dialog id="reviewModal" class="review-modal">
        <form method="POST" action="{{ route('buyer.review', $trade->id) }}" class="review-modal__inner">
            @csrf
            <div class="review-modal__header">
                <h3 class="review-modal__title">取引を完了しました。</h3>
            </div>

            <div class="review-modal__body">
                <p class="review-modal__subtitle">今回の取引相手はどうでしたか？</p>

                <div class="review-stars" role="radiogroup" aria-label="評価">
                    <input type="hidden" name="score" id="reviewScore" value="{{ old('score', '') }}">

                    @for($i=1; $i<=5; $i++)
                        <button type="button" class="review-star"
                        data-value="{{ $i }}" aria-label="{{ $i }}点" aria-pressed="false">★</button>
                        @endfor
                </div>
            </div>

            <div class="review-modal__footer">

                <button type="submit" class="review-modal__submit">
                    送信する
                </button>
            </div>
        </form>
    </dialog>
    <div class="seller-profile">
        <div class="profile-image">
            <img id="profile_preview"
                src="{{ optional($trade->buyerProfile)->image ? asset('storage/' . optional($trade->buyerProfile)->image) : asset('images/cat_default_avatar.png') }}"
                alt="{{ optional($trade->buyerProfile)->name }}">
        </div>
        <h1>「{{ $trade->sellerProfile->name }}」 さんとの取引画面</h1>
        <button type="button" class="trade-finish-btn"
            onclick="document.getElementById('reviewModal').showModal()">
            取引を完了する
        </button>
    </div>

    <div class="border"></div>

    <div class="product-box">
        @if (Str::startsWith($sell->image, ['http://', 'https://']))
        <img src="{{ $sell->image }}" class="card-img-top img-fluid custom-img" alt="{{ $sell->name }}">
        @else
        <img src="{{ Storage::url($trade->sell->image) }}" class="card-img-top img-fluid custom-img" alt="{{ $trade->sell->name }}">
        @endif
        <h3>{{ $trade->sell->name }}</h3>
        <p>価格：¥{{ number_format($trade->sell->price) }}</p>
    </div>

    <div class="border"></div>

    <div class="chat-box">
        @foreach($trade->messages->sortBy('created_at') as $message)
        @php
        $isMe = ($message->user_id === auth()->id());
        @endphp

        <div class="message {{ $isMe ? 'message--me' : 'message--other' }}">
            <div class="message__meta">
                <div class="profile-image">
                    <img src="{{ optional($message->user->profile)->image ? asset('storage/' . optional($message->user->profile)->image) : asset('images/cat_default_avatar.png') }}"
                        alt="{{ optional($message->user->profile)->name }}">
                </div>
                <strong class="message__name">{{ $message->user->profile->name }}</strong>
            </div>

            <div class="message__content">
                <p class="message__bubble">{{ $message->body }}</p>

                @if($message->image)
                <img class="message__img" src="{{ asset('storage/' . $message->image) }}" alt="添付画像">
                @endif
                <small class="message__time">{{ $message->created_at->format('Y/m/d H:i') }}</small>

                @if($isMe)
                <div class="message__actions">
                    <form action="{{ route('chat.destroy', $message->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">削除</button>
                    </form>
                    <form action="{{ route('chat.update', $message->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <textarea name="body" rows="3" class="form-control">{{ old('body', $message->body) }}</textarea>
                        <button type="submit" class="btn btn-primary mt-2">更新</button>
                    </form>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        <form action="{{ url('/chat/buyer/' . $trade->id) }}" method="POST" enctype="multipart/form-data" class="message-form" novalidate>
            @csrf
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <textarea id="chat-body" name="body" rows="3" data-trade-id="{{ $trade->id }}" data-user-id="{{ auth()->id() }}" class="form-control" placeholder="取引メッセージを入力してください">{{ old('body','') }}</textarea>


            <input type="file" id="image" name="image" style="display:none;">
            <label for="image" class="image-btn">画像を追加</label>

            <button type="submit" class="btn btn-primary mt-2">
                <i class="fas fa-paper-plane"></i>送信</button>
        </form>
        <script src="{{ asset('js/chat.js') }}"></script>
    </div>

    <!-- サイドバー -->
    <div class="trade-sidebar">
        <aside class="trade-sidebar">
            <h3 class="trade-sidebar__title">その他の取引
        </aside>
    </div>

    @if(!empty($shouldOpenCompleteModal) && $shouldOpenCompleteModal)
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const d = document.getElementById('sellerReviewModal');
            if (d) d.showModal();
            initReviewStars('sellerReviewModal', 'sellerReviewScore');
        });
    </script>
    @endif


    @endsection