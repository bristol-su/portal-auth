@extends('bristolsu::base')

@section('title', 'Secure Entry')

@section('content')

    <form action="{{route('password.confirmation')}}" method="POST">
        @csrf
        <input type="Password" name="password" />
        @if($errors->has('password'))
            {{$errors->first('password')}}
        @endif
        <button type="submit">Confirm</button>
    </form>

@endsection
