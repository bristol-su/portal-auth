@extends('bristolsu::base')

@section('title', 'Login')

@section('content')






    <div class="container">




        @if(session()->has('messages'))
            @foreach(session()->get('messages') as $message)
                <div class="alert alert-{{$message['type']}}">{{$message['message']}}</div>
            @endforeach
        @endif


        <div class="row justify-content-center">
            <div class="col-md-8" style="padding-top: 15px;">
                <div class="card">
                    <div class="card-header">{{ __('Login') }}</div>

                    <div class="card-body">
                        <div class="row justify-content-center">
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5>Login</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">


                                        <form method="POST" action="{{ route('login.action') }}">
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


                                            <div class="form-group row">
                                                <label for="identifier"
                                                       class="col-md-4 col-form-label text-md-right">{{ \Illuminate\Support\Str::title(\BristolSU\Auth\Settings\Credentials\IdentifierAttribute::getValue()) }}</label>

                                                <div class="col-md-6">
                                                    <input id="identifier" type="text"
                                                           class="form-control{{ $errors->has('identifier') ? ' is-invalid' : '' }}"
                                                           name="identifier" value="{{ old('identifier') }}" required
                                                           autofocus
                                                        aria-describedby="identifier-help">

                                                    <small id="identifier-help" class="form-text text-muted">
                                                        Enter the {{\BristolSU\Auth\Settings\Credentials\IdentifierAttribute::getValue()}} you used to register.
                                                    </small>

                                                    @if ($errors->has('identifier'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('identifier') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="password"
                                                       class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                                                <div class="col-md-6">
                                                    <input id="password" type="password"
                                                           class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                                           name="password" required>

                                                    @if ($errors->has('password'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('password') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col-md-6 offset-md-4">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="remember"
                                                               id="remember" {{ old('remember') ? 'checked' : '' }}>

                                                        <label class="form-check-label" for="remember">
                                                            {{ __('Remember Me') }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col-md-6 offset-md-4">
                                                    <button type="submit" class="btn btn-block btn-primary">
                                                        {{ __('Login') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                        <div class="form-group row">
                                            <div class="col-md-6 offset-md-4">
                                                <a class="btn btn-link" href="{{ route('password.forgot') }}">
                                                    {{ __('Forgot Your Password?') }}
                                                </a>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-xs-12 col-sm-12 col-md-10 col-lg-8 col-xl-6 offset-md-4">
                                                <a class="btn btn-link" href="{{ route('register') }}">
                                                    {{ __('I\'m new here!') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(count($social))
                        <div class="col-md-3" style="border-left: 2px solid black">
                            <div class="row">
                                <div class="col-md-12">
                                    <h5>Or login with...</h5>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    @foreach($social as $driver)
                                        <a href="{{route('social.login', ['driver' => $driver])}}">
                                            <button type="button" variant="secondary">
                                                Login with {{\Illuminate\Support\Str::title($driver)}}
                                            </button>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>









    <x-portal-card
            title="Login">
        <x-slot name="body">


        </x-slot>

        <x-slot name="actions">
            <x-portal-link href="{{route('register')}}">Register</x-portal-link>
            <x-portal-link href="{{route('password.forgot')}}">Forgot Password</x-portal-link>
        </x-slot>

    </x-portal-card>

@endsection
