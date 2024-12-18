@extends('layouts.app')
@section('title', __('woocommerce::lang.contactus_create'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('woocommerce::lang.contactus_create')</h1>
</section>

<!-- Main content -->
<section class="content">
    {!! Form::open(['action' => '\Modules\Woocommerce\Http\Controllers\WoocommerceController@saveContact', 'method' => 'post','files' => true]) !!}
    <div class="row">
        <div class="col-xs-12" style="padding:10px;  max-height:auto;margin:auto 30% ;width:40% ">
           <!--  <pos-tab-container> -->
            {{-- @php $i=0; @endphp --}}
            {{-- @if(count($allData)>0)
                @php $i=1; @endphp
                @foreach($allData as $key => $item)
                    @component("components.widget",["class"=>"box-primary"])
                        <div class="col-xs-12   " style="padding:20px !important;">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6  ">
                                <div class="leftSection sec   form-group" style="padding:10px;">
                                    {{-- {!! Form::text('name[]', $item->name, ['class' => 'form-control',"placeholder"=>__("messages.name_section"),"style"=>"max-width:50%;font-size:20px !important;font-weight:bold !important"]); !!}
                                    <br> --}}
                                    {{-- {!! Form::text('title[]', $item->title, ['class' => 'form-control',"placeholder"=>__("messages.please_enter_title"),"style"=>"max-width:50%"]); !!}
                                    {!! Form::hidden('line_id[]', $item->id, ['class' => 'form-control',"placeholder"=>__("messages.please_enter_title"),"style"=>"max-width:50%"]); !!} --}}
                                    {{-- <br>
                                    {!! Form::text('mobile[]', $item->mobile, ['class' => 'form-control',"placeholder"=>__("messages.enter_mobile"),"style"=>"max-width:50%"]); !!} --}}
                                    {{-- {!! Form::textarea('description[]', $item->desc, ['class' => 'form-control',"id"=>"description".$i,"placeholder"=>__("messages.please_enter_desc")]); !!} --}}
                                    {{-- <br> --}}
                                    {{-- {!! Form::text('button[]', $item->button, ['class' => 'form-control',"placeholder"=>__("messages.please_enter_btn_desc"),"style"=>"max-width:50%"]); !!} --}}  
                                    {{-- <br> --}}
                                    {{-- <input type="checkbox" data-line_id="{{$item->id}}" class="view-in-ecommerce" @if($item->view != 0) checked @endif     /> {{ __( 'lang_v1.View In Ecommerce' ) }}<br> --}}
                                    {{-- <input type="checkbox" data-line_id="{{$item->id}}" class="add-as-about_us" @if($item->about_us != 0) checked @endif     /> {{ __( 'lang_v1.Add as About-us' ) }} --}}
                                {{-- </div>
                            </div> --}}
                            {{-- <div class="col-lg-6 col-md-6  col-sm-6 col-xs-6  ">
                                <div class="leftSection images pull-right" style="max-width:50%; ">
                                        {!! Form::label('image', __('lang_v1.product_image') . ':') !!}
                                        {!! Form::file('image[]', ['id' => 'upload_image'.$i,'accept' => 'image/*']); !!}
                                        <small><p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)]) <br> @lang('lang_v1.aspect_ratio_should_be_1_1')</p></small>
                                        <br>
                                        <img width="250px" height="200px" src="{{$item->image_url}}" alt="Section image">
                                </div>
                            </div> --}}
                        {{-- </div>
                    @endcomponent
                    @php $i++ @endphp
                @endforeach
            @endif --}}
            {{-- <input type="text" class="count" hidden value="{{$i}}">
            @component('components.widget',['class'=>'hide sections box-primary']) 
                <div class="contents">

                </div>
            @endcomponent --}}
             
            {{-- *********3************ --}}
            @component('components.widget',['class'=>' sections box-primary' ])
            <h4 style="text-align: center;color:#fff;background-color:#bc6d05d9;padding:10px" > @lang("Contact Us Card")</h4>
 
            <div class="row">
                    <div class="col-lg-12 col-md-12  col-sm-12 col-xs-12">
                                {!! Form::text('title', null, ['class' => 'form-control',"placeholder"=>__("messages.please_enter_title"),"style"=>"max-width:100%;font-size:20px !important;font-weight:bold !important"]); !!}
                                <br>  
                                {!! Form::text('links', null, ['class' => 'form-control hide',"placeholder"=>__("Link"),"style"=>"max-width:100%"]); !!}  
                                <br>
                                {!! Form::text('mobile', null, ['class' => 'form-control',"placeholder"=>__("messages.enter_mobile"),"style"=>"max-width:100%"]); !!}  
                                <br>
                                {!! Form::text('address', null, ['class' => 'form-control hide',"placeholder"=>__("Address"),"style"=>"max-width:100%"]); !!}
                                <br>
                                {!! Form::text('additional_info', null, ['class' => 'form-control hide',"id"=>"additional_info","style"=>"max-width:100%","placeholder"=>__("Additional Note")]); !!}                                 
                    </div>
                    <div class="col-lg-12 col-md-12  col-sm-12 col-xs-12">
                        <div class="leftSection images pull-right" style="max-width:100%;">
                            {!! Form::label('icon', __('Icon') . ':') !!}
                            {!! Form::file('icon', ['id' => 'upload_image',"accept" => "image/*"]); !!}
                            <small><p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)]) <br> @lang('lang_v1.aspect_ratio_should_be_1_1')</p></small>
                        </div>
                    </div>
            </div>
            @endcomponent
         
            {{-- <div class="btn btn-primary add" onClick="addSection();">
                    add Section
            </div> --}}

            <!--  </pos-tab-container> -->
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
        function addSection(){
        var count = $(".count").val();
        $html     = ' <div class="col-xs-12   " style="padding:20px !important;"><div class="col-lg-6 col-md-6 col-sm-6 col-xs-6  "><div class="leftSection sec   form-group" style="padding:10px;">'+
                            '{!! Form::text("title[]", null, ['class' => 'form-control','placeholder'=>__('messages.please_enter_title'),'style'=>'max-width:50%']); !!}'+
                            '<br><input type="text" class="current" hidden value="'+count+'">'+
                            '{!! Form::text("mobile[]", null, ['class' => 'form-control' ,'required','placeholder'=>__('messages.enter_mobile')."*",'style'=>'max-width:50%;font-size:16px !important;font-weight:400 !important']); !!}'+
                            "<br>"+
                            // '{!! Form::textarea("description[]",null, ['class' => 'form-control','id'=>'description','placeholder'=>__('messages.please_enter_desc')]); !!}'+
                            // "<br>"+
                            // '{!! Form::text("button[]", null, ['class' => 'form-control','placeholder'=>__('messages.please_enter_btn_desc'),'style'=>'max-width:50%']); !!}'+
                            // "<br>"+
                            // '</div></div><div class="col-lg-6 col-md-6  col-sm-6 col-xs-6"><div class="leftSection images pull-right" style="max-width:50%; ">'+
                            // '{!! Form::label('image', __('lang_v1.product_image') . ':') !!}'+
                            // '{!! Form::file('image[]', ['id' => 'upload_image',"accept" => "image/*"]); !!}'+
                            // '<small><p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)]) <br> @lang('lang_v1.aspect_ratio_should_be_1_1')</p></small></div></div>'+
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

        
        table_contact_us = $('#table_contact_us').DataTable({
            processing:true,
            serverSide:true,
            scrollY:  "75vh",
            scrollX:  true,
            scrollCollapse: true, 
            "ajax": {
                "url": "/woocommerce/sections/contact-us/all",
                "data": function ( d ) {
                    d.check        = "Top";
                    d              = __datatable_ajax_callback(d);
                }
            },
            columns: [
                { data: 'action', name: 'action'},
                { data: 'icon'  , name: 'icon'  ,class: 'img_section' },
                { data: 'title'  , name: 'title'      },
                { data: 'mobile'  , name: 'mobile'  },
                { data: 'links'   , name: 'links' , class: 'desc_section' },
                { data: 'address'  , name: 'address'  , class: 'btn_section'   },
                { data: 'additional_note'  , name: 'additional_note'  , class: 'btn_section'   },
                { data: 'type'  , name: 'type'   , class:"type"    },
            ],
            fnDrawCallback: function(oSettings) {
                __currency_convert_recursively($('#table_contact_us'));
            },
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
        var image_only = "#upload_image";
        var img        = $(".images").find(image_only);
        img.fileinput(img_fileinput_setting);

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


</script>
@endsection