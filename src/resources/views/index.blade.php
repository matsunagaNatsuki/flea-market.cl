@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/index.css') }}">
@endsection

@section('content')
@if(isset($search) && $search != '')
<h2>検索結果：「{{$search}}」</h2>
@endif

<div class="border">
    <ul class="border__list">
        <li><a href="{{ route('items.list', ['tab' => 'recommend', 'search' => $search]) }}">おすすめ</a></li>
        @if(!auth()->guest())
        <li><a href="{{ route('items.list', ['tab'=>'mylist', 'search'=>$search]) }}">マイリスト</a></li>
        @endif
    </ul>
</div>

<div class="image-container">
    <div class="row">
        @foreach ($sells as $sell)
        <div class="col-md-2 mb-4">
            <a href="{{ url('/item/' . $sell->id) }}">
                <div class="card">
                    <div class="position-relative">
                        @if ($sell->sold())
                            <div class="sold-ribbon"><span>SOLD</span></div>
                        @endif
                        @if (Str::startsWith($sell->image, ['http://', 'https://']))
                            <img src="{{ $sell->image }}" class="card-img-top img-fluid custom-img" alt="{{ $sell->name }}">
                        @else
                            <img src="{{ Storage::url($sell->image) }}" class="card-img-top img-fluid custom-img" alt="{{ $sell->name }}">
                        @endif
                    </div>

                    <div class="card-body">
                        <h5 class="card-title">{{ $sell->name }}</h5>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endsection