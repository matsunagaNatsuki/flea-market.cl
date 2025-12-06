@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/profile_edit.css') }}">
@endsection

@section('content')
<div class="profile-form">
    <h2 class="profile-form__heading content__heading">プロフィール設定</h2>
    <div class="profile-form__inner">
        <form class="profile-form" action="/mypage/profile" method="post" enctype="multipart/form-data" novalidate>
            @csrf
            <div class="profile-form_image">
                <label class="profile-form__label" for="profile_image">画像を選択する</label>
                <input class="profile-form__image" type="file" name="image" id="profile_image">
                <img id="profile_preview"
                    src="{{ optional($profile)->image ? asset('storage/' . optional($profile)->image) : asset('images/cat_default_avatar.png') }}"
                    alt="{{ optional($profile)->name }}">
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const fileInput = document.getElementById('profile_image');
                    const previewImg = document.getElementById('profile_preview');

                    if (!fileInput || !previewImg) return;

                    fileInput.addEventListener('change', function(event) {
                        const file = event.target.files[0];
                        if (!file) {
                            return;
                        }
                        const imageUrl = URL.createObjectURL(file);
                        previewImg.src = imageUrl;
                    });
                });
            </script>


            <div class="profile-form__group">
                <label class="profile-form__label" for="name">ユーザー名</label>
                <input class="profile-form__name" type="text" name="name" id="name" value="{{ old('name', optional($profile)->name) }}">

            </div>

            <div class="profile-form__group">
                <label class="profile-form__label" for="postal_code">郵便番号</label>
                <input class="profile-form__postal_code" type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', optional($profile)->postal_code) }}">
            </div>

            <div class="profile-form__group">
                <label class="profile-form__label" for="address">住所</label>
                <input class="profile-form___address" type="text" name="address" id="address" value="{{ old('address', optional($profile)->address) }}">
            </div>

            <div class="profile-form__group">
                <label class="profile-form__label" for="building">建物名</label>
                <input class="profile-form__build" type="text" name="building" id="building" value="{{ old('building', optional($profile)->build) }}">
            </div>

            <div class="btn">
                <button type="submit">更新する</button>
            </div>

            @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
                @endforeach
            </div>
            @endif
        </form>
    </div>
</div>
@endsection