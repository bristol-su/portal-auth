@extends('portal-auth::layout')

@section('title', 'Verify')

@section('auth-content')

    @if(session()->has('messages'))
        @foreach(session()->get('messages') as $message)
            <div class="alert alert-{{$message['type']}}">{{$message['message']}}</div>
        @endforeach
    @endif

    <p-featured-card
        title="Verify your email address"
        bg="login-page"
    >
        Before proceeding, please check your email for a verification link. If you did not receive the email, <a href="#" onclick="event.preventDefault(); document.getElementById('resend-form').submit();"> {{ __('click here to request another') }}</a>, check your email address on our website or contact us.

        <form id="resend-form" action="{{ route('verify.resend') }}" method="POST"
              style="display: none;">@csrf</form>

    </p-featured-card>

@endsection
