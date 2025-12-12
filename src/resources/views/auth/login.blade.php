@extends('layouts.auth')


@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
@endsection

@section('content')
<div class="login-form">
    <h2 class="login-form__heading content__heading">ログイン</h2>
    <div class="login-form__inner">
        <form class="login-form__form" action="/login" method="POST" novalidate>
            @csrf

            <div class="login-form__group">
                <label class="login-form__label" for="email">メールアドレス</label>
                <input class="login-form__input" type="email" name="email" id="email">
                @if ($errors->has('email'))
                    <p class="login-form__error-message">{{ $errors->first('email') }}</p>
                @endif
            </div>

            <div class="login-form__group">
                <label class="login-form__label" for="password">パスワード</label>
                <input class="login-form__input" type="password" name="password" id="password">
                @if ($errors->has('password'))
                    <p class="login-form__error-message">{{ $errors->first('password') }}</p>
                @endif
            </div>

            <button class="login-form__btn btn" type="submit">ログインする</button>
            <a href="/register">会員登録はこちら</a>
        </form>
    </div>
</div>
@endsection
