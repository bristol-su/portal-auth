@extends('bristolsu::base')

@section('title', 'Verify')

@section('content')

    @if(session()->has('messages'))
        @foreach(session()->get('messages') as $message)
            <x-portal-alert :variant="$message['type']" :dismissible="true">
                {{$message['message']}}
            </x-portal-alert>
        @endforeach
    @endif

    <x-portal-card
            title="Verfication"
            subtitle="We need to verify your email address">
        <x-slot name="body">

            You should have received an email with a link to verify your email. If you haven't received the email, click resend.

            <form action="{{route('verify.resend')}}" method="POST">
                @csrf
                <x-portal-button type="submit">
                    Resend
                </x-portal-button>
            </form>
        </x-slot>
        <x-slot name="actions">
        </x-slot>

    </x-portal-card>

@endsection
