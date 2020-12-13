@extends('layouts.app')

@section('title', 'Register')

@section('app-content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Register') }}</div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5>Create an account...</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">


                                        <form method="POST" action="{{ route('register') }}">
                                            @csrf

                                            <div class="form-group row">
                                                <label for="identifier"
                                                       class="col-md-4 col-form-label text-md-right">{{ ucfirst(siteSetting('authentication.registration_identifier.identifier')) }}</label>

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

                                            <div class="form-group row mb-0">
                                                <div class="col-md-6 offset-md-4">
                                                    <button type="submit" class="btn btn-primary">
                                                        {{ __('Register') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @if(is_array(siteSetting('thirdPartyAuthentication.providers', [])) && count(siteSetting('thirdPartyAuthentication.providers', [])) > 0)

                            <div class="col-md-3" style="border-left: 2px solid black">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5>Or login with...</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <social-login
                                            :providers="{{json_encode(siteSetting('thirdPartyAuthentication.providers'))}}"></social-login>
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
