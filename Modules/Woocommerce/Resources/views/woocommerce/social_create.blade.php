@extends('layouts.app')
@section('title', __('woocommerce::lang.social_create'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('woocommerce::lang.social_create')</h1>
</section>

 

<!-- Main content -->
<section class="content">
    {!! Form::open(['action' => '\Modules\Woocommerce\Http\Controllers\WoocommerceController@saveSocial', 'method' => 'post','files' => true]) !!}
    <div class="row">
        <div class="col-xs-12" style="padding:10px;  max-height:auto;margin:auto 30% ; width:40% ">
           
             @component('components.widget',['class'=>'  sections box-primary' ,"style" => "background-color:#bc6d05d9; !important"]) 
                <h4 style="text-align: center;color:#fff;background-color:#bc6d05d9;padding:10px" > @lang("Social Media Card")</h4>
                <div class="contents">
                    <input type="text" name="style" hidden value="{{$style}}">
                    <div class="col-xs-12" style="padding:00px !important;"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="leftSection sec   form-group" style="padding:10px;">
                        {!! Form::text("title", null, ['class' => 'form-control','placeholder'=>__('messages.please_enter_title'),'style'=>'max-width:100%']); !!}
                        <br>
                        {!! Form::text("link",null, ['class' => 'form-control','id'=>'description','placeholder'=>__('Link or Mobile')]); !!}
                        
                        </div></div><div class="col-lg-12 col-md-12  col-sm-12 col-xs-12"><div class="leftSection images pull-right" style="max-width:100%;">
                        {!! Form::label('icon', __('lang_v1.product_image') . ':') !!}
                        {!! Form::file('icon', ['id' => 'upload_image',"accept" => "image/*"]); !!}
                        <small><p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)]) <br> @lang('lang_v1.aspect_ratio_should_be_1_1')</p></small></div></div>
                        </div> 
                </div>
            @endcomponent
         
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="form-group pull-right">
            {{Form::submit('save', ['class'=>"btn btn-danger"])}}
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</section>
@stop
@section('javascript')
<script type="text/javascript">
              
        var img_fileinput_setting = {
            showUpload: false,
            showPreview: true,
            browseLabel: LANG.file_browse_label,
            removeLabel: LANG.remove,
            previewSettings: {
                image: { width: 'auto', height: 'auto', 'max-width': '100%', 'max-height': '100%' },
            },
        };

        var i     = 1;
        var j     = 1;
     
        $(".images").each(function(){
            var e     = $(this);
            var image = "#upload_image" ;
            var img   = e.find(image);
            img.fileinput(img_fileinput_setting);
            
        });

     
        
</script>
@endsection