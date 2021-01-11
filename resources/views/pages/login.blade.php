@extends('bristolsu::base')

@section('title', 'Login')

@section('content')

    <form action="{{route('login.action')}}" method="POST">
        @csrf
        <input type="text" placeholder="Your email address" name="identifier" />
        @if($errors->has('identifier'))
            {{$errors->first('identifier')}}
        @endif

        <input type="Password" name="password" />
        @if($errors->has('password'))
            {{$errors->first('password')}}
        @endif

        <x-portal-button type="submit">
            Login
        </x-portal-button>
    </form>

    <a href="{{route('register')}}">Register</a>
    <a href="{{route('password.forgot')}}">Forgot Password</a>

@endsection
