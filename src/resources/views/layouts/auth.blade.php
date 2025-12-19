<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    @yield('css')
</head>

<body>
    <div class=top-bar></div>
    <div class="logo">
        <img src="{{ asset('storage/images/logo.svg') }}?v={{ time() }}" alt="logo" class="logo-img">
    </div>



    <main>
        @yield('content')
    </main>

</body>

</html>