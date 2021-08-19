@extends('portal-auth::layout')

@section('title', 'Reset Password')

@section('auth-content')

    @if(session()->has('messages'))
        @foreach(session()->get('messages') as $message)
            <div class="alert alert-{{$message['type']}}">{{$message['message']}}</div>
        @endforeach
    @endif

    <p-featured-card
        title="Reset Password"
        bg="login-page"
    >
        <register-form
            button-text="Reset Password"
            route="{{$formUrl}}"
            identifier-value="{{$email}}"
            identifier="{{ \Illuminate\Support\Str::title(\BristolSU\Auth\Settings\Credentials\IdentifierAttribute::getValue()) }}"
            identifier-key="{{ \BristolSU\Auth\Settings\Credentials\IdentifierAttribute::getValue() }}"
        >
        </register-form>

    </p-featured-card>


@endsection
