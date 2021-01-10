@extends('bristolsu::base')

@section('title', 'Reset Password')

@section('content')

    <form action="{{route('password.reset.action')}}" method="POST">
        @csrf
        <input type="text" disabled value="{{$email}}" placeholder="Your email address" name="identifier" />
        @if($errors->has('identifier'))
            {{$errors->first('identifier')}}
        @endif

        <input type="Password" name="password" />
        @if($errors->has('password'))
            {{$errors->first('password')}}
        @endif

        <input type="Password" name="password_confirmation" />

        <button type="submit">Reset Password</button>
    </form>

@endsection
