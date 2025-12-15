@extends('layouts.auth')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/seller.css') }}">
@endsection

@section('title', '出品者用チャット画面')

@section('content')
<div class="container">
    <h2>{{ $trade->buyerProfile->name }} さんの取引画面</h2>

    <div class="product-box">
        @if (Str::startsWith($sell->image, ['http://', 'https://']))
        <img src="{{ $sell->image }}" class="card-img-top img-fluid custom-img" alt="{{ $sell->name }}">
        @else
        <img src="{{ Storage::url($trade->sell->image) }}" class="card-img-top img-fluid custom-img" alt="{{ $trade->sell->name }}">
        @endif
        <h3>{{ $trade->sell->name }}</h3>
        <p>価格：¥{{ number_format($trade->sell->price) }}</p>
    </div>

    <div class="chat-box">
        @foreach($trade->messages as $message)
        <div class="message">
            <strong>{{ $message->user->name }}</strong>
            <p>{{ $message->body }}</p>
            <div class="profile-image">
                <img src="{{ optional($message->user->profile)->image ? asset('storage/' . optional($message->user->profile)->image) : asset('images/cat_default_avatar.png') }}"
                    alt="{{ optional($message->user->profile)->name }}">
            </div>
            @if($message->image)
            <img src="{{ asset('storage/' . $message->image) }}" alt="添付画像" width="150">
            @endif
            <small>{{ $message->created_at->format('Y/m/d H:i') }}</small>
            <form action="{{ route('chat.destroy', $message->id) }}" method="POST" style="display:inline;">
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
        @endforeach
    </div>

    <form action="{{ url('/chat/seller/' . $trade->id) }}" method="POST" enctype="multipart/form-data" class="message-form" novalidate>
        @csrf
        <label for="body">取引メッセージを記入してください</label>
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <textarea id="chat-body" name="body" rows="3" data-trade-id="{{ $trade->id }}" data-user-id="{{ auth()->id() }}" class="form-control">{{ old('body','') }}</textarea>

        <label for="image">画像を追加</label>
        <input type="file" name="image" class="form-control">

        <button type="submit" class="btn btn-primary mt-2">
            <i class="fas fa-paper-plane"></i>送信</button>
    </form>
    <script src="{{ asset('js/chat.js') }}"></script>
</div>

<div class="trade-sidebar">
    <aside class="trade-sidebar">
        <h3 class="trade-sidebar__title">他の取引中の商品</h3>

        @foreach($sidebarTrades as $trades)
        <a href="{{ route('get.seller', $trades->id) }}" class="trade-sidebar__item" {{ $trades->id === $trade->id ? 'is-active' : '' }}">

            <div class="trade-sidebar__info">
                <p class="trade-sidebar__name">{{ $trades->sell->name }}</p>
            </div>
        </a>
        @endforeach
        <div class="sidebar-message">
            @if(($trades->messages_count ?? 0) > 0)
            < class="badge badge-message">{{ $trades->messages_count }}件</
                @endif
                </div>
    </aside>
</div>
<!-- 評価 -->
<dialog id="sellerReviewModal" class="review-modal">
    <form method="POST" action="{{ route('seller.review', $trade->id) }}" class="review-modal__inner">
        @csrf
        <div class="review-modal__header">
            <h3 class="review-modal__title">取引が完了しました。</h3>
        </div>

        <div class="review-modal__body">
            <p class="review-modal__subtitle">今回の取引相手はどうでしたか？</p>

            <div class="review-stars" role="radiogroup" aria-label="評価">
                <input type="hidden" name="score" id="reviewScore" value="{{ old('score', '') }}">

                @for($i=1; $i<=5; $i++)
                    <button type="button" class="review-star" data-value="{{ $i }}" aria-label="{{ $i }}点" aria-pressed="false">★</button>
                @endfor
            </div>
        </div>

        <div class="review-modal__footer">
            <button type="button" class="review-modal__cancel"
                onclick="document.getElementById('reviewModal').close()">
                キャンセル
            </button>

            <button type="submit" class="review-modal__submit">
                送信する
            </button>
        </div>
    </form>
</dialog>

@if(!empty($shouldOpenCompleteModal) && $shouldOpenCompleteModal)
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const d = document.getElementById('sellerReviewModal');
        if (d) d.showModal();
    });
</script>
@endif

@endsection