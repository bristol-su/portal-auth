@extends('bristolsu::base')

@section('title', 'Login')

@section('content')

    <form action="{{route('login.action')}}" method="POST">
        @csrf
        <input type="text" placeholder="Your email address" name="identifier" />
        <input type="Password" name="password" />

        <button type="submit">Login</button>
    </form>

    <a href="{{route('register')}}">Register</a>

@endsection
