@extends('layouts.app')
@section('title', __('woocommerce::lang.floating_edit'))

@section('content')
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.2/croppie.min.css">
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('woocommerce::lang.floating_edit')</h1>
</section>


<!-- Main content -->
<section class="content">
    {!! Form::open(['action' => ['\Modules\Woocommerce\Http\Controllers\WoocommerceController@updateFloat',"id" => $id], 'method' => 'post','files' => true]) !!}
    <div class="row">
        <div class="col-xs-12" style="padding:10px;  max-height:auto; margin:auto 20%;width:55%">
           
             @component('components.widget',['class'=>'  sections box-primary']) 
             <h4 style="text-align: center;color:#fff;background-color:#bc6d05d9;padding:10px" > @lang("Floating Item Card")</h4>

                <div class="contents">
                    <div class="col-xs-12" style="padding:00px !important;"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="leftSection sec   form-group" style="padding:10px;">
                        {!! Form::label('title', __('Title') . ':') !!}
                        {!! Form::text("title", $floating->title, ['class' => 'form-control','placeholder'=>__('messages.please_enter_title'),'style'=>'max-width:100%']); !!}
                        <br>
                        {!! Form::label('category_id', __('Category ') . ':') !!}
                         
                        {!! Form::select("category_id",$list_category,($floating->category_id)?$floating->category_id:null, ['class' => 'form-control select2' ]); !!}
                     
                        <br>
                        </div>
                    </div>
                        {{-- <div class="col-lg-12 col-md-12  col-sm-12 col-xs-12">
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
                            @if($floating->icon_url != null || $floating->icon_url != "" )
                                <div class="col-lg-12 text-center">
                                    <img src="{{$floating->icon_url}}" width=80% height=20% style="border-radius:10px;border:2px solid black;padding: 2px" />
                                </div>
                            @endif
                        </div> --}}
                        <div class="col-xs-12" style="padding:00px !important;"> 
                            <div class="box-container container" style="margin-top:30px;width:90% !important">
                                <div class="panel panel-primary">
                                    <div class="panel-heading"> <i class="fa fa-close" style="cursor:pointer" id="box-back">   </i> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  Float Image</div>
                                    <div class="panel-body">
                                        <div class="row">
                                            {{-- <div class="col-md-6 text-center">
                                                <div id="image-preview"
                                                    style="background:#e1e1e1;padding:30px;height:300px;"></div>
                                            </div> --}}
                                            <div class="col-md-12">
                                                 
                                                        <div class="row ">
                                                            <input type="hidden" id="id" value="{{$floating->id}}">
                                                            <input type="hidden" id="business_id" value="{{session()->get('user.business_id')}}">
                                                             @if($floating->icon_url != null || $floating->icon_url != "" )
                                                                    <div class="image_section" style="text-align: center">
                                                                        <img class="img" src="{{$floating->icon_url}}" alt="" width="400px" height="250px">
                                                                    </div>
                                                            @else
                                                                <div class="image_section">
                                                                No Image Found.
                                                                </div>
                                                            @endif
                                                        </div>
                                               
                                            </div>
                                            <h3>&nbsp;</h3>
                                            <div class="col-md-12">
            
                                          
                                            <div class="col-md-12 text-center global">
                                                <div id="cropie-demo" style="width:250px"></div>
                                                
                                            </div>
                                            <div class="col-md-12 text-center ">
                                                <h3>&nbsp;</h3>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <i class="btn btn-note" style="width:100%" id="sized" data-size="250" data-sized="400">16/9 </i>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <i class="btn btn-note" style="width:100%" id="sized2" data-size="200">4/3 </i>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p>&nbsp;</p>
                                                        <i class="btn btn-note" style="width:100%" id="sized4" data-size="150">1/1 </i>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <p>&nbsp;</p>
                                                        <i class="btn btn-note" style="width:100%" id="sized3" data-size="290" data-sized="950">Orginal Size</i>
                                                    </div>
                                                </div>
                                                <h3>&nbsp;</h3>
                                                <strong class="text-left" style="text-align: left">Select Image:</strong>
                                                <input type="file" id="upload"> <br><span>&nbsp;&nbsp;&nbsp;&nbsp; </span>
                                                
                                                
                                                
                                            </div>
                                            <div class="clearfix"></div>
                                            <div class="col-md-8 text-left pull-right" style="padding-top:00px;">
                                                <button class="btn btn-primary upload-result">CHANGE FLOATING IMAGE</button>
                                                
                                                <br>
                                            </div>
                                        </div>
                                            
                                            
                                        </div>
                        
                                    
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> 
                </div>
                <div class="col-xs-12">
                <div class="form-group pull-right">
                    {{Form::submit('Update', ['class'=>"btn btn-primary"])}}
                </div>
            </div>
            @endcomponent
         
        </div>
    </div>
    <div class="row">
        </div>
    {!! Form::close() !!}
</section>
@stop
@section('javascript')
<script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.2/croppie.js"></script>

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
 
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
        $uploadCrop = $('#cropie-demo').croppie({
                enableExif: true,
                viewport: {
                    width: 200,
                    height: 200,
                    type: 'triangle'
                },
                boundary: {
                    width: 600,
                    height: 300
                }
            });
       
        $('#logo-change').on('click', function() {
            $(".box-container").toggleClass("hide");
            $("#box-back").removeClass("hide");
            $(".layout").css({"display":"block"});
            $(this).addClass("hide");
        });
        $('#box-back').on('click', function() {
            $("#logo-change").toggleClass("hide");
            $(".box-container").addClass("hide");
            $(".layout").css({"display":"none"});
            $(this).addClass("hide");
        });
        $('#sized').on('click', function() {
            var size = $(this).attr("data-size");
            var sized = $(this).attr("data-sized");
            html = $(".global").find(".cr-boundary").parent().find('img').attr("src");
            $(".global").find("div").remove(); 
            $(".global").append('<div id="cropie-demo" style="width:250px"></div>'); 
            file = $('#upload');
            
            $uploadCrop = $('#cropie-demo').croppie({
                enableExif: true,
                viewport: {
                    width: sized,
                    height: size,
                    type: 'triangle'
                },
                boundary: {
                        width: 600,
                        height: 300
                    }
            });
            $(".global").find(".cr-boundary").parent().find('img').attr("src",html) ;
            var reader = new FileReader();
            reader.onload = function(e) {
               
                $uploadCrop.croppie('bind', {
                    url: e.target.result,
                    
                }).then(function() {
                    console.log('jQuery bind complete');
                });
            }
             
            
            reader.readAsDataURL(file[0].files[0]);
             

            
        });
        $('#sized2').on('click', function() {
            var size = $(this).attr("data-size");
            html = $(".global").find(".cr-boundary").parent().find('img').attr("src");
            $(".global").find("div").remove(); 
            $(".global").append('<div id="cropie-demo" style="width:250px"></div>'); 
            $uploadCrop = $('#cropie-demo').croppie({
                enableExif: true,
                viewport: {
                    width: size,
                    height: size,
                    type: 'triangle'
                },
                boundary: {
                    width: 600,
                    height: 300
                }
            });
            $(".global").find(".cr-boundary").parent().find('img').attr("src",html) ;
            file = $('#upload');
            var reader = new FileReader();
            reader.onload = function(e) {
                $uploadCrop.croppie('bind', {
                    url: e.target.result
                }).then(function() {
                    console.log('jQuery bind complete');
                });
            }
            reader.readAsDataURL(file[0].files[0]);
             
        });
        $('#sized3').on('click', function() {
            var size = $(this).attr("data-size");
            var sized = $(this).attr("data-sized");
            html = $(".global").find(".cr-boundary").parent().find('img').attr("src");
            $(".global").find("div").remove(); 
            $(".global").append('<div id="cropie-demo" style="width:250px"></div>'); 
            $uploadCrop = $('#cropie-demo').croppie({
                enableExif: true,
                viewport: {
                    width: sized,
                    height: size,
                    type: 'triangle'
                },
                boundary: {
                    width: 600,
                    height: 300
                }
            });
            $(".global").find(".cr-boundary").parent().find('img').attr("src",html) ;
            file = $('#upload');
            var reader = new FileReader();
            reader.onload = function(e) {
                $uploadCrop.croppie('bind', {
                    url: e.target.result
                }).then(function() {
                    console.log('jQuery bind complete');
                });
            }
            reader.readAsDataURL(file[0].files[0]);
        });
        $('#sized4').on('click', function() {
            var size = $(this).attr("data-size");
            html = $(".global").find(".cr-boundary").parent().find('img').attr("src");
            $(".global").find("div").remove(); 
            $(".global").append('<div id="cropie-demo" style="width:250px"></div>'); 
            $uploadCrop = $('#cropie-demo').croppie({
                enableExif: true,
                viewport: {
                    width: size,
                    height: size,
                    type: 'triangle'
                },
                boundary: {
                    width: 600,
                    height: 300
                }
            });
            $(".global").find(".cr-boundary").parent().find('img').attr("src",html) ;
            file = $('#upload');
            var reader = new FileReader();
            reader.onload = function(e) {
                $uploadCrop.croppie('bind', {
                    url: e.target.result
                }).then(function() {
                    console.log('jQuery bind complete');
                });
            }
            reader.readAsDataURL(file[0].files[0]);
        });
        // ..1................................................
        $('#upload').on('change', function() {
            var reader  = new FileReader();
           
            reader.onload = function(e) {
                 
                $uploadCrop.croppie('bind', {
                    url: e.target.result
                }).then(function() {
                    console.log('jQuery bind complete');
                });
            }
            reader.readAsDataURL(this.files[0]);
        });
        // ..2......................................................
        $('.upload-result').on('click', function(ev) {
            $uploadCrop.croppie('result', {
                type: 'canvas',
                size: 'viewport'
            }).then(function(img) {
                business_id = $("#business_id").val();
                id          = $("#id").val();
                $.ajax({
                    
                    url: "/woocommerce/image-crops/float",
                    type: "POST", 
                    data: {
                        "image": img,
                        "id": id,
                        "business_id": business_id,
                    },
                    success: function(data) {
                        // html = '<img src="' + img + '" />';
                        // $("#image-preview").html(html);
                        window.location.reload();
                    }
                });
            });
        });
     
        
</script>
@endsection