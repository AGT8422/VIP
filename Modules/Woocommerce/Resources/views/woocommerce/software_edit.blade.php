@extends('layouts.app')
@section('title', __('woocommerce::lang.software_create'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('woocommerce::lang.software_create')</h1>
</section>

 

<!-- Main content -->
<section class="content">
    {!! Form::open(['action' => ['\Modules\Woocommerce\Http\Controllers\WoocommerceController@softwareUpdate','id'=>$software->id] , 'method' => 'post','files' => true]) !!}
    <div class="row">
        <div class="col-xs-12" style="padding:10px;  max-height:auto; ">
           <!--  <pos-tab-container> -->
             @component('components.widget',['class'=>'  sections box-primary']) 
                <div class="contents">
                    
                    <div class="col-xs-12" style="padding:20px !important;"><div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"><div class="leftSection sec   form-group" style="padding:10px;">
                        {!! Form::text("name" , $software->name??null, ['class' => 'form-control' ,'required','placeholder'=>__('messages.name_section')."*",'style'=>'max-width:50%;font-size:20px !important;font-weight:bold !important']); !!}
                        {!! Form::text("title", $software->title??null, ['class' => 'form-control','placeholder'=>__('messages.please_enter_title'),'style'=>'max-width:50%']); !!}
                        <br>
                        {!! Form::textarea("description",$software->description??null, ['class' => 'form-control','id'=>'description','placeholder'=>__('messages.please_enter_desc')]); !!}
                        <br>
                        {!! Form::text("button", $software->button??null, ['class' => 'form-control','placeholder'=>__('messages.please_enter_btn_desc'),'style'=>'max-width:50%']); !!}
                        <br>
                        </div></div><div class="col-lg-6 col-md-6  col-sm-6 col-xs-6"><div class="leftSection images pull-right" style="max-width:100%;">
                        {!! Form::label('image', __('lang_v1.product_image') . ':') !!}
                        {!! Form::file('image', ['id' => 'upload_image',"accept" => "image/*"]); !!}
                        <small><p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)]) <br> @lang('lang_v1.aspect_ratio_should_be_1_1')</p></small></div></div>
                        @if($software->image != null)  <img src="{{$software->image_url}}"  width=200 height=200> @else  @endif
                        <br>
                        {!! Form::label('topSection', __('Use In Top Section') . ':') !!}
                        {!! Form::checkbox("topSection", 1,($software->topSection)?1:0, [   'style'=>'max-width:50%']); !!}
                        <br>
                        </div> 
                    </div>
                    <div class="form-group pull-right">
                        {{Form::submit('update', ['class'=>"btn btn-primary"])}}
                    </div>
            @endcomponent
            {{-- <div class="btn btn-primary add" onClick="addSection();">
                    add Section
            </div> --}}
            <!--  </pos-tab-container> -->
        </div>
    </div>
  
    {!! Form::close() !!}
</section>
@stop
@section('javascript')
<script type="text/javascript">
        function addSection(){
            var count = $(".count").val();
            $html     = ' <div class="col-xs-12   " style="padding:20px !important;"><div class="col-lg-6 col-md-6 col-sm-6 col-xs-6  "><div class="leftSection sec   form-group" style="padding:10px;">'+
                                '{!! Form::text("name[]", null, ['class' => 'form-control' ,'required','placeholder'=>__('messages.name_section')."*",'style'=>'max-width:50%;font-size:20px !important;font-weight:bold !important']); !!}'+
                                '<br><input type="text" class="current" hidden value="'+count+'">'+
                                '{!! Form::text("title[]", null, ['class' => 'form-control','placeholder'=>__('messages.please_enter_title'),'style'=>'max-width:50%']); !!}'+
                                "<br>"+
                                '{!! Form::textarea("description[]",null, ['class' => 'form-control','id'=>'description','placeholder'=>__('messages.please_enter_desc')]); !!}'+
                                "<br>"+
                                '{!! Form::text("button[]", null, ['class' => 'form-control','placeholder'=>__('messages.please_enter_btn_desc'),'style'=>'max-width:50%']); !!}'+
                                "<br>"+
                                '</div></div><div class="col-lg-6 col-md-6  col-sm-6 col-xs-6"><div class="leftSection images pull-right" style="max-width:50%; ">'+
                                '{!! Form::label('image', __('lang_v1.product_image') . ':') !!}'+
                                '{!! Form::file('image[]', ['id' => 'upload_image',"accept" => "image/*"]); !!}'+
                                '<small><p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)]) <br> @lang('lang_v1.aspect_ratio_should_be_1_1')</p></small></div></div>'+
                                '</div> ';
            count = parseFloat(count) + 1;
            $(".count").val(count);         
            $(".sections").removeClass("hide");
            // alert($(".count").val());
            $(".contents").append($html);
        }
                //     '{!! Form::checkbox("view[]",    1  , 0  , [ 'class' => 'check input-icheck','data-id'=>'data[]']); !!} {{ __( 'lang_v1.View In Ecommerce' ) }}'+
                 
        $(".view-in-ecommerce").each(function(){
                var  e   = $(this); 
                var line = e.data("line_id");
                e.on('change', function() {
                    if(!e.attr("checked")){
                        e.attr("checked",true)
                    }else{
                        e.attr("checked",false);
                        $.ajax({
                            url: "/woocommerce/sections/dont-view-in-commerce?id="+line,
                            method: 'get',
                            dataType: 'html',
                            success: function(result) {
                                if (JSON.parse(result).success == true) {
                                    toastr.success(JSON.parse(result).msg);
                                } else {
                                    toastr.error(JSON.parse(result).msg);
                                }
                            },
                        });
                        
                    }
                    if(e.attr("checked")){
                        $.ajax({
                            url: "/woocommerce/sections/view-in-commerce?id="+line,
                            method: 'get',
                            dataType: 'html',
                            success: function(result) {
                                if (JSON.parse(result).success == true) {
                                    toastr.success(JSON.parse(result).msg);
                                } else {
                                    toastr.error(JSON.parse(result).msg);
                                }
                            },
                        });
                    }
                });
        
        });
        $(".add-as-about_us").each(function(){
                    var  e   = $(this); 
                    var line = e.data("line_id");
                    e.on('change', function() {
                        if(!e.attr("checked")){
                            e.attr("checked",true)
                        }else{
                            e.attr("checked",false);
                            $.ajax({
                                url: "/woocommerce/sections/dont-view-in-commerce?Style=About_us&id="+line,
                                method: 'get',
                                dataType: 'html',
                                success: function(result) {
                                    if (JSON.parse(result).success == true) {
                                        toastr.success(JSON.parse(result).msg);
                                    } else {
                                        toastr.error(JSON.parse(result).msg);
                                    }
                                },
                            });
                            
                        }
                        if(e.attr("checked")){
                            $.ajax({
                                url: "/woocommerce/sections/view-in-commerce?Style=About_us&id="+line,
                                method: 'get',
                                dataType: 'html',
                                success: function(result) {
                                    if (JSON.parse(result).success == true) {
                                        toastr.success(JSON.parse(result).msg);
                                    } else {
                                        toastr.error(JSON.parse(result).msg);
                                    }
                                },
                            });
                        }
                    });
            
            });

                

        var img_fileinput_setting = {
            showUpload: false,
            showPreview: true,
            browseLabel: LANG.file_browse_label,
            removeLabel: LANG.remove,
            previewSettings: {
                image: { width: 'auto', height: 'auto', 'max-width': '100%', 'max-height': '100%' },
            },
        };
        console.log($('#table_section').html());
        table_section = $('#table_section').DataTable({
            processing:true,
            serverSide:true,
            scrollY:  "75vh",
            scrollX:  true,
            scrollCollapse: true, 
            "ajax": {
                "url": "/sections/all",
                "data": function ( d ) {
                    d.name         = $('.section_name').val();
                    d              = __datatable_ajax_callback(d);
                }
            },
            columns: [
                { data: 'action', name: 'action'},
                { data: 'image'  , name: 'image'  ,class: 'img_section' },
                { data: 'name'  , name: 'name'  },
                { data: 'title'  , name: 'title'      },
                { data: 'description'  , name: 'description' , class: 'desc_section' },
                { data: 'button'  , name: 'button'  , class: 'btn_section'   },
                { data: 'type'  , name: 'type'   , class:"type"    },
            ],
            fnDrawCallback: function(oSettings) {
                __currency_convert_recursively($('#table_section'));
            },
        });

        var i     = 1;
        var j     = 1;
        $(".sec").each(function(){
            var e     = $(this);
            var desc  = "#description"+i;
            var ds    = "description"+i;
            var child = e.children().find(desc);
            init_tinymce(ds);
            i++;
        });
        $(".images").each(function(){
            var e     = $(this);
            var image = "#upload_image"+j;
            var img   = e.find(image);
            img.fileinput(img_fileinput_setting);
            j++;
        });

        var ds_only    = "description";
        init_tinymce(ds_only);
        var img_only    = "#upload_image";
        var img_on      = $(".images").find(img_only);
        img_on.fileinput(img_fileinput_setting);
        
</script>
@endsection