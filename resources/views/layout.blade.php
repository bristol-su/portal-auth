@extends('bristolsu::base')

@section('content')
    <div id="portal-auth-root">
        @yield('auth-content')
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('modules/portal-auth/js/auth.js') }}"></script>
@endpush
