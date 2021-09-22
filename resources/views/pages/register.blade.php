@extends('portal-auth::layout')

@section('title', 'Register')

@section('auth-content')

    <p-featured-card
        logo="{{ asset('images/logo.png') }}"
        title="Register"
        subtext="{{\BristolSU\Auth\Settings\Messaging\RegisterSubtitle::getValue()}}"
        bg="login-page"
    >
        <div class="w-full">
            <p-tabs>
                <p-tab title="Register">
                    <register-form
                        route="{{route('register.action')}}"
                        identifier="{{ \Illuminate\Support\Str::title(\BristolSU\Auth\Settings\Credentials\IdentifierAttribute::getValue()) }}"
                        identifier-key="{{ \BristolSU\Auth\Settings\Credentials\IdentifierAttribute::getValue() }}"
                        button-text="Create Account"
                    >
                    </register-form>
                    <p-button variant="secondary" href="{{ route('login') }}">
                        I have an account
                    </p-button>
                </p-tab>
                @if(count($social))
                    <p-tab title="Socials">
                        @foreach($social as $driver)
                            <a href="{{route('social.login', ['driver' => $driver])}}">
                                <p-button>
                                    Login with {{\Illuminate\Support\Str::title($driver)}}
                                </p-button>
                            </a>
                        @endforeach
                    </p-tab>
                @endif
            </p-tabs>
        </div>

    </p-featured-card>

@endsection
