<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/verify.css') }}">
    @yield('css')
</head>

<div class="mail_notice--div">
    <div class="mail_notice--header">
        <p class="notice_header--p">メール認証はお済みですか？</p>
    </div>

    <div class="mail_notice--content">
        @if (session('resent'))
        <p class="notice_resend--p" role="alert">
            新規認証メールを再送信しました！
        </p>
        @endif
        <p class="alert_resend--p">
            このページを閲覧するには、Eメールによる認証が必要です。
            もし認証用のメールを受け取っていない場合、
        <form class="mail_resend--form" method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="mail_resend--button">こちらのリンク</button>をクリックして、認証メールを受け取ってください。
        </form>
        </p>
    </div>
</div>