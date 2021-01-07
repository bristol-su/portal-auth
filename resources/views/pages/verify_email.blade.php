@extends('bristolsu::base')

@section('title', 'Verify')

@section('content')

    Your email must be verified. Click below to resend the email.

    <form action="{{route('verify.resend')}}" method="POST">
        @csrf
        <button type="submit">
            Resend
        </button>
    </form>

@endsection
