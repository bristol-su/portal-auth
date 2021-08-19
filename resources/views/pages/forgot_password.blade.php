@extends('portal-auth::layout')

@section('title', 'Forgot Password')

@section('auth-content')

    @if(session()->has('messages'))
        @foreach(session()->get('messages') as $message)
            <div class="alert alert-{{$message['type']}}">{{$message['message']}}</div>
        @endforeach
    @endif

    <p-featured-card
        title="Forgot Password"
        bg="login-page"
    >
        <forgot-password-form
            route="{{route('password.forgot.action')}}"
            identifier="{{ \Illuminate\Support\Str::title(\BristolSU\Auth\Settings\Credentials\IdentifierAttribute::getValue()) }}"
            identifier-key="{{ \BristolSU\Auth\Settings\Credentials\IdentifierAttribute::getValue() }}"
        >
        </forgot-password-form>
        <a href="{{ route('login') }}">
            I remember my password
        </a>

    </p-featured-card>

@endsection
