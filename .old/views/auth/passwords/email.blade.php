@extends('layouts.portal')

@section('title', 'Password Reset')

@section('app-content')
<p-password-email-form
    status="{{session('status', null)}}">
</p-password-email-form>
@endsection
