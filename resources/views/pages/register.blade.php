@extends('bristolsu::base')

@section('title', 'Login')

@section('content')

    <form action="{{route('register.action')}}" method="POST">
        @csrf
        Email: <input type="text" placeholder="Your email address" name="identifier" />
        @if($errors->has('identifier'))
            {{$errors->first('identifier')}}
        @endif

        Password: <input type="password" name="password" />
        @if($errors->has('password'))
            {{$errors->first('password')}}
        @endif

        Password Confirmation: <input type="password" name="password_confirmation" />

        <button type="submit">Register</button>
    </form>

    <a href="{{route('login')}}">Login</a>

@endsection
