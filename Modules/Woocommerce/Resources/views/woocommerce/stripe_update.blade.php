@extends('layouts.app')
@section('title', __('woocommerce::lang.stripe_edit'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('woocommerce::lang.stripe_edit')</h1>
</section>

 

<!-- Main content -->
<section class="content">
    {!! Form::open(['action' => ['\Modules\Woocommerce\Http\Controllers\WoocommerceController@updateStripeApi',"id" => $id], 'method' => 'post','files' => true]) !!}
    <div class="row">
        <div class="col-xs-12" style="padding:10px;  max-height:auto; margin:auto 30%;width:40%">
           
             @component('components.widget',['class'=>'  sections  ']) 
             <h4 style="text-align: center;color:#fff;background-color:#bc6d05d9;padding:10px" > @lang("Stripe Card")</h4>

                <div class="contents">
                    <div class="col-xs-12" style="padding:00px !important;"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="leftSection sec   form-group" style="padding:10px;">
                        {!! Form::label('api_public', __('Api Publishable Key') . ':') !!}
                        {!! Form::text("api_public", $stripe->api_public, ['class' => 'form-control','placeholder'=>__('messages.please_fill'),'style'=>'max-width:100%']); !!}
                        <br>
                        {!! Form::label('api_private', __('Api Secret Key') . ':') !!}
                        {!! Form::text("api_private", $stripe->api_private, ['class' => 'form-control','placeholder'=>__('messages.please_fill'),'style'=>'max-width:100%']); !!}
                        <br>
                        {!! Form::label('product_key', __('Product Key') . ':') !!}
                        {!! Form::text("product_key", $stripe->product_key, ['class' => 'form-control','placeholder'=>__('messages.please_fill'),'style'=>'max-width:100%']); !!}
                        <br>
                        {!! Form::label('url_website', __('Website Url ') . ':') !!}
                        {!! Form::text("url_website", $stripe->url_website, ['class' => 'form-control ','placeholder'=>__('https://example.com') ]); !!}
                     
                        <br>
                        <div class="form-group pull-right">
                            {{Form::submit('update', ['class'=>"btn btn-primary"])}}
                            </div>
                        </div>
                    </div>
                         
                        
                        
                    </div> 
                </div>
            @endcomponent
         
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