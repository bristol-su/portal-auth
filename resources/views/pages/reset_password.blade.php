@extends('portal-auth::layout')

@section('title', 'Reset Password')

@section('auth-content')

    <p-featured-card
        logo="{{ asset('images/logo.png') }}"
        title="Reset Password"
        bg="login-page"
    >
        <register-form
            button-text="Reset Password"
            route="{{$formUrl}}"
            identifier-value="{{$email}}"
            identifier="{{ \Illuminate\Support\Str::title(\BristolSU\Auth\Settings\Credentials\IdentifierAttribute::getValue()) }}"
            identifier-key="{{ \BristolSU\Auth\Settings\Credentials\IdentifierAttribute::getValue() }}"
        >
        </register-form>

    </p-featured-card>


@endsection
