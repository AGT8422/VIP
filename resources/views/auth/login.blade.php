@extends('layouts.auth2_old')
@section('title', __('lang_v1.login'))

@section('content')   
    {{-- section change the database --}}
    @php 
        $url       = request()->root();
        $parsedUrl = parse_url($url);
        $host      = $parsedUrl['host'] ?? '';  
        $hostParts = explode('.', $host);
        
        if (count($hostParts) == 3) {
            // Remove the last two parts (domain and TLD)
            array_pop($hostParts); // TLD
            array_pop($hostParts); // Domain

            // The remaining parts are the subdomain
            $subdomain = implode('.', $hostParts);
        } else if(count($hostParts) == 2){
            // Remove the last two parts (domain and TLD)
            array_pop($hostParts); // TLD

            // The remaining parts are the subdomain
            $subdomain = implode('.', $hostParts);
        } else {
            // No subdomain
            $subdomain = '';

        }
        $subdomain     = $subdomain;  
        $domain_url    = request()->session()->get('user_main.domain_url');
        $database_name = (parse_url(request()->root(),PHP_URL_HOST) == $domain_url)?request()->session()->get('user_main.database'):"albasee2_da";
        $database_user = (parse_url(request()->root(),PHP_URL_HOST) == $domain_url)?request()->session()->get('user_main.database_user'):"";
        $domain_name   = (parse_url(request()->root(),PHP_URL_HOST) == $domain_url)?request()->session()->get('user_main.domain'):$subdomain;
        
        if(parse_url(request()->root(),PHP_URL_HOST) != $domain_url){
            request()->session()->flush();
        }
         
     
    @endphp 
 
    <div class="col-12"   style="text-align: center;
            color: #ffffff;
            background-color: #000000; 
            margin-bottom: 10px;
            border-radius: 10px 10px 0px 0px;
            border-bottom:10px solid #ee660e;
            padding-top: 1px;
            padding-bottom: 15px;">
            <h3 style="color: #ffffff">{{env('APP_TITLE','izoCloud')}}</h3>
            <select class="form-control col-12" id="change_lang" style="margin:auto 5% ; width:90%;border-radius:10px !important">
                @foreach(config('constants.langs') as $key => $val)
                    <option value="{{$key}}"  @if( $key == "en") selected @endif >
                        {{$val['full_name']}}
                    </option>
                @endforeach
            </select>  
    </div>
    
    <br>
    <form method="POST" action="{{ route('login') }}" id="login-form"  >
        {{ csrf_field() }}
        @csrf

            
        {{-- database section --}}
        <input type="hidden" id="database_name" name="database_name" value="{{$database_name}}">
        <input type="hidden" id="database_user" name="database_user" value="{{$database_user}}">
        <input type="hidden" id="domain_name"   name="domain_name" value="{{$domain_name}}">


        {{--User name--}}
        <div class="form-group has-feedback {{ $errors->has('username') ? ' has-error' : '' }}" >
            @php
                $username = old('username');
                $password = null;
                if(config('app.env') == 'demo'){
                    $username = 'admin';
                    $password = '123456';

                    $demo_types = array(
                        'all_in_one' => 'admin',
                        'super_market' => 'admin',
                        'pharmacy' => 'admin-pharmacy',
                        'electronics' => 'admin-electronics',
                        'services' => 'admin-services',
                        'restaurant' => 'admin-restaurant',
                        'superadmin' => 'superadmin',
                        'woocommerce' => 'woocommerce_user',
                        'essentials' => 'admin-essentials',
                        'manufacturing' => 'manufacturer-demo',
                    );

                    if( !empty($_GET['demo_type']) && array_key_exists($_GET['demo_type'], $demo_types) ){
                        $username = $demo_types[$_GET['demo_type']];
                    }
                }
            @endphp 
            <input id="username" type="text" class="form-control " style="width:83% !important;margin:auto 5%;padding:10px;border-radius:10px" name="username" value="{{ $username }}" required autofocus placeholder="Username">
            <span class="fa fa-user form-control-feedback" style="margin:auto 1%"></span>
                
            
            @if ($errors->has('username'))
                <span class="help-block">
                    <strong>{{ $errors->first('username') }}</strong>
                </span>
            @endif

        </div>

        {{--Password--}}
        <div class="form-group has-feedback {{ $errors->has('password') ? ' has-error' : '' }}">
            
            <input id="password" type="password" class="form-control" name="password" style="width:83% !important;margin:auto 5%;padding:10px;border-radius:10px" value="{{ $password }}" required placeholder="Password">
            <span class="glyphicon glyphicon-lock form-control-feedback" style="margin:auto 1%"></span>
            
            @if ($errors->has('password'))
                <span class="help-block">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
            @endif

        </div>
        <div class="form-group">
            <div class="checkbox icheck col-md-6 col-12 text-left py-5">
                <label style="color: #0c0c0c">
                    <input type="checkbox" name="logout_other"  >&nbsp;&nbsp; {{"Logout Form Other Device"}}
                </label>
            </div>
        </div>
        <div class="form-group">
            <div class="checkbox icheck col-md-6 col-12  text-left py-5">
                <label style="color: #0c0c0c">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>&nbsp;&nbsp; {{"Remember Me"}}
                </label>
            </div>
        </div>
        <br>

        <div class="form-group">
            <div class="col-xl-12 col-md-12 col-6 text-center">
                <br> 
                <br> 
                <a href="{{\URL::to("/auth/google-page")}}" style="padding:10px;background-color:#027bcb;color:white; border-radius: 10px;margin:auto 5%;width:90% !important; font-size: 19px;cursor:pointer">{{"Google Login"}}</a>
                <br>
                <br> 
            </div>
            <button type="submit" class="btn   btn-login" style="border-radius: 10px;margin:auto 5%; width:90%;height: 50px;font-size: 19px;">{{"Login"}}</button>
        </div>
        <div class="form-group">
        <br> 
        </div>

        {{-- <div class="form-group" style="padding-bottom: 9px;">
            @if(config('app.env') != 'demo')
                <a href="{{ route('password.request') }}" class="pull-right" style="color: #0c0c0c">
                    @lang('lang_v1.forgot_your_password')
                </a>
            @endif
        </div> --}}
        

    </form>

    {{-- </div> --}}

    {{-- <div class="" style="text-align: center;background-color: white; padding: 6px 10px 15px 10px;border-radius: 10px;max-width: 350px;margin: auto; margin-top: 70px;">
        <h3>لتجربة البرنامج يمكنك الضغط هنا </h3>
        <button type="button" class="btn btn-danger btn-flat btn-login" style="border-radius: 10px;height: 50px;font-size: 19px;" id="test" >تجربة البرنامج </button>

    </div> --}}

    @if(config('app.env') == 'demo')
    <div class="col-md-12 col-xs-12" style="padding-bottom: 30px;">
        @component('components.widget', ['class' => 'box-primary', 'header' => '<h4 class="text-center">Demo Shops <small><i> Demos are for example purpose only, this application <u>can be used in many other similar businesses.</u></i></small></h4>'])

            <a href="?demo_type=all_in_one" class="btn btn-app bg-olive demo-login" data-toggle="tooltip" title="Showcases all feature available in the application." data-admin="{{$demo_types['all_in_one']}}"> <i class="fas fa-star"></i> All In One</a>

            <a href="?demo_type=pharmacy" class="btn bg-maroon btn-app demo-login" data-toggle="tooltip" title="Shops with products having expiry dates." data-admin="{{$demo_types['pharmacy']}}"><i class="fas fa-medkit"></i>Pharmacy</a>

            <a href="?demo_type=services" class="btn bg-orange btn-app demo-login" data-toggle="tooltip" title="For all service providers like Web Development, Restaurants, Repairing, Plumber, Salons, Beauty Parlors etc." data-admin="{{$demo_types['services']}}"><i class="fas fa-wrench"></i>Multi-Service Center</a>

            <a href="?demo_type=electronics" class="btn bg-purple btn-app demo-login" data-toggle="tooltip" title="Products having IMEI or Serial number code."  data-admin="{{$demo_types['electronics']}}" ><i class="fas fa-laptop"></i>Electronics & Mobile Shop</a>

            <a href="?demo_type=super_market" class="btn bg-navy btn-app demo-login" data-toggle="tooltip" title="Super market & Similar kind of shops." data-admin="{{$demo_types['super_market']}}" ><i class="fas fa-shopping-cart"></i> Super Market</a>

            <a href="?demo_type=restaurant" class="btn bg-red btn-app demo-login" data-toggle="tooltip" title="Restaurants, Salons and other similar kind of shops." data-admin="{{$demo_types['restaurant']}}"><i class="fas fa-utensils"></i> Restaurant</a>
            <hr>

            <i class="icon fas fa-plug"></i> Premium optional modules:<br><br>

            <a href="?demo_type=superadmin" class="btn bg-red-active btn-app demo-login" data-toggle="tooltip" title="SaaS & Superadmin extension Demo" data-admin="{{$demo_types['superadmin']}}"><i class="fas fa-university"></i> SaaS / Superadmin</a>

            <a href="?demo_type=woocommerce" class="btn bg-woocommerce btn-app demo-login" data-toggle="tooltip" title="WooCommerce demo user - Open web shop in minutes!!" style="color:white !important" data-admin="{{$demo_types['woocommerce']}}"> <i class="fab fa-wordpress"></i> WooCommerce</a>

            <a href="?demo_type=essentials" class="btn bg-navy btn-app demo-login" data-toggle="tooltip" title="Essentials & HRM (human resource management) Module Demo" style="color:white !important" data-admin="{{$demo_types['essentials']}}">
                    <i class="fas fa-check-circle"></i>
                    Essentials & HRM</a>
                    
            <a href="?demo_type=manufacturing" class="btn bg-orange btn-app demo-login" data-toggle="tooltip" title="Manufacturing module demo" style="color:white !important" data-admin="{{$demo_types['manufacturing']}}">
                    <i class="fas fa-industry"></i>
                    Manufacturing Module</a>

            <a href="?demo_type=superadmin" class="btn bg-maroon btn-app demo-login" data-toggle="tooltip" title="Project module demo" style="color:white !important" data-admin="{{$demo_types['superadmin']}}">
                    <i class="fas fa-project-diagram"></i>
                    Project Module</a>

            <a href="?demo_type=services" class="btn btn-app demo-login" data-toggle="tooltip" title="Advance repair module demo" style="color:white !important; background-color: #bc8f8f" data-admin="{{$demo_types['services']}}">
                    <i class="fas fa-wrench"></i>
                    Advance Repair Module</a>

            <a href="{{url('docs')}}" target="_blank" class="btn btn-app" data-toggle="tooltip" title="Advance repair module demo" style="color:white !important; background-color: #2dce89">
                    <i class="fas fa-network-wired"></i>
                    Connector Module / API Documentation</a>
        @endcomponent   
    </div>
    @endif

@stop

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function(){
            $('#change_lang').change( function(){
                window.location = "{{ route('login') }}?lang=" + $(this).val();
                // var id = $(this).val();
                // alert(id);
                // $.ajax({
                //     method: 'GET',
                //     url: '/lang/'+id,
                //     success: function(result) {
                //     },
                // });
            });

            // $('#test').click( function (e) {
            //    e.preventDefault();
            //    $('#username').val('admin 25	');
            //    $('#password').val("55555");
            //    $('form#login-form').submit();
            // });

            
        })
    </script>
@endsection
