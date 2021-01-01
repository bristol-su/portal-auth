@extends('layouts.portal')

@section('title', 'Password Reset')

@section('app-content')
<p-reset-password
    route="{{route('password.update')}}"
    token="{{$token}}"
    identifier="{{$identifier}}">
</p-reset-password>
@endsection
