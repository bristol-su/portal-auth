@extends('portal-auth::layout')

@section('title', 'Forgot Password')

@section('auth-content')

    <p-featured-card
        logo="{{ asset('/images/logo.png') }}"
        title="Forgot Password"
        bg="login-page"
    >
        <div class="w-full">
            <p-tabs>
                <p-tab title="Forgot Password">
                    <forgot-password-form
                        route="{{route('password.forgot.action')}}"
                        identifier="{{ \Illuminate\Support\Str::title(\BristolSU\Auth\Settings\Credentials\IdentifierAttribute::getValue()) }}"
                        identifier-key="{{ \BristolSU\Auth\Settings\Credentials\IdentifierAttribute::getValue() }}"
                    >
                    </forgot-password-form>
                    <p-button variant="secondary" href="{{ route('login') }}">
                        I remember my password
                    </p-button>
                </p-tab>
            </p-tabs>
        </div>
    </p-featured-card>

@endsection
