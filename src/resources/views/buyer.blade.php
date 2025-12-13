@extends('layouts.auth')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/seller.css') }}">
@endsection

@section('title', '購入者用チャット画面')

@section('content')
<div class="container">
    <h2>{{ $trade->sell->user->name }} の取引画面</h2>

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

    <form action="{{ url('/chat/buyer/' . $trade->id) }}" method="POST" enctype="multipart/form-data" class="message-form" novalidate>
        @csrf
        <label for="body">取引メッセージを記入してください</label>
        <textarea id="chat-body" name="body" rows="3" data-trade-id="{{ $trade->id }}" data-user-id="{{ auth()->id() }}" class="form-control">{{ old('body','') }}</textarea>


        <label for="image">画像を追加</label>
        <input type="file" name="image" class="form-control">

        <button type="submit" class="btn btn-primary mt-2">
            <i class="fas fa-paper-plane"></i>送信</button>
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </form>
</div>

<script>
    (function() {
        const ID = 'chat-body';

        function el() {
            return document.getElementById(ID);
        }

        function keyOf(t) {
            return `chat_draft_trade_${t.dataset.tradeId}_user_${t.dataset.userId}`;
        }

        function restore(force = false) {
            const t = el();
            if (!t) return;
            const saved = localStorage.getItem(keyOf(t));
            if (!saved) return;

            if (force || t.value.trim() === '') {
                t.value = saved;
            }
        }

        function save() {
            const t = el();
            if (!t) return;
            const v = t.value;
            if (v.trim() === '') return;
            localStorage.setItem(keyOf(t), v);
        }

        window.addEventListener('pageshow', function() {
            restore(true);
            setTimeout(() => restore(true), 0);
            requestAnimationFrame(() => restore(true));
        });

        window.addEventListener('pagehide', save);

        let last = null;
        setInterval(() => {
            const t = el();
            if (!t) return;
            if (t.value !== last) {
                last = t.value;
                save();
            }
        }, 500);

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => restore(false));
        } else {
            restore(false);
        }

        document.addEventListener('submit', function(e) {
            const t = el();
            if (!t) return;
            if (e.target && e.target.closest('form')) {
                localStorage.removeItem(keyOf(t));
            }
        }, true);
    })();
</script>
@endsection