@extends('bristolsu::base')

@section('title', 'Register')

@section('content')

    <x-portal-card
            title="Register">
        <x-slot name="body">
            <form action="{{route('register.action')}}" method="POST">
                @csrf

                <x-portal-text
                        id="identifier"
                        name="identifier"
                        :label="\Illuminate\Support\Str::title(\BristolSU\Auth\Settings\Credentials\IdentifierAttribute::getValue())"
                        help="Enter the {{\BristolSU\Auth\Settings\Credentials\IdentifierAttribute::getValue()}} you would like to use to log in."
                        sr-label="Enter the {{\BristolSU\Auth\Settings\Credentials\IdentifierAttribute::getValue()}} you would like to use to log in."
                        :errors="$errors->get('identifier')"
                        :validated="$errors->has('identifier')"
                        :required="true"
                        :value="old('identifier')"
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
                    Register
                </x-portal-button>

            </form>
        </x-slot>
        <x-slot name="actions">
            <x-portal-link href="{{route('login')}}">Login</x-portal-link>
        </x-slot>

    </x-portal-card>

@endsection
