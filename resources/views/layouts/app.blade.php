@inject('request', 'Illuminate\Http\Request')

@if($request->segment(1) == 'pos' && ($request->segment(2) == 'create' || $request->segment(3) == 'edit'))
    @php
          $pos_layout = true;
    @endphp
    @else
    @php
        
        $pos_layout = false;
    @endphp
@endif


<!DOCTYPE html>
<html lang="{{ session()->get('user.language', config('app.locale')) }}" dir="{{in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) ? 'rtl' : 'ltr'}}">
   
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

        <title>@yield('title') - {{ Session::get('business.name') }}</title>

        @include('layouts.partials.css')

        @yield('css')
        @yield('special_css')
        <style>
            @media screen and (max-width: 576px) {

                .sidebar-open .main-header {

                    transform: translate(230px, 0);
                }
                .blackBack{
                    background-color: black !important;
                }
            }
         .waring_message{
            border: 1px solid black;
            border-radius: 10px;
            box-shadow: 1px 1px 10px rgb(133, 133, 133);
            width:30%;
            /* margin: auto 50%; */
            padding:20px ;
            color:white;
             font-weight: 500;
            background-color: RED;
            font-size: 20px;
            position: fixed;
            z-index: 100 !important;
            right:0px;
            top: 70px;
         
        }
        </style>
    </head>


    <body class=" @if($pos_layout) hold-transition lockscreen @else hold-transition skin-@if(!empty(session('business.theme_color'))){{session('business.theme_color')}}@else{{'black-light'}}@endif sidebar-mini @endif">
        @php $moduleUtil = new \App\Utils\ModuleUtil; $package =  \Modules\Superadmin\Entities\Subscription::first();   @endphp 
        @if(!$moduleUtil->isSubscribed(request()->session()->get('user.business_id')))
        <div class="waring_message text-center" >
            {{-- {!! __("lang_v1.subscribe_wrang") !!} {!! "<br> During " . $package["permitted_period"]      !!} --}}
        </div>
        @endif 
        @php $moduleUtil = new \App\Utils\ModuleUtil; $package =  \Modules\Superadmin\Entities\Subscription::first();   @endphp 
        @if($package)
            @if(!$moduleUtil->isSubscribed(request()->session()->get('user.business_id')))

                @if(\Carbon::createFromFormat('Y-m-d', $package->permitted_period) > \Carbon::now())
                <div class="waring_message text-center" >
                    {!! __("lang_v1.subscribe_wrang") !!} {!! "<br> During " . $package["permitted_period"]      !!}
                </div>
                @else
                @php $now = \Carbon::now();  @endphp 
                    @if($package->end_date < $now ) 
                <div class="waring_message text-center" >
                        <b>Your Subscribe is Expired </b> <br>
                        <p>hurry up to renew your subscribe</p> 
                    </div>
                    @endif     
                @endif
                
            @endif
        @endif
    <div class="wrapper thetop">
            <script type="text/javascript">
            // alert(localStorage.getItem("upos_sidebar_collapse"));
                if(localStorage.getItem("upos_sidebar_collapse") == 'true'){
                    var body = document.getElementsByTagName("body")[0];
                    body.className += " sidebar-collapse";
                }
            </script>
            @if(!$pos_layout)
             
                @include('layouts.partials.header')
                @include('layouts.partials.sidebar')
            @else
                
                @include('layouts.partials.header-pos')
            @endif

            <!-- Content Wrapper. Contains page content -->
            <div id="inside-content" style="backgrond-color:black; @if(session()->get('user.language', config('app.locale'))=='ar') margin-right:16%; @else margin-left:16%;  @endif " class=" @if(!$pos_layout) content-wrapper @endif">
                <!-- empty div for vuejs -->
                <div id="app">
                    @yield('vue')
                </div>
                
                <!-- Add currency related field-->
                <input type="hidden" id="__code" value="{{session('currency')['code']}}">
                <input type="hidden" id="__symbol" value="{{session('currency')['symbol']}}">
                <input type="hidden" id="__thousand" value="{{session('currency')['thousand_separator']}}">
                <input type="hidden" id="__decimal" value="{{session('currency')['decimal_separator']}}">
                <input type="hidden" id="__symbol_placement" value="{{session('business.currency_symbol_placement')}}">
                <input type="hidden" id="__precision" value="{{config('constants.currency_precision', 2)}}">
                <input type="hidden" id="__quantity_precision" value="{{config('constants.quantity_precision', 2)}}">
                <!-- End of currency related field-->

                @if(session('status'))
                    <input type="hidden" id="status_span" data-status="{{ session('status.success') }}" data-msg="{{ session('status.msg') }}">
                @endif
                
                @yield('content')

                <div class='scrolltop no-print'>
                    <div class='scroll icon'><i class="fas fa-angle-up"></i></div>
                </div>

                @if(config('constants.iraqi_selling_price_adjustment'))
                    <input type="hidden" id="iraqi_selling_price_adjustment">
                @endif

                <!-- This will be printed -->
                <section class="invoice print_section" id="receipt_section">
                </section>

            </div>
            @include('home.todays_profit_modal')
            <!-- /.content-wrapper -->

            @if(!$pos_layout)
                @include('layouts.partials.footer')
            @else
                @include('layouts.partials.footer_pos')
            @endif

            <audio id="success-audio">
              <source src="{{ asset('/audio/success.ogg?v=' . $asset_v) }}" type="audio/ogg">
              <source src="{{ asset('/audio/success.mp3?v=' . $asset_v) }}" type="audio/mpeg">
            </audio>
            <audio id="error-audio">
              <source src="{{ asset('/audio/error.ogg?v=' . $asset_v) }}" type="audio/ogg">
              <source src="{{ asset('/audio/error.mp3?v=' . $asset_v) }}" type="audio/mpeg">
            </audio>
            <audio id="warning-audio">
              <source src="{{ asset('/audio/warning.ogg?v=' . $asset_v) }}" type="audio/ogg">
              <source src="{{ asset('/audio/warning.mp3?v=' . $asset_v) }}" type="audio/mpeg">
            </audio>
        </div>

        @if(!empty($__additional_html))
            {!! $__additional_html !!}
        @endif

        @include('layouts.partials.javascripts')

        <div class="modal fade view_modal" tabindex="-1" role="dialog"
        aria-labelledby="gridSystemModalLabel"></div>

        @if(!empty($__additional_views) && is_array($__additional_views))
            @foreach($__additional_views as $additional_view)
                @includeIf($additional_view)
            @endforeach
        @endif
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

    <script>
         setInterval(function() {
            $('meta[name="csrf-token"]').attr('content', '{{ csrf_token() }}');
        }, 300000); 
     </script>

</html>
