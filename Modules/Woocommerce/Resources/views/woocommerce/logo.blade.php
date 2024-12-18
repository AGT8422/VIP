@extends('layouts.app')
@section('title', __('woocommerce::lang.logo'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('woocommerce::lang.logo')</h1>
</section>

<header>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.2/croppie.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* .box-container{
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%,-50%);
            z-index: 6;
            width: 90%;
            max-height: 200px;
        } */
        /* .layout{
            position: absolute;
            display: none;
            z-index: 5;
            background-color:rgba(21, 21, 21, 0.253);
            width: 100%; 
            height: 100%;
            color: transparent; 
        } */
        .img{
            background-color:rgba(21, 21, 21, 0.253);
            width: 400px;
            margin: 10px auto;
            color: transparent; 
            padding:3px;
            border: 3px solid black;
            border-radius: 10px;
        }
    </style>
</header>
<div class="layout">&nbsp;</div>
<!-- Main content -->
<section class="content">
     <div class="row ">
        <div class="col-xs-12" style="padding:10px;  max-height:auto; ">
            {{-- *********3************ --}}
            @component('components.widget',['class'=>' sections box-primary',"title"=>__('Website Logo')]) 
                <div class="box-container container" style="margin-top:30px;width:90% !important">
                    <div class="panel panel-primary">
                        <div class="panel-heading"> <i class="fa fa-close" style="cursor:pointer" id="box-back">   </i> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  Upload Your Logo</div>
                        <div class="panel-body">
                            <div class="row">
                                {{-- <div class="col-md-6 text-center">
                                    <div id="image-preview"
                                        style="background:#e1e1e1;padding:30px;height:300px;"></div>
                                </div> --}}
                                <div class="col-md-4">
                                     
                                            <div class="row ">
                                                <input type="hidden" id="business_id" value="{{session()->get('user.business_id')}}">
                                                @if($logo != "" && $logo != "nan" ) 
                                                    <div class="image_section" style="text-align: center">
                                                        <img class="img" src="{{$logo}}" alt="" width="400px" height="250px">
                                                    </div>
                                                @else
                                                    <div class="image_section">
                                                    No Image Found.
                                                    </div>
                                                @endif
                                            </div>
                                   
                                </div>
                                <div class="col-md-8 text-center ">
                                    <h3>&nbsp;</h3>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <button style="width:100%" id="sized" data-size="250" data-sized="400">16/9 </button>
                                        </div>
                                        <div class="col-md-5">
                                            <button style="width:100%" id="sized2" data-size="200">4/3 </button>
                                        </div>
                                        <div class="col-md-6">
                                            <p>&nbsp;</p>
                                            <button style="width:100%" id="sized4" data-size="150">1/1 </button>
                                        </div>
                                        <div class="col-md-5">
                                            <p>&nbsp;</p>
                                            <button style="width:100%" id="sized3" data-size="290" data-sized="950">Orginal Size</button>
                                        </div>
                                    </div>
                                    <h3>&nbsp;</h3>
                                    <strong class="text-left" style="text-align: left">Select Image:</strong>
                                    <input type="file" id="upload"> <br><span>&nbsp;&nbsp;&nbsp;&nbsp; </span>
                                    
                                    
                                    
                                </div>
                                <div class="col-md-9">

                              
                                <div class="col-md-12 text-center global">
                                    <div id="cropie-demo" style="width:250px"></div>
                                    
                                </div>
                                
                                <div class="clearfix"></div>
                                <div class="col-md-12 text-left pull-left" style="padding-top:00px;">
                                    <button class="btn btn-primary upload-result">CHANGE LOGO</button>
                                    
                                    <br>
                                </div>
                            </div>
                                
                                
                            </div>
            
                        
                        </div>
                    </div>
                </div>
            @endcomponent
                        @component('components.widget',['class'=>' sections box-primary',"title"=>__('Website Logo')]) 
            <div class="box-container container" style="margin-top:30px;width:90% !important">
                <div class="panel panel-primary">
                    <div class="panel-heading"> <i class="fa fa-close" style="cursor:pointer" id="box-back">   </i> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  Change Your Website Colors </div>
                    <div class="panel-body">
                        <div class="row">
                            {!! Form::open(['action' => '\Modules\Woocommerce\Http\Controllers\WoocommerceController@changeColor', 'method' => 'post','files' => true]) !!}
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label("web_color", __("Main Color"))!!}
                                    <br>
                                    {!! Form::color("web_color",$business->web_color ,["class"=>"color","style"=>"width:100px;border-radius:5px;"])!!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label("web_second_color", __("Second Color"))!!}
                                    <br>
                                    {!! Form::color("web_second_color",$business->web_second_color ,["class"=>"color","style"=>"width:100px;border-radius:5px;"])!!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label("web_font_color", __("Font Color"))!!}
                                    <br>
                                    {!! Form::color("web_font_color",$business->web_font_color ,["class"=>"color","style"=>"width:100px;border-radius:5px;"])!!}
                                </div>
                            </div>
                        </div>
                        
                        
                        {!! Form::submit("Update",["class"=>"btn btn-primary"])!!}
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        @endcomponent
        </div>
    </div>
   
 
</section>
 

@stop
@section('javascript')
 <script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.2/croppie.js"></script>

<script type="text/javascript">
 
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
                    width: 1000,
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
                        width: 1000,
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
                    width: 1000,
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
                    width: 1000,
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
                    width: 1000,
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
                $.ajax({
                    
                    url: "/woocommerce/image-crops",
                    type: "POST", 
                    data: {
                        "image": img,
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