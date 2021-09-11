@extends('portal-auth::layout')

@section('title', 'Verify')

@section('auth-content')

    <p-featured-card
        logo="{{ asset('images/logo.png') }}"
        title="Verify your email address"
        bg="login-page"
    >
        <div class="w-full">
            <p class="mb-3">Before proceeding, please check your email for a verification link.</p>
            <p class="mb-5">If you did not receive the email, <a href="#" onclick="event.preventDefault(); document.getElementById('resend-form').submit();"> {{ __('click here to request another email') }}</a>, check your email address on our website or contact us.</p>

            <form id="resend-form" action="{{ route('verify.resend') }}" method="POST"
                  style="display: none;">@csrf</form>

            <p-button variant="primary" onclick="event.preventDefault(); document.getElementById('portal-auth-logout-form').submit();">
                Back to Login
            </p-button>

            <form id="portal-auth-logout-form" action="{{ route('logout') }}" method="POST"
                  style="display: none;">@csrf</form>

        </div>
    </p-featured-card>

@endsection
