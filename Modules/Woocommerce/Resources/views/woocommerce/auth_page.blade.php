@extends('layouts.app')
@section('title', __('woocommerce::lang.auth_page'))

 
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('woocommerce::lang.auth_page')</h1>
</section>

<style>
    .desc_section {
            width:40%;
    }
    .img_section {
            width:10%;
    }
    .btn_section {
            width:10%;
    }
</style>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12" style="padding:10px;  max-height:auto; ">
            {{-- *********0************ --}}
            {!! Form::open(['action' => ['\Modules\Woocommerce\Http\Controllers\WoocommerceController@updateAuthImage'], 'method' => 'post','files' => true]) !!}
            @component('components.widget',['class'=>' sections box-primary',"title"=>__('Auth Information')]) 
                <div class="tab">
                     <div class="row">
                        <div class="col-lg-6 ">
                            <div class="content" style="width:100%">
                                <h1 style="font-size: 20px;font-weight:bold; text-transform: capitalize"> {{" Login Picture"}}</h1>         <br>
                                <div class="col-sm-10">
                                    <div class="form-group">
                                        <img width="100%" @if($login) src="{{$login->auth_url}}" @else src="" @endif >
                                        {!! Form::label('login', __('lang_v1.product_image') . ':') !!}
                                        {!! Form::file('login', ['id' => 'upload_image', 'accept' => 'image/*']); !!}
                                        <small><p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)]). @lang('lang_v1.aspect_ratio_should_be_1_1') @if(!empty($login->image)) <br> @lang('lang_v1.previous_image_will_be_replaced') @endif</p></small>
                                        </div>
                                    </div><br>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 ">
                            <div class="content" style="width:100%">
                                <h1 style="font-size: 20px;font-weight:bold; text-transform: capitalize"> {{" Signup Picture"}}</h1>         <br>
                                <div class="col-sm-10">
                                    <div class="form-group">
                                        <img width="100%" @if($signup) src="{{$signup->auth_url}}" @else src="" @endif >
                                        {!! Form::label('signup', __('lang_v1.product_image') . ':') !!}
                                        {!! Form::file('signup', ['id' => 'upload_image_1', 'accept' => 'image/*']); !!}
                                        <small><p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)]). @lang('lang_v1.aspect_ratio_should_be_1_1') @if(!empty($signup->image)) <br> @lang('lang_v1.previous_image_will_be_replaced') @endif</p></small>
                                        
                                        </div>
                                    </div><br>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group pull-right">
                        {{Form::submit('update', ['class'=>"btn btn-primary"])}}
                    </div>
                </div>
            @endcomponent
            
        </div>
    </div>
</section>
<!-- End Main Content -->
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
            $('#upload_image').fileinput(img_fileinput_setting);
            $('#upload_image_1').fileinput(img_fileinput_setting);
            if ($('#top_description').length) {
                init_tinymce('top_description');
            }
        // function addSection(){
        //     var count = $(".count").val();
        //     $html     = ' <div class="col-xs-12   " style="padding:20px !important;"><div class="col-lg-6 col-md-6 col-sm-6 col-xs-6  "><div class="leftSection sec   form-group" style="padding:10px;">'+
        //                         '{!! Form::text("name[]", null, ['class' => 'form-control' ,'required','placeholder'=>__('messages.name_section')."*",'style'=>'max-width:50%;font-size:20px !important;font-weight:bold !important']); !!}'+
        //                         '<br><input type="text" class="current" hidden value="'+count+'">'+
        //                         '{!! Form::text("title[]", null, ['class' => 'form-control','placeholder'=>__('messages.please_enter_title'),'style'=>'max-width:50%']); !!}'+
        //                         "<br>"+
        //                         '{!! Form::textarea("description[]",null, ['class' => 'form-control','id'=>'description','placeholder'=>__('messages.please_enter_desc')]); !!}'+
        //                         "<br>"+
        //                         '{!! Form::text("button[]", null, ['class' => 'form-control','placeholder'=>__('messages.please_enter_btn_desc'),'style'=>'max-width:50%']); !!}'+
        //                         "<br>"+
        //                         '</div></div><div class="col-lg-6 col-md-6  col-sm-6 col-xs-6"><div class="leftSection images pull-right" style="max-width:50%; ">'+
        //                         '{!! Form::label('image', __('lang_v1.product_image') . ':') !!}'+
        //                         '{!! Form::file('image[]', ['id' => 'upload_image',"accept" => "image/*"]); !!}'+
        //                         '<small><p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)]) <br> @lang('lang_v1.aspect_ratio_should_be_1_1')</p></small></div></div>'+
        //                         '</div> ';
        //     count = parseFloat(count) + 1;
        //     $(".count").val(count);         
        //     $(".sections").removeClass("hide");
        //     // alert($(".count").val());
        //     $(".contents").append($html);
        // }
                  
        // $(".view-in-ecommerce").each(function(){
        //         var  e   = $(this); 
        //         var line = e.data("line_id");
        //         e.on('change', function() {
        //             if(!e.attr("checked")){
        //                 e.attr("checked",true)
        //             }else{
        //                 e.attr("checked",false);
        //                 $.ajax({
        //                     url: "/woocommerce/sections/dont-view-in-commerce?id="+line,
        //                     method: 'get',
        //                     dataType: 'html',
        //                     success: function(result) {
        //                         if (JSON.parse(result).success == true) {
        //                             toastr.success(JSON.parse(result).msg);
        //                         } else {
        //                             toastr.error(JSON.parse(result).msg);
        //                         }
        //                     },
        //                 });
                        
        //             }
        //             if(e.attr("checked")){
        //                 $.ajax({
        //                     url: "/woocommerce/sections/view-in-commerce?id="+line,
        //                     method: 'get',
        //                     dataType: 'html',
        //                     success: function(result) {
        //                         if (JSON.parse(result).success == true) {
        //                             toastr.success(JSON.parse(result).msg);
        //                         } else {
        //                             toastr.error(JSON.parse(result).msg);
        //                         }
        //                     },
        //                 });
        //             }
        //         });
        
        // });
        // $(".add-as-about_us").each(function(){
        //             var  e   = $(this); 
        //             var line = e.data("line_id");
        //             e.on('change', function() {
        //                 if(!e.attr("checked")){
        //                     e.attr("checked",true)
        //                 }else{
        //                     e.attr("checked",false);
        //                     $.ajax({
        //                         url: "/woocommerce/sections/dont-view-in-commerce?Style=About_us&id="+line,
        //                         method: 'get',
        //                         dataType: 'html',
        //                         success: function(result) {
        //                             if (JSON.parse(result).success == true) {
        //                                 toastr.success(JSON.parse(result).msg);
        //                             } else {
        //                                 toastr.error(JSON.parse(result).msg);
        //                             }
        //                         },
        //                     });
                            
        //                 }
        //                 if(e.attr("checked")){
        //                     $.ajax({
        //                         url: "/woocommerce/sections/view-in-commerce?Style=About_us&id="+line,
        //                         method: 'get',
        //                         dataType: 'html',
        //                         success: function(result) {
        //                             if (JSON.parse(result).success == true) {
        //                                 toastr.success(JSON.parse(result).msg);
        //                             } else {
        //                                 toastr.error(JSON.parse(result).msg);
        //                             }
        //                         },
        //                     });
        //                 }
        //             });
            
        //     });

                

        
        //     var img_fileinput_setting = {
        //     showUpload: false,
        //     showPreview: true,
        //     browseLabel: LANG.file_browse_label,
        //     removeLabel: LANG.remove,
        //     previewSettings: {
        //         image: { width: 'auto', height: 'auto', 'max-width': '100%', 'max-height': '100%' },
        //     },
        // };

        // table_section = $('#table_section').DataTable({
        //     processing:true,
        //     serverSide:true,
        //     scrollY:  "75vh",
        //     scrollX:  true,
        //     scrollCollapse: true, 
        //     "ajax": {
        //         "url": "/woocommerce/sections/all",
        //         "data": function ( d ) {
        //             d.check        = "Top";
        //             d              = __datatable_ajax_callback(d);
        //         }
        //     },
        //     columns: [
        //         { data: 'action', name: 'action'},
        //         { data: 'image'  , name: 'image'  ,class: 'img_section' },
        //         { data: 'name'  , name: 'name'  },
        //         { data: 'title'  , name: 'title'      },
        //         { data: 'description'  , name: 'description' , class: 'desc_section' },
        //         { data: 'button'  , name: 'button'  , class: 'btn_section'   },
        //         { data: 'type'  , name: 'type'   , class:"type"    },
        //     ],
        //     fnDrawCallback: function(oSettings) {
        //         __currency_convert_recursively($('#table_section'));
        //     },
        // });
        // table_section_about = $('#table_section_about').DataTable({
        //     processing:true,
        //     serverSide:true,
        //     scrollY:  "75vh",
        //     scrollX:  true,
        //     scrollCollapse: true, 
        //     "ajax": {
        //         "url": "/woocommerce/sections/all",
        //         "data": function ( d ) {
        //             d.check        = "about";
        //             d              = __datatable_ajax_callback(d);
        //         }
        //     },
        //     columns: [
        //         { data: 'action', name: 'action'},
        //         { data: 'image'  , name: 'image'  ,class: 'img_section' },
        //         { data: 'name'  , name: 'name'  },
        //         { data: 'title'  , name: 'title'      },
        //         { data: 'description'  , name: 'description' , class: 'desc_section' },
        //         { data: 'button'  , name: 'button'  , class: 'btn_section'   },
        //         { data: 'type'  , name: 'type'   , class:"type"    },
        //     ],
        //     fnDrawCallback: function(oSettings) {
        //         __currency_convert_recursively($('#table_section_about'));
        //     },
        // });
        // table_section_auth = $('#table_section_auth').DataTable({
        //     processing:true,
        //     serverSide:true,
        //     scrollY:  "75vh",
        //     scrollX:  true,
        //     scrollCollapse: true, 
        //     "ajax": {
        //         "url": "/woocommerce/sections/all",
        //         "data": function ( d ) {
        //             d.check        = "signup";
        //             d              = __datatable_ajax_callback(d);
        //         }
        //     },
        //     columns: [
        //         { data: 'action', name: 'action'},
        //         { data: 'image'  , name: 'image'  ,class: 'img_section' },
        //         { data: 'name'  , name: 'name'  },
        //         { data: 'title'  , name: 'title'      },
        //         { data: 'description'  , name: 'description' , class: 'desc_section' },
        //         { data: 'button'  , name: 'button'  , class: 'btn_section'   },
        //         { data: 'type'  , name: 'type'   , class:"type"    },
        //     ],
        //     fnDrawCallback: function(oSettings) {
        //         __currency_convert_recursively($('#table_section_auth'));
        //     },
        // });
        // table_section_store = $('#table_section_store').DataTable({
        //     processing:true,
        //     serverSide:true,
        //     scrollY:  "75vh",
        //     scrollX:  true,
        //     scrollCollapse: true, 
        //     "ajax": {
        //         "url": "/woocommerce/sections/all",
        //         "data": function ( d ) {
        //             d.check        = "store";
        //             d              = __datatable_ajax_callback(d);
        //         }
        //     },
        //     columns: [
        //         { data: 'action', name: 'action'},
        //         { data: 'image'  , name: 'image'  ,class: 'img_section' },
        //         { data: 'name'  , name: 'name'  },
        //         { data: 'title'  , name: 'title'      },
        //         { data: 'description'  , name: 'description' , class: 'desc_section' },
        //         { data: 'button'  , name: 'button'  , class: 'btn_section'   },
        //         { data: 'type'  , name: 'type'   , class:"type"    },
        //     ],
        //     fnDrawCallback: function(oSettings) {
        //         __currency_convert_recursively($('#table_section_store'));
        //     },
        // });


        // var i     = 1;
        // var j     = 1;
        // $(".sec").each(function(){
        //     var e     = $(this);
        //     var desc  = "#description"+i;
        //     var ds    = "description"+i;
        //     var child = e.children().find(desc);
        //     init_tinymce(ds);
        //     i++;
        // });
        // $(".images").each(function(){
        //     var e     = $(this);
        //     var image = "#upload_image"+j;
        //     var img   = e.find(image);
        //     img.fileinput(img_fileinput_setting);
        //     j++;
        // });

        // $(document).on('click', 'a.delete-section', function(e){
        //     e.preventDefault();
        //     swal({
        //         title: LANG.sure,
        //         icon: "warning",
        //         buttons: true,
        //         dangerMode: true,
        //     }).then((willDelete) => {
        //         if (willDelete) {
        //             var href = $(this).attr('href');
        //             $.ajax({
        //                 method: "POST",
        //                 url: href,
        //                 dataType: "json",
        //                 success: function(result){
        //                     if(result.success == true){
        //                         toastr.success(result.msg);
        //                         table_section.ajax.reload();
        //                     } else {
        //                         toastr.error(result.msg);
        //                     }
        //                 }
        //             });
        //         }
        //     });
        // });
       
</script>
 
@endsection