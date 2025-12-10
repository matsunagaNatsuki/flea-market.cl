@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/sell.css') }}">
@endsection



@section('content')
<div class="sell-form">
    <h1 class="sell-form__heading content__heading">商品の出品</h1>
    <div class="sell-form__inner">
        <form class="sell-form__form" action="{{ route('sells.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="sell-form__image">
                <label class="sell-form__label" for="image">商品画像</label>
                <label for="image" class="sell-form__button" id="image-label">画像を選択する</label>
                <input type="file" class="sell-form__input" name="image" id="image" accept="image/*">

                <img id="preview" src="" alt="選択した画像のプレビュー" style="display: none; max-width: 200px;">

                <script>
                    document.getElementById('image').addEventListener('change', function(event) {
                        var file = event.target.files[0];
                        var label = document.getElementById('image-label')
                        var preview = document.getElementById('preview');

                        if (file) {
                            var reader = new FileReader();
                            reader.onload = function(e) {
                                preview.src = e.target.result;
                                preview.style.display = "block";
                                label.style.visibility = 'hidden';
                            };
                            reader.readAsDataURL(file);
                        }

                        document.getElementById('preview').addEventListener('click', function() {
                            document.getElementById('image').click();
                        })
                    });
                </script>
            </div>

            <div class="sell-detail__form">
                <h2 class="sell-detail__heading content_heading">商品の詳細</h2>

                <div class="sell-detail__form group">
                    <label class="sell-detail__label">カテゴリー</label>
                    <ul class="category-list">
                        @foreach($categories as $category)
                        <li class="category-item">
                            <input type="checkbox" name="category_id[]" value="{{ $category->id }}" id="category_{{ $category->id }}">
                            <label for="category_{{ $category->id }}">{{ $category->name }}</label>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <label for="condition_id">商品の状態</label>
                <select name="condition_id" id="condition_id">
                    <option value="" disabled selected>選択してください</option>
                    @foreach ($conditions as $condition)
                    <option value="{{ $condition->id }}" {{ old('condition_id') == $condition->id ? 'selected' : ''}}>
                        {{ $condition->condition_name }}
                    </option>
                    @endforeach
                </select>

                <h2 class="sell-date__heading content_heading">商品名と説明</h2>

                <div class="sell-date__form group">
                    <label class="sell-date__label" for="name">商品名</label>
                    <input class="sell-date__input" type="text" id="name" name="name">
                </div>

                <div class="sell-date__form group">
                    <label class="sell-date__label" for="brand">ブランド名</label>
                    <input class="sell-date__input" type="text" id="brand" name="brand">
                </div>

                <div class="sell-date__form group">
                    <label class="sell-date__label" for="description">商品の説明</label>
                    <input class="sell-date__input" type="text" id="description" name="description">
                </div>

                <div class="sell-date__form group">
                    <label class="sell-date__label" for="price">販売価格</label>
                    <input class="sell-date__input" type="text" id="price" name="price">
                </div>

                <div class="btn">
                    <button type="submit">出品する</button>
                </div>

                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif



            </div>

        </form>
    </div>
</div>
@endsection