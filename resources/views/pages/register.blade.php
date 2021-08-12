@extends('bristolsu::base')

@section('title', 'Register')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Register') }}</div>

                    <div class="card-body">
                        <div class="row justify-content-center">
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5>Create an account...</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">


                                        <form method="POST" action="{{ route('register.action') }}">
                                            @csrf

                                            <div class="form-group row">
                                                <label for="identifier"
                                                       class="col-md-4 col-form-label text-md-right">{{ \Illuminate\Support\Str::title(\BristolSU\Auth\Settings\Credentials\IdentifierAttribute::getValue()) }}</label>

                                                <div class="col-md-6">
                                                    <input id="identifier" type="text"
                                                           class="form-control{{ $errors->has('identifier') ? ' is-invalid' : '' }}"
                                                           name="identifier" value="{{ old('identifier') }}" required>

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
                                                <label for="password-confirm"
                                                       class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                                                <div class="col-md-6">
                                                    <input id="password-confirm" type="password" class="form-control"
                                                           name="password_confirmation" required>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col-md-6 offset-md-4">
                                                    <button type="submit" class="btn btn-block btn-primary">
                                                        {{ __('Create Account') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                        <div class="form-group row">
                                            <div class="col-xs-12 col-sm-12 col-md-10 col-lg-8 col-xl-6 offset-md-4">
                                                <a class="btn btn-link" href="{{ route('login') }}">
                                                    {{ __('I have an account!') }}
                                                </a>
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
        </div>
    </div>

@endsection
