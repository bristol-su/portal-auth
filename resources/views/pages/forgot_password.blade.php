@extends('bristolsu::base')

@section('title', 'Forgot Password')

@section('content')

    @if(session()->has('messages'))
        @foreach(session()->get('messages') as $message)
            <x-portal-alert :variant="$message['type']" :dismissible="true">
                {{$message['message']}}
            </x-portal-alert>
        @endforeach
    @endif

    <x-portal-card
            title="Forgot Password"
            subtitle="Regain access to your account">
        <x-slot name="body">
            <form action="{{route('password.forgot.action')}}" method="POST">
                @csrf

                <x-portal-text
                        id="identifier"
                        name="identifier"
                        :label="\Illuminate\Support\Str::title(\BristolSU\Auth\Settings\Credentials\IdentifierAttribute::getValue())"
                        help="Enter the {{\BristolSU\Auth\Settings\Credentials\IdentifierAttribute::getValue()}} you used to register."
                        sr-label="Enter the {{\BristolSU\Auth\Settings\Credentials\IdentifierAttribute::getValue()}} you used to register."
                        :errors="$errors->get('identifier')"
                        :validated="$errors->has('identifier')"
                        :required="true"
                        :value="old('identifier')"
                >

                </x-portal-text>

                <x-portal-button type="submit">Send Reset Link</x-portal-button>
            </form>

        </x-slot>
        <x-slot name="actions">
        </x-slot>

    </x-portal-card>


@endsection
