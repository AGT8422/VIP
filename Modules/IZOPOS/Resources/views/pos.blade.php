@extends('layouts.app')

@section('title', __('Pos'))


@section('content')
<section class="content">
    <h1>Hello World</h1>

    <p>
        This view is loaded from module:  POS {!! config('izopos.name') !!}
    </p>
<section>
@endsection
