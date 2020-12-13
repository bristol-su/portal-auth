@extends('layouts.portal')

@section('title', 'Register')

@section('app-content')
    <p-verify
        :email-sent="{{(session('resent', false) ? 'true' : 'false')}}"
    >
    </p-verify>
@endsection
