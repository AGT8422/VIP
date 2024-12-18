@extends('layouts.app')

@section('title', __('Izo Pos'))


@section('content')
<section class="content">
    <h1>Hello World</h1>

    <p>
        This view is loaded from module:  {!! config('izopos.name') !!}
    </p>
<section>
@endsection
