<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FiMan</title>
    <link rel="stylesheet" href="{{ asset('css/customBootstrap.min.css') }}">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="{{ asset('js/navbar.js') }}" defer></script>
    @yield('head')
</head>

<body>
    <nav class="navbar navbar-dark bg-dark navbar-expand-sm sticky-top">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle Notification">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse ml-auto" id="navbarNav">
                <ul class="navbar-nav w-100">
                    <li class="nav-item">
                        <a href="/" class="nav-link" aria-current="page">Statistiken</a>
                    </li>

                    <li class="nav-item">
                        <a href="/expenses" class="nav-link">Ausgaben</a>
                    </li>

                    <li class="nav-item">
                        <a href="/categories" class="nav-link">Kategorien</a>
                    </li>

                    <li class="nav-item">
                        <a href="/bankAccounts" class="nav-link">Konten</a>
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



    <div class="container" style="margin-bottom: 200px">
        <h1 class="mt-3">@yield('pageHeading')</h1>
        <p class="lead">@yield('pageDescription')</p>
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>
</body>

</html>
