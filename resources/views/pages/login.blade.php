@extends('portal-auth::layout')

@section('title', 'Login')

@section('auth-content')

    @if(session()->has('messages'))
        @foreach(session()->get('messages') as $message)
            <div class="alert alert-{{$message['type']}}">{{$message['message']}}</div>
        @endforeach
    @endif

    <p-featured-card
        title="Login"
        subtext="{{\BristolSU\Auth\Settings\Messaging\LoginSubtitle::getValue()}}"
        bg="login-page"
    >
        <p-tabs>
            <p-tab title="Login">
                <login-form
                    route="{{route('login.action')}}"
                    identifier="{{ \Illuminate\Support\Str::title(\BristolSU\Auth\Settings\Credentials\IdentifierAttribute::getValue()) }}"
                    identifier-key="{{ \BristolSU\Auth\Settings\Credentials\IdentifierAttribute::getValue() }}"
                >
                </login-form>
                <p-button variant="secondary" href="{{ route('password.forgot') }}">
                    Forgot your password?
                </p-button>

                <p-button variant="secondary" href="{{ route('register') }}">
                    I'm new here!
                </p-button>
            </p-tab>
            @if(count($social))
                <p-tab title="social-login">
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
