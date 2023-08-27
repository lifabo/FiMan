@extends('base_layout')

@section('pageHeading')
    <h1 class="pageHeading">Login</h1>
@endsection

@section('content')
    <form action="/loginVerification" method="post">
        @csrf
        <input type="text" class="input" placeholder="Username" name="username" autocomplete="off" required
            value="{{ session('username') }}">

        <input type="password" class="input" placeholder="Password" name="password" required
            value="{{ session('password') }}">

        <input type="submit" class="loginBtn" value="Login">

        @if (session()->has('status'))
            <p> {{ session('status') }} </p>
        @endif
    </form>
@endsection
