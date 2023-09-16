<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    {{-- <link rel="stylesheet" href="{{ asset('css/style.css') }}"> --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous">
    </script>
</head>

<body>
    <section class="vh-100 bg-dark">
        <div class="d-flex align-items-center justify-content-center h-100 text-center">
            <form action="/loginVerification" method="post" style="width: 23rem;">
                @csrf
                <h3 class="fw-normal mb-3 text-white" style="letter-spacing: 1px;">FiMan</h3>

                <div class="mb-4">
                    <input type="text" id="txtUsername" placeholder="Benutzername" name="username" autofocus
                        autocomplete="off" required value="{{ session('username') }}"
                        class="form-control form-control-lg">
                </div>

                <div class="mb-4">
                    <input type="password" id="txtPassword" placeholder="Passwort" name="password" required
                        value="{{ session('password') }}" class="form-control form-control-lg">
                </div>

                <div class="mb-4">
                    <button class="btn btn-primary btn-lg btn-block w-100" type="submit">Login</button>
                </div>
                @if (session()->has('status'))
                    <p class="text-danger">
                        {{ session('status') }}
                    </p>
                @endif
            </form>
            {{-- <div class="col-sm-7 px-0 d-none d-sm-block">
                    <img src="/img/IMG_4836.JPG" alt="Login image" class="w-100 vh-100"
                        style="object-fit: cover; object-position: left;">
                </div> --}}
        </div>
    </section>
</body>

</html>
