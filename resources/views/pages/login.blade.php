@extends('portal-auth::layout')

@section('title', 'Login')

@section('auth-content')

    <p-featured-card
        title="{{\BristolSU\Auth\Settings\Messaging\LoginHeader::getValue()}}"
        subtext="{{\BristolSU\Auth\Settings\Messaging\LoginSubtitle::getValue()}}"
        bg="login-page"
    >
        <login-form
            route="{{route('login.action')}}"
            identifier="{{ \Illuminate\Support\Str::title(\BristolSU\Auth\Settings\Credentials\IdentifierAttribute::getValue()) }}"
            identifier-key="{{ \BristolSU\Auth\Settings\Credentials\IdentifierAttribute::getValue() }}"
        >

        </login-form>
    </p-featured-card>

{{--    <div class="container">--}}

{{--        @if(session()->has('messages'))--}}
{{--            @foreach(session()->get('messages') as $message)--}}
{{--                <div class="alert alert-{{$message['type']}}">{{$message['message']}}</div>--}}
{{--            @endforeach--}}
{{--        @endif--}}


{{--                                            <div class="form-group row">--}}
{{--                                                <label for="identifier"--}}
{{--                                                       class="col-md-4 col-form-label text-md-right">{{ \Illuminate\Support\Str::title(\BristolSU\Auth\Settings\Credentials\IdentifierAttribute::getValue()) }}</label>--}}

{{--                                                <div class="col-md-6">--}}
{{--                                                    <input id="identifier" type="text"--}}
{{--                                                           class="form-control{{ $errors->has('identifier') ? ' is-invalid' : '' }}"--}}
{{--                                                           name="identifier" value="{{ old('identifier') }}" required--}}
{{--                                                           autofocus--}}
{{--                                                        aria-describedby="identifier-help">--}}

{{--                                                    <small id="identifier-help" class="form-text text-muted">--}}
{{--                                                        Enter the {{\BristolSU\Auth\Settings\Credentials\IdentifierAttribute::getValue()}} you used to register.--}}
{{--                                                    </small>--}}

{{--                                                    @if ($errors->has('identifier'))--}}
{{--                                                        <span class="invalid-feedback" role="alert">--}}
{{--                                                            <strong>{{ $errors->first('identifier') }}</strong>--}}
{{--                                                        </span>--}}
{{--                                                    @endif--}}
{{--                                                </div>--}}
{{--                                            </div>--}}

{{--                                            <div class="form-group row">--}}
{{--                                                <label for="password"--}}
{{--                                                       class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>--}}

{{--                                                <div class="col-md-6">--}}
{{--                                                    <input id="password" type="password"--}}
{{--                                                           class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"--}}
{{--                                                           name="password" required>--}}

{{--                                                    @if ($errors->has('password'))--}}
{{--                                                        <span class="invalid-feedback" role="alert">--}}
{{--                                                            <strong>{{ $errors->first('password') }}</strong>--}}
{{--                                                        </span>--}}
{{--                                                    @endif--}}
{{--                                                </div>--}}
{{--                                            </div>--}}

{{--                                            <div class="form-group row">--}}
{{--                                                <div class="col-md-6 offset-md-4">--}}
{{--                                                    <div class="form-check">--}}
{{--                                                        <input class="form-check-input" type="checkbox" name="remember"--}}
{{--                                                               id="remember" {{ old('remember') ? 'checked' : '' }}>--}}

{{--                                                        <label class="form-check-label" for="remember">--}}
{{--                                                            {{ __('Remember Me') }}--}}
{{--                                                        </label>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}

{{--                                            <div class="form-group row">--}}
{{--                                                <div class="col-md-6 offset-md-4">--}}
{{--                                                    <button type="submit" class="btn btn-block btn-primary">--}}
{{--                                                        {{ __('Login') }}--}}
{{--                                                    </button>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </form>--}}
{{--                                        <div class="form-group row">--}}
{{--                                            <div class="col-md-6 offset-md-4">--}}
{{--                                                <a class="btn btn-link" href="{{ route('password.forgot') }}">--}}
{{--                                                    {{ __('Forgot Your Password?') }}--}}
{{--                                                </a>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                        <div class="form-group row">--}}
{{--                                            <div class="col-xs-12 col-sm-12 col-md-10 col-lg-8 col-xl-6 offset-md-4">--}}
{{--                                                <a class="btn btn-link" href="{{ route('register') }}">--}}
{{--                                                    {{ __('I\'m new here!') }}--}}
{{--                                                </a>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}

{{--                    @if(count($social))--}}
{{--                        <div class="col-md-3" style="border-left: 2px solid black">--}}
{{--                            <div class="row">--}}
{{--                                <div class="col-md-12">--}}
{{--                                    <h5>Or login with...</h5>--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                            <div class="row">--}}
{{--                                <div class="col-md-12">--}}
{{--                                    @foreach($social as $driver)--}}
{{--                                        <a href="{{route('social.login', ['driver' => $driver])}}">--}}
{{--                                            <button type="button" variant="secondary">--}}
{{--                                                Login with {{\Illuminate\Support\Str::title($driver)}}--}}
{{--                                            </button>--}}
{{--                                        </a>--}}
{{--                                    @endforeach--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    @endif--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}

@endsection
