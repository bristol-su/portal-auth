@extends('portal-auth::layout')

@section('title', 'Verify')

@section('auth-content')

    @if(session()->has('messages'))
        @foreach(session()->get('messages') as $message)
            <div class="alert alert-{{$message['type']}}">{{$message['message']}}</div>
        @endforeach
    @endif

    <p-featured-card
        logo="{{ asset('images/logo.png') }}"
        title="Verify your email address"
        bg="login-page"
    >
        <div class="w-full">
            <p class="mb-3">Before proceeding, please check your email for a verification link.</p>
            <p class="mb-5">If you did not receive the email, <a href="#" onclick="event.preventDefault(); document.getElementById('resend-form').submit();"> {{ __('click here to request another') }}</a>, check your email address on our website or contact us.</p>

            <form id="resend-form" action="{{ route('verify.resend') }}" method="POST"
                  style="display: none;">@csrf</form>

            <p-button variant="primary" href="{{ route('login') }}">
                Back to Login
            </p-button>

        </div>
    </p-featured-card>

@endsection
