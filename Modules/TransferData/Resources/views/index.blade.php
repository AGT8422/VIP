@extends('transferdata::layouts.master')

@section('content')
    <h1>Hello World &rightarrow;</h1>

    <p>
        This view is loaded from module: {!! config('transferdata.name') !!}
    </p>
@endsection
