<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <title>FiMan</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @yield('head')
</head>

<body>
    <div class="navBar">
        <div class="navBarEntry">
            <a href="/" class="navBarLink">Startseite</a>
        </div>

        {{ session('hallotest') }}

        @if (!session()->has('loggedInUsername'))
            <div class="navBarEntry">
                <a href="/login" class="navBarLink">Login</a>
            </div>

            <div class="navBarEntry">
                <a href="/register" class="navBarLink">Konto erstellen</a>
            </div>
        @endif

        @if (session()->has('loggedInUsername'))
            <div class="navBarEntry">
                <a href="/categories" class="navBarLink">Kategorien</a>
            </div>

            <div class="navBarEntry">
                <a href="" class="navBarLink">Ausgabenübersicht</a>
            </div>

            <div class="navBarEntry">
                <p class="navBarLink">angemeldet als: {{ session('loggedInUsername') }}</p>
            </div>

            <div class="navBarEntry">
                <a href="/logout" class="navBarLink">Logout</a>
            </div>
        @endif
    </div>



    <div class="content">
        @yield('pageHeading')
        @yield('content')
    </div>
</body>

</html>
