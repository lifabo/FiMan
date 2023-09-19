<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FiMan</title>
    {{-- <link rel="stylesheet" href="{{ asset('css/style.css') }}"> --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="{{ asset('js/navbar.js') }}" defer></script>
    @yield('head')
</head>

<body>
    <nav class="navbar navbar-dark navbar-expand-sm bg-dark sticky-top">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle Notification">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse ml-auto" id="navbarNav">
                <ul class="navbar-nav w-100">
                    <li class="nav-item">
                        <a href="/" class="nav-link" aria-current="page">Startseite</a>
                    </li>

                    <li class="nav-item">
                        <a href="/categories" class="nav-link">Kategorien</a>
                    </li>

                    <li class="nav-item">
                        <a href="/expenses" class="nav-link">Ausgabenübersicht</a>
                    </li>

                    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <span class="nav-link text-white">angemeldet als:
                                    {{ session('loggedInUsername') }}</span>
                            </li>

                            <li class="nav-item">
                                <a href="/logout" class="nav-link">Logout</a>
                            </li>
                        </ul>
                    </div>
                </ul>
            </div>
        </div>
    </nav>



    <div class="container">
        <h1 class="mt-5">@yield('pageHeading')</h1>
        <p class="lead">@yield('pageDescription')</p>
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>
</body>

</html>
