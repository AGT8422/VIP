<!DOCTYPE html>
<html lang="{{ session()->get('user.language', config('app.locale')) }}">
<html>
<head>
    <!-- PWA  -->
    <meta name="theme-color" content="#e68000"/>
    <link rel="apple-touch-icon" href="{{ asset('logo.png') }}">
    <link rel="manifest" href="{{ asset('/manifest.json') }}"> 
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="icon" type="image/x-icon" href="{{ asset('/public/uploads/POS.ico') }}">

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
    <div class="container">
        <div class="row eq-height-row" style="border:0px solid white">
            <div class="col-md-12 col-sm-12  eq-height-col" style="width:100% !important;border:0px solid white">
                {{-- <img src="/img/home-bg.jpg"  class="img-responsive" alt="Logo" style="width:100% !important"> --}}
                <div class="left-col-content login-header" style="border:0px solid white"> 
                    <div style="margin-top: 10%;display: none" >
                    <a href="/">
                        @if(file_exists(public_path('uploads/IZO-D1.jpg')))
                            <img src="/uploads/IZO-D1.jpg" class="img-rounded" alt="Logo" width="150">
                        @else
                        {{ config('app.name', 'izocloud') }}
                        @endif
                        </a>
                        <br/>
                        @if(!empty(config('constants.app_title')))
                            <small style="color:#960a27">{{config('constants.app_title')}}</small>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12" style="border:0px solid white;margin-top :10%" >
                <div class="col-md-6 col-md-offset-3" style="background-color: rgb(236, 236, 236); padding:0px;border-radius:10px;box-shadow:5px 5px 100px grey">
                    @yield('content')
                </div>
                
                <div class="row" style="border:0px solid red;">
                        <div class="col-md-12 col-xs-12" >
                                @if(!($request->segment(1) == 'business' && $request->segment(2) == 'register'))
                                    <!-- Register Url -->
                                    {{-- @if(config('constants.allow_registration'))
                                        <a href="{{ route('business.getRegister') }}@if(!empty(request()->lang)){{'?lang=' . request()->lang}} @endif" class="btn  btn-flat" ><b>{{ __('business.not_yet_registered')}}</b> {{ __('business.register_now') }}</a>
                                        <!-- pricing url -->
                                        @if(Route::has('pricing') && config('app.env') != 'demo' && $request->segment(1) != 'pricing')
                                            &nbsp; <a href="{{ action('\Modules\Superadmin\Http\Controllers\PricingController@index') }}">@lang('superadmin::lang.pricing')</a>
                                        @endif  
                                    @endif --}}
                                @endif
                                @if($request->segment(1) != 'login')
                                    &nbsp; &nbsp;<span class="text-black">{{ __('business.already_registered')}} </span><a href="{{ action('Auth\LoginController@login') }}@if(!empty(request()->lang)){{'?lang=' . request()->lang}} @endif">{{ __('business.sign_in') }}</a>
                                @endif
                        </div>   
                    </div>
                      
                    <div class="col-md-12 col-xs-12">
                        <h6 style="text-decoration:ltr !important;text-align:center;color:white !important; ">
                            <b>{{ config('app.name', 'IZO CLOUD ') }} - V{{config('author.app_version')}} | Powered By AGT | +971-56-777-9250  |  +971-4-23-55-919  </b>
                            <b><br> All Rights Reserved | Copyright  &copy;  {{ date('Y') }} </b>
                        </h6>
                    </div>
                     
                     
                 </div>


            </div>
        </div>
    </div>

    


    
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
     <script>
        // setInterval(() => {
        //       $.get('/auth/status', function(response) {
        //         if (response.authenticated) {
        //             // User is logged in
        //             console.log("yes");
        //         } else {
        //             // User is logged out
        //             console.log("no");
        //             $.ajax({
        //                 type:'GET',
        //                 url:'/user/log-out',
        //                 async: false,
        //                 success:function(data){
        //                 // do something with data
        //                 }
        //             });
        //         }
        //     });
        // }, 8000);
     </script>
     <script src="{{ asset('/sw.js') }}"></script>
     <script>
        if ("serviceWorker" in navigator) {
           // Register a service worker hosted at the root of the
           // site using the default scope.
           navigator.serviceWorker.register("/sw.js").then(
           (registration) => {
              console.log("Service worker registration succeeded:", registration);
           },
           (error) => {
              console.error(`Service worker registration failed: ${error}`);
           },
         );
       } else {
          console.error("Service workers are not supported.");
       }
     </script>
</body>

</html>