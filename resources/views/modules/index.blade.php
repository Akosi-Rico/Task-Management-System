@extends('layout.app')
@section("body")
    <parent
        :baseimage="{{ json_encode(asset("image")) }}"
        :data="{{ json_encode($data) }}">
    </parent>
    @section("javascript")
      
    @endsection
@endsection


