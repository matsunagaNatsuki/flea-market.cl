@extends('layouts.auth')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/seller.css') }}">
@endsection

@section('title', '購入者用チャット画面')

@section('content')
<div class="container">
    <h2>{{ $trade->sell->user->name }} の取引画面</h2>

    <div class="product-box">
        <img src="{{ asset('storage/' . $trade->sell->image) }}" alt="商品画像" width="200">
        <h3>{{ $trade->sell->name }}</h3>
        <p>価格：¥{{ number_format($trade->sell->price) }}</p>
    </div>

    <div class="chat-box">
        @foreach($trade->messages as $message)
        <div class="message {{ $message->user_id === Auth::id() ? 'my-message' : 'other-message' }}">
            <strong>{{ $message->user->name }}</strong>
            <p>{{ $message->body }}</p>
            @if($message->image)
            <img src="{{ asset('storage/' . $msg->image) }}" alt="添付画像" width="150">
            @endif
            <small>{{ $message->created_at->format('Y/m/d H:i') }}</small>
        </div>
        @endforeach
    </div>

    <form action="{{ url('/chat/buyer/' . $trade->id) }}" method="POST" enctype="multipart/form-data" class="message-form">
        @csrf
        <label for="body">取引メッセージを記入してください</label>
        <textarea name="body" rows="3" class="form-control" required></textarea>
        <label for="image">画像を追加</label>
        <input type="file" name="image" class="form-control">

        <button type="submit" class="btn btn-primary mt-2">
            <i class="fas fa-paper-plane"></i> 送信</button>
    </form>
</div>



@endsection