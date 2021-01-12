@extends('bristolsu::base')

@section('title', 'Secure Entry')

@section('content')

    <x-portal-card
        title="Confirm Password"
        subtitle="You need to confirm your password before you can access this page.">
        <x-slot name="body">
            <form action="{{route('password.confirmation')}}" method="POST">
                @csrf

                <x-portal-password
                        id="password"
                        name="password"
                        label="Password"
                        help="Enter the password you use to log into the portal"
                        sr-label="Enter the password you use to log into the portal"
                        :errors="$errors->get('password')"
                        :validated="$errors->has('password')"
                        :required="true"
                >

                </x-portal-password>

                <x-portal-button type="submit">
                    Confirm
                </x-portal-button>

            </form>
        </x-slot>
        <x-slot name="actions">
        </x-slot>

    </x-portal-card>

@endsection
