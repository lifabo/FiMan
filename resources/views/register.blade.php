@extends('base_layout')

@section('pageHeading')
    <h1 class="pageHeading">Konto erstellen</h1>
@endsection

@section('content')
    <p>Dein Passwort muss mindestens 10 Zeichen lang sein und mindestens ein Sonderzeichen und eine Zahl beinhalten.</p>
    <form action="/verifyAccountCreation" method="post">
        @csrf
        <input type="text" class="input" placeholder="Username" name="username" autocomplete="off" required
            value="{{ session('username') }}">

        <input type="password" class="input" placeholder="Password" name="password" required
            value="{{ session('password') }}">

        <input type="password" class="input" placeholder="Password wiederholen" name="passwordRepeat" required
            value="{{ session('passwordRepeat') }}">

        <input type="submit" class="registerBtn" value="Konto erstellen">

        @if (session()->has('status'))
            <p> {{ session('status') }} </p>
        @endif
    </form>
@endsection
