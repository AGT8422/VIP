<!DOCTYPE html>
<html lang="{{ session()->get('user.language', config('app.locale')) }}">
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="IZOCLOUD |, المبيعات والمخازن اونلاين بإشتراك شهري">
    <meta name="author" content="IZOCLOUD">
    <meta property="og:type" content="website"/>
    <meta property="og:title" content="IZOCLOUD - برنامج المحاسبة لإدارة الأنشطة التجارية"/>
    <meta property="og:site_name" content="IZOCLOUD "/>
    <meta property="og:image" content="http://test.izocloud.com/uploads/IZO-D1.jpg"/>
    <meta property="og:description" content=" مرحبا   نحن نعم علي بناء و تطوير المواقع وبرامج سطح المكتب"/>

    <meta name="keywords" content="صيدليات ,
		مخازن , مخزون , مستودعات , حسابات , مشتريات , مبيعات , عملاء , موردين , عملاء وموردين , محلات ، إدارة محلات ،
		برنامج مخازن , برنامج حسابات , برنامج مستودعات , برنامج مشتريات , برنامج عملاء , برنامج موردين , برنامج عملاء وموردين , برنامج محلات , برنامج مخزون , إدارة مستودعات ،برنامج محلات ،برنامج مخازن مجانى ,
		برنامج المخازن , برنامج الحسابات , برنامج المستودعات , برنامج المشتريات , برنامج العملاء , برنامج الموردين , برنامج العملاء والموردين ، برنامج المحلات , برنامج المخزون , برنامج إدارة المستودعات ، برنامج المحلات ،برنامج للمخازن مجانى ,
		برنامج للمخازن , برنامج للحسابات , برنامج للمستودعات , برنامج للمشتريات , برنامج للعملاء , برنامج للموردين , برنامج للعملاء والموردين ، برنامج للمحلات , برنامج للمخزون , برنامج لإدارة المستودعات ، برنامج للمحلات ،
		برنامج عربى ,
		justagain , pharmacy , drugs , ERP , store , customers , clients , suppliers , sales , stores , store , point of sale , pos , pos system , supermarket system ,
		" />


    <title>@yield('title') - {{ config('app.name', 'POS') }}</title> 

    @include('layouts.partials.css')

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body style=" background-image:url('../../../uploads/IZO-D2.gif') ; 
                    background-size: cover;
                    background-attachment: fixed;
                    background-repeat:  no-repeat; 
                    background-position: center;

                    font-family : Almarai !important ;">
    @inject('request', 'Illuminate\Http\Request')
    @if (session('status'))
        <input type="hidden" id="status_span" data-status="{{ session('status.success') }}" data-msg="{{ session('status.msg') }}">
    @endif
    <div class="container-fluid">
        <div class="row eq-height-row">
            <div class="col-md-5 col-sm-5 hidden-xs left-col eq-height-col" style="width:100% !important">
                {{-- <img src="/img/home-bg.jpg"  class="img-responsive" alt="Logo" style="width:100% !important"> --}}
                 
                <div class="left-col-content login-header" > 
                    <div style="margin-top: 10%;display: none" >
                    <a href="/">
                    @if(file_exists(public_path('uploads/IZO-D1.jpg')))
                        <img src="/uploads/IZO-D1.jpg" class="img-rounded" alt="Logo" width="150">
                    @else
                       {{ config('app.name', 'erp4anyone') }}
                    @endif
                    </a>
                    <br/>
                    @if(!empty(config('constants.app_title')))
                        <small style="color:#960a27">{{config('constants.app_title')}}</small>
                    @endif
                    </div>
                </div>
            </div>
            <div class="col-md-7 col-sm-7 col-xs-12 right-col " style="width:100% !important; ">
                <div class="row">
                    <div class="col-md-12 col-xs-12" style=" width:100%"  style="border:5px solid rgba(0, 0, 0, 0.536)">
                        
                        @yield('content')
                        {{-- <div class="col-md-7 col-xs-12" style="   width:100%; text-align:center">
                            @if(!($request->segment(1) == 'business' && $request->segment(2) == 'register'))
                                <!-- Register Url -->
                                @if(config('constants.allow_registration'))
                                    <a href="{{ route('business.getRegister') }}@if(!empty(request()->lang)){{'?lang=' . request()->lang}} @endif" class="btn  btn-flat" ><b>{{ __('business.not_yet_registered')}}</b> {{ __('business.register_now') }}</a>
                                    <!-- pricing url -->
                                    {{--@if(Route::has('pricing') && config('app.env') != 'demo' && $request->segment(1) != 'pricing')
                                        &nbsp; <a href="{{ action('\Modules\Superadmin\Http\Controllers\PricingController@index') }}">@lang('superadmin::lang.pricing')</a>
                                    @endif--}}
                                {{-- @endif
                            @endif
                            @if($request->segment(1) != 'login')
                                &nbsp; &nbsp;<span class="text-white">{{ __('business.already_registered')}} </span><a href="{{ action('Auth\LoginController@login') }}@if(!empty(request()->lang)){{'?lang=' . request()->lang}} @endif">{{ __('business.sign_in') }}</a>
                            @endif
                        </div> --}}
                    </div>
                    
                 </div>


            </div>
        </div>
    </div>

    <small>
    	<b>{{ config('app.name', 'IZO CLOUD ') }} - V{{config('author.app_version')}} | Copyright  &copy;  {{ date('Y') }} , All rights reserved.</b>
    	<b> <br> +971-56-777-9250   _  +971-4-23-55-919  , Powered By AGT </b>
    </small>


    
    @include('layouts.partials.javascripts')
    
    <!-- Scripts -->
    <script src="{{ asset('js/login.js?v=' . $asset_v) }}"></script>
    
    @yield('javascript')

    <script type="text/javascript">
        $(document).ready(function(){
            $('.select2_register').select2();

            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' // optional
            });
        });
    </script>
</body>

</html>