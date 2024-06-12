@extends('layout.app')
@section("body")
    <login
        :baseimage="{{ json_encode(asset("image")) }}"
        :loginurl="{{ json_encode(route("login.process")) }}"
        :baseurl="{{ json_encode(route("index") )}}"
        :registerurl=" {{ json_encode(route("login.create"))}}">
    </login>
@endsection
