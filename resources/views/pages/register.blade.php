@extends('bristolsu::base')

@section('title', 'Login')

@section('content')

    <form action="{{route('register.action')}}" method="POST">
        @csrf
        Email: <input type="text" placeholder="Your email address" name="identifier" />

        Password: <input type="password" name="password" />

        Password Confirmation: <input type="password" name="password_confirmation" />

        <button type="submit">Register</button>
    </form>

    <a href="{{route('login')}}">Login</a>

@endsection
