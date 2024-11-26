@extends('layouts.app')
@section('title', __('woocommerce::lang.software_page'))

 
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('woocommerce::lang.software_page')</h1>
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
    {!! Form::open(['action' => ['\Modules\Woocommerce\Http\Controllers\WoocommerceController@updateSoftwareTop',"id" => $software->id], 'method' => 'post','files' => true]) !!}
    <div class="row">
        <div class="col-xs-12" style="padding:10px;  max-height:auto; ">
            {{-- *********0************ --}}
            @component('components.widget',['class'=>' sections box-primary',"title"=>__('Top Section Information')]) 
                <div class="tab">
                     <div class="row">
                        <div class="col-lg-6 ">
                            <div class="content" style="width:100%">
                                <h1 style="font-size: 20px;font-weight:bold; text-transform: capitalize"> {{"Pictures"}}</h1>         <br>
                                <div class="col-sm-10">
                                    <div class="form-group">
                                        <img width="100%" src="{{$software->image_url}}">
                                        {!! Form::label('image', __('lang_v1.product_image') . ':') !!}
                                        {!! Form::file('image', ['id' => 'upload_image', 'accept' => 'image/*']); !!}
                                        <small><p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)]). @lang('lang_v1.aspect_ratio_should_be_1_1') @if(!empty($product->image)) <br> @lang('lang_v1.previous_image_will_be_replaced') @endif</p></small>
                                        
                                        </div>
                                    </div><br>
                                </div>
                            </div>
                        <div class="col-lg-6 ">
                          <br>
                            <h1 style="font-size: 20px;font-weight:bold; text-transform: capitalize"> {{$software->name}}</h1>         <br>
                            <span style="font-size: 18px; "> 
                                &nbsp; &nbsp; Title :   
                            </span> 
                          
                            <div class="col-sm-12">
                                <div class="form-group">
                                {!! Form::text('top_name', $software->title, ['class' => 'form-control', 'required',
                                  'placeholder' => __('Software Section Title ')]); !!}
                                </div>
                            </div>
                            <br>
                            <span style="font-size: 18px; "> 
                                &nbsp; &nbsp; Description :   
                            </span> 
                          
                            <div class="col-sm-12">
                                <div class="form-group">
                                  {!! Form::textarea('top_description', $software->description, ['class' => 'form-control','id' => 'top_description']); !!}
                                </div>
                              </div>
                             
                            <br>
                        </div>
                        </div>
                    </div>
            @endcomponent
            
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12" style="padding:10px;  max-height:auto; ">
            {{-- *********1************ --}}
            @component('components.widget',['class'=>' sections box-primary',"title"=>__('Slider Images')]) 
                <div class="tab">
                    <div class="row text-center">
                    <div class="col-md-12"  >    
                        <h1 style="text-align: center">{{" MORE IMAGES "}} </h1>
                           
                        <div class="content" style="display:flex"  >
                            
                        @php $images =  json_decode($software->alter_image); @endphp
                        @foreach($software->alter_image_url as $key => $slider)
                            <div class="image_section" style="border:1px solid black;border-radius:3px;padding:10px;margin:5px;position:relative;">
                                 <span class="delete-more-image"  data-url="{{$images[$key]}}" style="position:absolute;left:-10px;bottom:-10px;padding:2px;border-radius:3px;border: 1px solid red;background-color:red;cursor:pointer;color:white ;margin:10px;font-weight:bold;width:100%;height:40px;">&nbsp;Remove</span>
                                 <img src="{{ $slider }}"  width=100 height=100 alt="{{ $slider }}">
                            </div>
                        @endforeach
                        </div>
                        <div class="col-sm-2">
                                <div class="form-group">
                                    {!! Form::file('image_more[]', ['id' => 'upload_image_1','multiple', 'accept' => 'image/*']); !!}
                                    <small><p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)]). @lang('lang_v1.aspect_ratio_should_be_1_1') @if(!empty($product->image)) <br> @lang('lang_v1.previous_image_will_be_replaced') @endif</p></small>
                                    
                                </div>
                            
                            <br>
                        </div> 
                    </div>
                 
                     
                    </div>
                </div>
                   
            @endcomponent
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12" style="padding:10px;  max-height:auto; ">
            {{-- *********1************ --}}
            @component('components.widget',['class'=>' sections box-primary',"title"=>__('Video')]) 
                <div class="tab">
                    <div class="row text-center">
                     
                        
                        <div class="col-sm-12">
                            <div class="col-sm-12 text-center" style="padding-top:10px;border:1px solid #9f9f9f;border-radius:10px;background-color:#d6d6d6">
                                @php $vedi = json_decode($software->video)  ; $vedio = ""; @endphp
                                @if(isset($vedi) && $vedi != null )
                                @foreach ($vedi as $item)
                                  @php $vedio = $item ;  @endphp
                                @endforeach
                                <video controls width="auto" height="250">
                                  <source src="{{ asset('public/uploads/img/vedios/'.$vedio) }}" type="video/mp4">
                                  <source src="{{ asset('public/uploads/img/vedios/'.$vedio) }}" type="video/webm">
                                  <source src="{{ asset('public/uploads/img/vedios/'.$vedio) }}" type="video/ogg">
                                    <!-- Add more source tags for different video formats if needed -->
                                </video>
                                <br>
                                @endif
                              <div class="form-group text-left" style="background-color:#d6d6d6">
                                {!! Form::label('vedio', __('Vedio') . ':') !!}
                                {!! Form::file('vedio', ['id' => 'upload_video', 'accept' => 'video/*']); !!}
                                <small><p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.vedio_size_limit') / 1000000)]) <br> @lang('lang_v1.aspect_ratio_should_be_1_1')</p></small>
                              </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endcomponent
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12" style="padding:10px;  max-height:auto; ">
            {{-- *********1************ --}}
            @component('components.widget',['class'=>' sections box-primary',"title"=>__('Other Section')]) 
                <div class="tab">
                    <div class="row text-center">
                        <div class="col-sm-12">
                            <div class="col-sm-12 text-center"  >
                             <span  class="btn btn-primary"   ><a style="color:white" href="{{\URL::to("/woocommerce/software/create")}}"><i class="fas fa-plus"></i>@lang("messages.add")</a></span>
                                <table class="table table-striped table-bordered dataTable" style="width:100%" id="table_floating_bar">
                                    <thead>
                                        <tr>
                                            <th class="xl-1"  style="background-color:#00000034">Action</th>
                                            <th class="xl-1"  style="background-color:#00000034">Name</th>
                                            <th class="xl-1"  style="background-color:#00000034">Description</th>
                                            <th class="xl-1 img_section"  style="background-color:#00000034">Image</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($otherSoftware as $item)
                                            <tr>
                                                <th class="xl-1"   >
                                                     <span class="btn btn-primary"  ><a style="color:white" href="{{\URL::to('/woocommerce/software/edit',$item->id)}}"><i class="fas fa-edit"></i>@lang("messages.edit")</a></span>
                                                     <span class="btn error delete-section" data-href="{{\URL::to('/woocommerce/software/del',$item->id)}}"><a style="color:red" ><i class="fas fa-trash"></i>@lang("messages.delete")</a></span>
                                                </th>
                                                <th class="xl-1"  >{!! $item->name !!} @if($item->topSection == 1) <br>**** <b class="btn btn-info">Top Section</b> ****@endif</th>
                                                <th class="xl-1"  >{!! $item->description !!}</th>
                                                <th class="xl-1 img_section"   ><img src="{{$item->image_url}}" width=100 height=100 > </th>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <br>
                <br>
            <div class="form-group pull-right">
                {{Form::submit('update', ['class'=>"btn btn-primary"])}}
            </div>
            @endcomponent
        </div>
    </div>
    {!! Form::close() !!}
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
            
        $(document).on('click', '.delete-more-image', function() {
            var name = $(this).attr("data-url");
            swal({
                title: LANG.sure,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                     
                    $.ajax({
                        method: 'GET',
                        url: "/woocommerce/software/del-image",
                        dataType: 'json',
                        data: { name: name } ,
                        success: function(result) {
                            
                            if (result.success == true) {
                                toastr.success(result.msg);
                                window.location.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        },
                    });
                }
            });
        });
        $(document).on('click', '.delete-section', function() {
            var url = $(this).attr("data-href");
            swal({
                title: LANG.sure,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                     
                    $.ajax({
                        method: 'GET',
                        url: url,
                        dataType: 'json',
                        success: function(result) {
                            
                            if (result.success == true) {
                                toastr.success(result.msg);
                                window.location.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        },
                    });
                }
            });
        });
        
        
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