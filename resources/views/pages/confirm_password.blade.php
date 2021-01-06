@extends('bristolsu::base')

@section('title', 'Secure Entry')

@section('content')

    <form action="{{route('password.confirmation')}}" method="POST">
        @csrf
        <input type="Password" name="password" />
        <button type="submit">Confirm</button>
    </form>

@endsection
