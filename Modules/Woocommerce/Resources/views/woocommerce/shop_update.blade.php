@extends('layouts.app')
@section('title', __('woocommerce::lang.shop_edit'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('woocommerce::lang.shop_edit')</h1>
</section>

 

<!-- Main content -->
<section class="content">
    {!! Form::open(['action' => ['\Modules\Woocommerce\Http\Controllers\WoocommerceController@updateShop',"id" => $id], 'method' => 'post','files' => true]) !!}
    <div class="row">
        <div class="col-xs-12" style="padding:10px;  max-height:auto; margin:auto 30%;width:40%">
           
             @component('components.widget',['class'=>'  sections']) 
             <h4 style="text-align: center;color:#fff;background-color:#bc6d05d9;padding:10px" > @lang("Shop By Category Card")</h4>

                <div class="contents">
                    <div class="col-xs-12" style="padding:00px !important;"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="leftSection sec   form-group" style="padding:10px;">
                        {!! Form::label('name', __('Name') . ':') !!}
                        {!! Form::text("name", $shop->name, ['class' => 'form-control','placeholder'=>__('Name'),'style'=>'max-width:100%']); !!}
                        <br>
                        {!! Form::label('category_id', __('Category ') . ':') !!}
                         
                        {!! Form::select("category_id",$list_category,($shop->category_id)?$shop->category_id:null, ['class' => 'form-control select2' ]); !!}
                     
                        <br>
                        </div>
                    </div>
                        <div class="col-lg-12 col-md-12  col-sm-12 col-xs-12">
                            <div class="leftSection images pull-right" style="max-width:100%;">
                                {!! Form::label('icon', __('lang_v1.product_image') . ':') !!}
                                {!! Form::file('icon', ['id' => 'upload_image',"accept" => "image/*"]); !!}
                                <small>
                                    <p class="help-block">
                                        @lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)]) <br> 
                                        @lang('lang_v1.aspect_ratio_should_be_1_1')
                                    </p>
                                </small>
                            </div>
                            <br>
                            @if($shop->icon_url != null || $shop->icon_url != "" )
                                <div class="col-lg-12 text-center">
                                    <img src="{{$shop->icon_url}}" width=80% height=20% style="border-radius:10px;border:2px solid black;padding: 2px" />
                                </div>
                            @endif
                            
                        </div>
                        
                        <div class="form-group pull-right">
                            {{Form::submit('update', ['class'=>"btn btn-primary"])}}
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