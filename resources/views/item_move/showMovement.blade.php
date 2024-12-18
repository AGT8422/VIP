@extends('layouts.app')
@section('title', __('home.home'))

@section('content')
<section class="content">
   <!-- Page level currency setting -->
    
	<input type="hidden" id="p_code" value="{{$currency_details->code}}">
	<input type="hidden" id="p_symbol" value="{{$currency_details->symbol}}">
	<input type="hidden" id="p_thousand" value="{{$currency_details->thousand_separator}}">
	<input type="hidden" id="p_decimal" value="{{$currency_details->decimal_separator}}">
<div class="row text-center" style="margin:0px 30%">
    @component("components.widget",["title" => "search" ,"class" => "box-primary",])
        <div class="row">
            <div class="col-xs-12">
                {!! Form::label('product_id', __('product.name') . ':') !!}  
                {!! Form::select('product_id', $products_list, null, ['class' => 'form-control select2' , "id" => "product_id" , 'placeholder' => __('messages.please_select')]); !!}
            </div>
        </div>
    @endcomponent
</div>
<div class="row text-center" style="margin:0px 5%">
        @component("components.widget",["class" => "box-second" ,"title" => "Result"])
                <div class="move text-center">
                </div>
        @endcomponent
</div>

</section>
@stop

@section("javascript")
 
<script>
    
        $(document).on("change","#product_id",function(){
                var id = $(this).val();
                $.ajax({
                    type: "GET",
                    url: "/get-move",
                    data: { 
                        id_product : id ,
                    },
                    success: function(response) {
                        // alert(JSON.stringify(response));
                        $(".move").html("");
                        $(".move").html(response);
                    }
                });// redirect to your page
             
        });
        
</script>
@endsection