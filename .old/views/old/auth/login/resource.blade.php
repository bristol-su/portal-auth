@extends('layouts.app')

@section('title', 'Log In')

@section('app-content')
    <div id="portal">
        <div class="py-5" id="vue-root">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h2 style="text-align: center">Access to {{$activity->name}}</h2>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-12">
                        <log-into-resource
                            :user="{{$user}}"
                            :can-be-user="{{($canBeUser?'true':'false')}}"
                            :groups="{{$groups}}"
                            :roles="{{$roles}}"
                            :activity="{{$activity}}"
                            redirect-to="{{$redirectTo}}"
                            :admin="{{($admin?'true':'false')}}">

                        </log-into-resource>
                    </div>
                </div>
                <br/>
            </div>
        </div>
    </div>

@endsection
