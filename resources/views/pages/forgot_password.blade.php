@extends('bristolsu::base')

@section('title', 'Forgot Password')

@section('content')

    <form action="{{route('password.forgot.action')}}" method="POST">
        @csrf
        <input type="text" placeholder="Your email address" name="identifier" />
        @if($errors->has('identifier'))
            {{$errors->first('identifier')}}
        @endif

        <button type="submit">Send Reset Link</button>
    </form>

@endsection
