@extends('layout.app')
@section("body")
    <register
        :baseimage="{{ json_encode(asset("image")) }}"
        :registerurl=" {{ json_encode(route('login.store')) }}"
        :baseurl="{{ json_encode(route("index") )}}">
    </register>
@endsection