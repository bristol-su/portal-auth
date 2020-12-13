@extends('layouts.portal')

@section('title', 'Register')

@section('app-content')
    <p-register
        route="{{route('register')}}"
        default-identifier="{{old('identifier')}}"
        :default-remember="{{ (old('remember', false) ? 'true' : 'false') }}"
        :server-errors="{{count($errors) > 0 ? $errors : '{}'}}"
        class="align-center"
    >
        </div>
@endsection
