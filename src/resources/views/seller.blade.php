@extends('layouts.auth')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/seller.css') }}">
@endsection

@section('title', '出品者用チャット画面')

@section('content')
<div class="container">
    <!-- 購入者側が取引完了を行うと出品者側に評価モーダルが表示されます -->
    <dialog id="sellerReviewModal" class="review-modal">
        <form method="POST" action="{{ route('seller.review', $trade->id) }}" class="review-modal__inner">
            @csrf
            <div class="review-modal__header">
                <h3 class="review-modal__title">取引が完了しました。</h3>
            </div>

            <div class="review-modal__body">
                <p class="review-modal__subtitle">今回の取引相手はどうでしたか？</p>

                <div class="review-stars" role="radiogroup" aria-label="評価">
                    <input type="hidden" name="score" id="sellerReviewScore" value="{{ old('score', '') }}">

                    @for($i=1; $i<=5; $i++)
                        <button type="button" class="review-star" data-value="{{ $i }}" aria-label="{{ $i }}点" aria-pressed="false">★</button>
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
    <div class="buyer-profile">
        <div class="profile-image">
            <img id="profile_preview"
                src="{{ optional($trade->buyerProfile)->image ? asset('storage/' . optional($trade->buyerProfile)->image) : asset('images/cat_default_avatar.png') }}"
                alt="{{ optional($trade->buyerProfile)->name }}">
        </div>
        <h1>「{{ $trade->buyerProfile->name }}」 さんとの取引画面</h1>
    </div>

    <div class="border"></div>

    <div class="product-box">
        @if (Str::startsWith($sell->image, ['http://', 'https://']))
        <img src="{{ $sell->image }}" class="card-img-top img-fluid custom-img" alt="{{ $sell->name }}">
        @else
        <img src="{{ Storage::url($trade->sell->image) }}" class="card-img-top img-fluid custom-img" alt="{{ $trade->sell->name }}">
        @endif
        <div class="product-info">
            <h3>{{ $trade->sell->name }}</h3>
            <p>¥{{ number_format($trade->sell->price) }}</p>
        </div>
    </div>

    <div class="border"></div>

    <div class="chat-box">
        @foreach($trade->messages as $message)
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
                <p class="message__bubble {{ mb_strlen($message->body) <= 4 ? 'short-message' : '' }}">
                    {{ $message->body }}
                </p>


                @if($message->image)
                <img class="message__img" src="{{ asset('storage/' . $message->image) }}" alt="添付画像">
                @endif

                @if($isMe)
                <div class="message__actions">
                    <form action="{{ route('chat.destroy', $message->id) }}" method="POST" class="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-delete btn-sm">削除</button>
                    </form>
                    <button type="button" class="btn btn-primary btn-sm edit-toggle">編集</button>
                    <form action="{{ route('chat.update', $message->id) }}" method="POST" class="edit-form" style="display:none;">
                        @csrf
                        @method('PUT')
                        <textarea name="body" rows="3" class="form-control">{{ old('body', $message->body) }}</textarea>
                        <button type="submit" class="btn btn-primary mt-2">更新</button>
                        <button type="button" class="btn cancel-edit">キャンセル</button>
                    </form>
                </div>
                @endif
            </div>
        </div>
        @endforeach

        <form action="{{ url('/chat/seller/' . $trade->id) }}" method="POST" enctype="multipart/form-data" class="message-form" novalidate>
            @csrf
            <div class="message-input">
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
            </div>


            <input type="file" id="image" name="image" style="display:none;">
            <label for="image" class="image-btn">画像を追加</label>



            <button type="submit" class="btn btn-primary mt-2">
                <img src="http://localhost/images/paper-airplane.png" alt="送信" width="35" height="35">
            </button>
        </form>
        <script src="{{ asset('js/chat.js') }}"></script>
    </div>

    <!-- サイドバー -->
    <div class="trade-sidebar">
        <aside class="trade-sidebar">
            <h3 class="trade-sidebar__title">その他の取引
                @foreach($sidebarTrades as $trades)
                <a href="{{ route('get.seller', $trades->id) }}" class="trade-sidebar__item {{ $trades->id === $trade->id ? 'is-active' : '' }}">

                    <div class="trade-sidebar__info">
                        <p class="trade-sidebar__name">{{ $trades->sell->name }}</p>
                    </div>
                </a>
                @endforeach
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.edit-toggle').forEach(function(button) {
                button.addEventListener('click', function() {
                    const form = this.parentElement.querySelector('.edit-form');
                    form.style.display = 'block';
                    this.style.display = 'none';
                });
            });
            document.querySelectorAll('.cancel-edit').forEach(function(button) {
                button.addEventListener('click', function() {
                    const form = this.closest('.edit-form');
                    const editButton = form.parentElement.querySelector('.edit-toggle');
                    form.style.display = 'none';
                    editButton.style.display = 'inline-block';
                });
            });
        });
    </script>


    @endsection