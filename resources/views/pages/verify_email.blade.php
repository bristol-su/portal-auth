@extends('bristolsu::base')

@section('title', 'Verify')

@section('content')

    Your email must be verified. Click below to resend the email.

    <form action="{{route('verify.resend')}}">
        @csrf
        <portal-button type="submit">
            Resend
        </portal-button>
    </form>

@endsection
