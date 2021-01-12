@extends('bristolsu::base')

@section('title', 'Reset Password')

@section('content')

    <x-portal-card
            title="Reset Password"
            subtitle="Change your password">
        <x-slot name="body">
            <form action="{{$formUrl}}" method="POST">
                @csrf

                <x-portal-text
                        id="identifier"
                        name="identifier"
                        :label="\Illuminate\Support\Str::title(\BristolSU\Auth\Settings\Credentials\IdentifierAttribute::getValue())"
                        :errors="$errors->get('identifier')"
                        :validated="$errors->has('identifier')"
                        :required="true"
                        :disabled="true"
                        :value="$email"
                >

                </x-portal-text>

                <x-portal-password
                        id="password"
                        name="password"
                        label="Password"
                        help="Enter a secure password to protect your account."
                        sr-label="Enter a secure password to protect your account."
                        :errors="$errors->get('password')"
                        :validated="$errors->has('password')"
                        :required="true"
                >

                </x-portal-password>

                <x-portal-password
                        id="password_confirmation"
                        name="password_confirmation"
                        label="Confirm Password"
                        help="Enter your password again."
                        sr-label="Enter your password again."
                        :errors="$errors->get('password_confirmation')"
                        :validated="$errors->has('password_confirmation')"
                        :required="true"
                >

                </x-portal-password>

                <x-portal-button type="submit">
                    Reset Password
                </x-portal-button>

            </form>
        </x-slot>
        <x-slot name="actions">
        </x-slot>

    </x-portal-card>



@endsection
