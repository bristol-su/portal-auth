@extends('portal-auth::layout')

@section('title', 'Register')

@section('auth-content')

    @if(session()->has('messages'))
        @foreach(session()->get('messages') as $message)
            <div class="alert alert-{{$message['type']}}">{{$message['message']}}</div>
        @endforeach
    @endif

    <p-featured-card
        title="Register"
        subtext="{{\BristolSU\Auth\Settings\Messaging\RegisterSubtitle::getValue()}}"
        bg="login-page"
    >
        <p-tabs>
            <p-tab title="Register">
                <register-form
                    route="{{route('register.action')}}"
                    identifier="{{ \Illuminate\Support\Str::title(\BristolSU\Auth\Settings\Credentials\IdentifierAttribute::getValue()) }}"
                    identifier-key="{{ \BristolSU\Auth\Settings\Credentials\IdentifierAttribute::getValue() }}"
                    button-text="Create Account"
                >
                </register-form>
                <a href="{{ route('login') }}">
                    I have an account
                </a>
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

    </p-featured-card>

@endsection
