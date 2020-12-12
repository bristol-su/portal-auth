@extends('layouts.app')

@section('title', 'Password Reset')

@section('app-content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Reset Password') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
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

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Send Password Reset Link') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
