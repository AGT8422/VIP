@extends('layouts.app')
@section('title', __('home.home'))

@section('content')
<section class="content">
    <div class="row text-center" style="margin:0px 30%">
    @component("components.widget",["class" => "box-primary","title" => "search"])
        <div class="row">
            <div class="col-xs-12">
                {!! Form::label('account_id', __('account.account_name') . ':') !!}  
                {!! Form::select('account_id', $account_list, null, ['class' => 'form-control select2' , "id" => "account_id" , 'placeholder' => __('messages.please_select')]); !!}
            </div>
        </div>
    @endcomponent
    
</section>
<div class="row text-center" style="margin:0px 10%">
    @component("components.widget",["class" => "box-pr" ,"title" => "Result"])
            <div class="ledge text-center">
            </div>
    @endcomponent
</div>
@stop

@section("javascript")
<script>
    
        $(document).on("change","#account_id",function(){
                var id = $(this).val();
                $.ajax({
                    type: "GET",
                    url: "/account/get-account",
                    data: { 
                        id_account : id ,
                    },
                    success: function(response) {
                        // alert(JSON.stringify(response));
                        $(".ledge").html("");
                        $(".ledge").html(response);
                    }
                });// redirect to your page
             
        });
        
</script>
@endsection