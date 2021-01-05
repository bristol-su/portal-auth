@extends('bristolsu::base')

@section('title', 'Verify')

@section('content')

    Your email must be verified

    <form action="{{route('verify.resend')}}">
        @csrf
        <portal-button type="submit">
            Resend
        </portal-button>
    </form>

@endsection
