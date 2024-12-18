@extends("layouts.app")

@section("title",__("home.Create_pos"))

@section('content')

<section class="content">
    @component("components.widget" ,["title"=>__("home.pos")])
    <div class="row">
        @foreach($pos as $it)
            <div class="col-4">
                <a href="/pos/create"> <div class="col-md-3 text-center" style="margin:0% 10%;border:1px solid black;box-shadow:1px 1px 2px black; padding:20px ; border-radius:.4rem; background-color:coral;color:black">
                    <h3 class="btn">{{$it->name}}</h3>
                </div></a>
            </div>
        @endforeach
    </div>
    @endcomponent
</section>

@stop
@section("javascript")
            <script src="{{ asset('js/patterns.js?v=' . $asset_v) }}"></script>
@endsection