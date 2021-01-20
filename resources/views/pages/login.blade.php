@extends('bristolsu::base')

@section('title', 'Login')

@section('content')

    @if(session()->has('messages'))
        @foreach(session()->get('messages') as $message)
            <x-portal-alert :variant="$message['type']" :dismissible="true">
                {{$message['message']}}
            </x-portal-alert>
        @endforeach
    @endif

    <x-portal-card
            title="Login">
        <x-slot name="body">
            <form action="{{route('login.action')}}" method="POST">
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

                <x-portal-password
                        id="password"
                        name="password"
                        label="Password"
                        sr-label="Enter the password you use to log into the portal"
                        :errors="$errors->get('password')"
                        :validated="$errors->has('password')"
                        :required="true"
                >

                </x-portal-password>

                <x-portal-button type="submit">
                    Login
                </x-portal-button>

{{--                <input type="Password" name="password" />--}}
{{--                @if($errors->has('password'))--}}
{{--                    {{$errors->first('password')}}--}}
{{--                @endif--}}


            </form>

            @if(count($social) > 0)
                <hr />
                @foreach($social as $driver)
                    <x-portal-link href="{{route('social.login', ['driver' => $driver])}}">
                        <x-portal-button type="button" variant="secondary">
                            Login with {{\Illuminate\Support\Str::title($driver)}}
                        </x-portal-button>
                    </x-portal-link>
                @endforeach
            @endif
        </x-slot>

        <x-slot name="actions">
            <x-portal-link href="{{route('register')}}">Register</x-portal-link>
            <x-portal-link href="{{route('password.forgot')}}">Forgot Password</x-portal-link>
        </x-slot>

    </x-portal-card>

@endsection
