@extends('layouts.app')


@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/address.css') }}">
@endsection

@section('content')
<div class="address-form">
    <h2 class="address-form__heading content__heading">住所の変更</h2>
    <div class="address-form__inner">
        <form class="address-form__form"
            action="{{ route('purchase.address.update', ['item_id' => $sell->id]) }}"
            method="POST">
            @csrf

            <div class="address-form__group">
                <label class="address-form__label" for="postal_code">郵便番号</label>
                <input class="address-form__input" type="text" name="postal_code" id="postal_code"
                    value="{{ old('postal_code', $user->profile->postal_code ?? '') }}">
                @error('postal_code')
                <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="address-form__group">
                <label class="address-form__label" for="address">住所</label>
                <input class="address-form__input" type="text" name="address" id="address"
                    value="{{ old('address', $user->profile->address ?? '') }}">
                @error('address')
                <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="address-form__group">
                <label class="address-form__label" for="building">建物名</label>
                <input class="address-form__input" type="text" name="building" id="building"
                    value="{{ old('building', $user->profile->building ?? '') }}">
                @error('building')
                <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <button class="address-form__btn btn" type="submit">更新する</button>
        </form>
    </div>

</div>