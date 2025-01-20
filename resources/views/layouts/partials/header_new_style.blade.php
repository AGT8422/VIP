@inject('request', 'Illuminate\Http\Request')

@php
    $pull               =  in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) ? 'pull-right' : 'pull-left'  ; 
    $left_menu_margin   =  in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) ? 'initial' : '20px'  ;
    $right_menu_margin  =  in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) ? '20px' : 'initial'  ;
    $right_profile      =  in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) ? 'initial' : '30px'  ;
    $left_profile       =  in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) ? '30px' : 'initial'  ;
    $right_menu         =  in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) ? 'initial' : '60px'  ;
    $left_menu          =  in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) ? '60px' : 'initial'  ;
    $right_1200         =  in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) ? 'initial' : '1%'  ;
    $left_1200          =  in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) ? '1%' : 'initial'  ;
    $right              =  in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) ? 'initial' : '10%'  ;
    $left               =  in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) ? '10%' : 'initial'  ;
    $right_mobile       =  in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) ? 'initial' : '0%'  ;
    $left_mobile        =  in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) ? '0%' : 'initial'  ;
    $txt                =  in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) ? 'left' : 'right'  ;
    $border_left        =  (in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl'))) ? '0px' : '5px';
    $border_right       =  (in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl'))) ? '5px' : '0px';
    $date_format        =  request()->session()->get('business.date_format'); 
@endphp
<style>
    .time_header{
        box-shadow:0px 0px 10px #3a3a3a33;
        margin-top:6px;
        color: #ee6800;
        background:#ffe8d9;
        border:1px solid #ee6800;
        border-radius:5px;
        padding:10px 20px 10px 20px;
        font-size:17px;
        text-align:center;
    }
            .sec-total {
            margin: auto 2% auto 2%;
            max-width: 1400px;
            }
    .company_name_style{
        padding-left:40px; 
        text-align: center;
        background-color: #f7f7f7;
        height: 79px;
        /* outline:1px solid #000; */
    }
    #header_shortcut_dropdown i{
        color:#ec8608;
    }
    #header_shortcut_dropdown{
        box-shadow: 0px 0px 20px #3a3a3a33;
        border-radius:10px;
        border:1px solid #ec8608;
        background-color: #ffe8d9;
    }
    #btnCalculator i{
        color:#ec8608 !important;
    }
    #btnCalculator{
        box-shadow: 0px 0px 20px #3a3a3a33;
        border-radius:10px !important;
        border:1px solid #ec8608 !important;
        background-color: #ffe8d9 !important;
    }
    #view_todays_profit i{
        color:#ec8608 !important;
    }
    #view_todays_profit{
        box-shadow: 0px 0px 20px #3a3a3a33;
        border-radius:10px !important;
        border:1px solid #ec8608 !important;
        background-color: #ffe8d9 !important;
    }
    #btnLanguage_shortcut_dropdown i{
        color:#ec8608 !important;
    }
    #btnLanguage_shortcut_dropdown{
        box-shadow: 0px 0px 20px #3a3a3a33;
        border-radius:10px !important;
        border:1px solid #ec8608 !important;
        background-color: #ffe8d9 !important;
    }
    .parent_right_header .item-one{
        display:inline-block;
        /* border:1px solid black; */
        /* padding:10px; */
        /* border-radius:10px; */
    }
    .parent_right_header{
        height: 80px;
        padding: 15px 0px;
        text-align: {{$txt}};
        font-size: 20px;
        /* border: 1px solid black; */
    }
    .icon-user{
        /* padding: 10px; */
    }
    .parent_left_header{

        padding:10px 50px;
    }
    .form-control-search{
        border: 2px solid #3a3a3a33; 
        border-radius: 10px;
        width:100%;
        /* background-color: #f7f7f7; */
        padding: 10px;
    }
    .user-image{
        margin-left: 10px !important;
        box-shadow: 0px 0px 10px #3a3a3a33 !important;
        outline:2px solid #000 !important;
        padding:1px !important;
    }
    .content-header h1{
        color:black !important;
    }
    .content-header{
        color:black !important;
        padding: 10px !important;
        border-left:{{$border_left}} solid #3a3a3a !important;
        border-right:{{$border_right}} solid #3a3a3a !important;
        box-shadow: 0px 0px 10px #3a3a3a33 !important;
        margin: 0px 1% !important;
    }
    .main-header-new{
        position: absolute;
        z-index: 1002;
        width: 60%;
        right:{{$right}};
        left: {{$left}};
        margin:  10px auto;
        box-shadow: 0px 0px 20px #3a3a3a33  ;
        background-color: #fff;
        height: 80px;
        border-radius: 20px;
        border:1px solid #f7f7f7;
        flex-direction: column-reverse
    }
    @media (max-width: 600px) {
            .sec-total {
            margin: auto 2% auto 2%;
            max-width: 1400px;
            }
        .company_name_style{
            padding-left:40px; 
            text-align: center;
            background-color: #f7f7f7;
            height: 79px;
            /* outline:1px solid #000; */
            display: none;
        }
        #header_shortcut_dropdown i{
            color:#ec8608;
        }
        #header_shortcut_dropdown{
            box-shadow: 0px 0px 20px #3a3a3a33;
            border-radius:10px;
            border:1px solid #ec8608;
            background-color: #ffe8d9;
            display: none !important;
        }
        
        #btnCalculator i{
            color:#ec8608 !important;
        }
        #btnCalculator{
            box-shadow: 0px 0px 20px #3a3a3a33;
            border-radius:10px !important;
            border:1px solid #ec8608 !important;
            background-color: #ffe8d9 !important;
        }
        #view_todays_profit i{
            color:#ec8608 !important;
        }
        #view_todays_profit{
            box-shadow: 0px 0px 20px #3a3a3a33;
            border-radius:10px !important;
            border:1px solid #ec8608 !important;
            background-color: #ffe8d9 !important;
        }
        #btnLanguage_shortcut_dropdown i{
        color:#ec8608 !important;
        }
        #btnLanguage_shortcut_dropdown{
            box-shadow: 0px 0px 20px #3a3a3a33;
            border-radius:10px !important;
            border:1px solid #ec8608 !important;
            background-color: #ffe8d9 !important;
        }
        .parent_right_header .item-one{
            display:inline-block;
            /* border:1px solid black; */
            /* padding:10px; */
            /* border-radius:10px; */
        }
        .parent_right_header{
            height: 80px;
            padding: 15px 0px;
            text-align: {{$txt}};
            font-size: 20px;
            display:none;
            /* border: 1px solid black; */
        }
        .icon-user{
            /* padding: 10px; */
        }
        .parent_left_header{
            display: none;
            padding:15px 50px;
        }
        .form-control-search{
            border: 2px solid #3a3a3a33; 
            border-radius: 10px;
            width:100%;
            /* background-color: #f7f7f7; */
            padding: 10px;
        }
        .user-image{
            margin-left: 10px !important;
            box-shadow: 0px 0px 10px #3a3a3a33 !important;
            outline:2px solid #000 !important;
            padding:1px !important;
        }
        .content-header h1{
            color:black !important;
        }
        
        .content-header{
            color:black !important;
            padding: 10px !important;
            border-left:{{$border_left}} solid #3a3a3a !important;
            border-right:{{$border_right}} solid #3a3a3a !important;
            box-shadow: 0px 0px 10px #3a3a3a33 !important;
            margin: 0px 1% !important;
        }
        .main-header-new{
            position: absolute;
            z-index: 1002;
            width: 100%;
            right:{{$right_mobile}};
            left: {{$left_mobile}};
            margin:  0px auto;
            box-shadow: 0px 0px 20px #3a3a3a33  ;
            background-color: #fff;
            height: 80px;
            border-radius: 0px;
            border:1px solid #f7f7f7;
        }
        .sec{
            /* padding-top: 100px !important;  */
        }
        #inside-content{
            width: 100% !important;
            margin: 0px !important;
        }
        .line_mobile{
            position: relative;
            display: block !important;
        }
        .menu_button .menu_button_span{
            cursor: pointer;
            position: relative;
            display: block;
            width:80%;
            height: 2px;
            padding:2px;
            background-color: #3a3a3a;
            border-radius: 10px;
            margin: 5px auto; 
        }
        .menu_button{
            cursor: pointer;
            box-shadow: 0px 0px 10px #3a3a3a33 !important;
            position: absolute;
            right:{{$right_menu}};
            left:{{$left_menu}};
            text-align: center;
            top:10px;
            width: 75px;
            height: auto;
            padding: 10px;
            border-radius: 10px;
            background-color: #dddddd;
            color: #000;

        }
        .profile{
            cursor: pointer;
            /* box-shadow: 0px 0px 10px #3a3a3a33 !important; */
            position: absolute;
            right:{{$right_profile}};
            left:{{$left_profile}};
            text-align: center;
            top:10px;
            /* width: 75px; */
            height: auto;
            /* padding: 10px; */
            /* border-radius: 10px; */
            /* background-color: #dddddd; */
            /* color: #000; */

        }
        .company_name{
            position: absolute;
            left: {{$left_menu_margin}};
            right: {{$right_menu_margin}};
        }
        .company_name .company{
            font-size: 14px;
        }
        
        
    }
    @media (min-width: 600px)  and  (max-width: 900px) {
            .sec-total {
            margin: auto 2% auto 2%;
            max-width: 1400px;
            }
        .company_name_style{
            padding-left:40px; 
            text-align: center;
            background-color: #f7f7f7;
            height: 79px;
            /* outline:1px solid #000; */
            display: none;
        }
        #header_shortcut_dropdown i{
            color:#ec8608;
            
        }
        #header_shortcut_dropdown{
            display: none !important;
            box-shadow: 0px 0px 20px #3a3a3a33;
            border-radius:10px;
            border:1px solid #ec8608;
            background-color: #ffe8d9;
        }
        
        #btnCalculator i{
            color:#ec8608 !important;
        }
        #btnCalculator{
            box-shadow: 0px 0px 20px #3a3a3a33;
            border-radius:10px !important;
            border:1px solid #ec8608 !important;
            background-color: #ffe8d9 !important;
        }
        #view_todays_profit i{
            color:#ec8608 !important;
        }
        #view_todays_profit{
            box-shadow: 0px 0px 20px #3a3a3a33;
            border-radius:10px !important;
            border:1px solid #ec8608 !important;
            background-color: #ffe8d9 !important;
        }
        #btnLanguage_shortcut_dropdown i{
        color:#ec8608 !important;
        }
        #btnLanguage_shortcut_dropdown{
            box-shadow: 0px 0px 20px #3a3a3a33;
            border-radius:10px !important;
            border:1px solid #ec8608 !important;
            background-color: #ffe8d9 !important;
        }
        .parent_right_header .item-one{
            display:inline-block;
            /* border:1px solid black; */
            /* padding:10px; */
            /* border-radius:10px; */
        }
        .parent_right_header{
            height: 80px;
            padding: 15px 10px;
            text-align: {{$txt}};
            font-size: 20px;
            display:none;
            /* border: 1px solid black; */
        }
        .icon-user{
            /* padding: 10px; */
        }
        .parent_left_header{
            display: none;
            padding:15px 50px;
        }
        .form-control-search{
            border: 2px solid #3a3a3a33; 
            border-radius: 10px;
            width:100%;
            /* background-color: #f7f7f7; */
            padding: 10px;
        }
        .user-image{
            margin-left: 10px !important;
            box-shadow: 0px 0px 10px #3a3a3a33 !important;
            outline:2px solid #000 !important;
            padding:1px !important;
        }
        .content-header h1{
            color:black !important;
        }
        
        .content-header{
            color:black !important;
            padding: 10px !important;
            border-left:{{$border_left}} solid #3a3a3a !important;
            border-right:{{$border_right}} solid #3a3a3a !important;
            box-shadow: 0px 0px 10px #3a3a3a33 !important;
            margin: 0px 1% !important;
        }
        .main-header-new{
            position: absolute;
            z-index: 1002;
            width: 100%;
            right:{{$right_mobile}};
            left: {{$left_mobile}};
            margin:  0px auto;
            box-shadow: 0px 0px 20px #3a3a3a33  ;
            background-color: #fff;
            height: 80px;
            border-radius: 0px;
            border:1px solid #f7f7f7;
        }
        .sec{
            /* padding-top: 100px !important;  */
        }
        #inside-content{
            width: 100% !important;
            margin: 0px !important;
        }
        .line_mobile{
            position: relative;
            display: block !important;
        }
        .menu_button .menu_button_span{
            cursor: pointer;
            position: relative;
            display: block;
            width:80%;
            height: 2px;
            padding:2px;
            background-color: #3a3a3a;
            border-radius: 10px;
            margin: 5px auto; 
        }
        .menu_button{
            cursor: pointer;
            box-shadow: 0px 0px 10px #3a3a3a33 !important;
            position: absolute;
            right:{{$right_menu}};
            left:{{$left_menu}};
            text-align: center;
            top:10px;
            width: 75px;
            height: auto;
            padding: 10px;
            border-radius: 10px;
            background-color: #dddddd;
            color: #000;

        }
        .company_name{
            position: absolute;
            left: {{$left_menu_margin}};
            right: {{$right_menu_margin}};
        }
        
    }
    @media (min-width: 1024px) and  (max-width:1200px) {
        #header_shortcut_dropdown{
            box-shadow: 0px 0px 20px #3a3a3a33;
            border-radius:10px;
            border:1px solid #ec8608;
            background-color: #ffe8d9;
            display: none !important;
        }
    }
    @media (min-width: 1024px) and  (max-width:1400px) {
            .sec-total {
            margin: auto 2% auto 2%;
            max-width: 1400px;
            }
        .company_name_style{
            padding-left:40px; 
            text-align: center;
            background-color: #f7f7f7;
            height: 79px;
            /* outline:1px solid #000; */
        }
        #header_shortcut_dropdown i{
            color:#ec8608;
        }
        #header_shortcut_dropdown{
            box-shadow: 0px 0px 20px #3a3a3a33;
            border-radius:10px;
            border:1px solid #ec8608;
            background-color: #ffe8d9;
        }
        
        #btnCalculator i{
            color:#ec8608 !important;
        }
        #btnCalculator{
            box-shadow: 0px 0px 20px #3a3a3a33;
            border-radius:10px !important;
            border:1px solid #ec8608 !important;
            background-color: #ffe8d9 !important;
        }
        #view_todays_profit i{
            color:#ec8608 !important;
        }
        #view_todays_profit{
            box-shadow: 0px 0px 20px #3a3a3a33;
            border-radius:10px !important;
            border:1px solid #ec8608 !important;
            background-color: #ffe8d9 !important;
        }
        #btnLanguage_shortcut_dropdown i{
        color:#ec8608 !important;
        }
        #btnLanguage_shortcut_dropdown{
            box-shadow: 0px 0px 20px #3a3a3a33;
            border-radius:10px !important;
            border:1px solid #ec8608 !important;
            background-color: #ffe8d9 !important;
        }
        .parent_right_header .item-one{
            display:inline-block;
            /* border:1px solid black; */
            /* padding:10px; */
            /* border-radius:10px; */
        }
        .parent_right_header{
            height: 80px;
            padding: 15px 10px;
            text-align: {{$txt}};
            font-size: 20px;
            /* border: 1px solid black; */
        }
        .icon-user{
            /* padding: 10px; */
        }
        .parent_left_header{
            padding:10px 50px;
        }
        .form-contro0-search{
            border: 2px solid #3a3a3a33; 
            border-radius: 10px;
            width:100%;
            /* background-color: #f7f7f7; */
            padding: 10px;
        }
        .user-image{
            margin-left: 10px !important;
            box-shadow: 0px 0px 10px #3a3a3a33 !important;
            outline:2px solid #000 !important;
            padding:1px !important;
        }
        .content-header h1{
            color:black !important;
        }
        
        .content-header{
            color:black !important;
            padding: 10px !important;
            border-left:{{$border_left}} solid #3a3a3a !important;
            border-right:{{$border_right}} solid #3a3a3a !important;
            box-shadow: 0px 0px 10px #3a3a3a33 !important;
            margin: 0px 1% !important;
        }
        .main-header-new{
            position: absolute;
            z-index: 1002;
            width: 80%;
            right:{{$right_1200}};
            left: {{$left_1200}};
            margin:  10px auto;
            box-shadow: 0px 0px 20px #3a3a3a33  ;
            background-color: #fff;
            height: 80px;
            border-radius: 20px;
            border:1px solid #f7f7f7;
            flex-direction: column-reverse
        }
        
    }
    @media (min-width: 900px)  and  (max-width: 1024px){
            .sec-total {
            margin: auto 2% auto 2%;
            max-width: 1400px;
            }
        .company_name_style{
            padding-left:40px; 
            text-align: center;
            background-color: #f7f7f7;
            height: 79px;
            /* outline:1px solid #000; */
            display: none;
            width: 10%;
        }
        #header_shortcut_dropdown i{
            color:#ec8608;
        }
        #header_shortcut_dropdown{
            display: none !important;
            box-shadow: 0px 0px 20px #3a3a3a33;
            border-radius:10px;
            border:1px solid #ec8608;
            background-color: #ffe8d9;
        }
        
        #btnCalculator i{
            color:#ec8608 !important;
        }   
        #btnCalculator{
            box-shadow: 0px 0px 20px #3a3a3a33;
            border-radius:10px !important;
            border:1px solid #ec8608 !important;
            background-color: #ffe8d9 !important;
        }
        #view_todays_profit i{
            color:#ec8608 !important;
        }   
        #view_todays_profit{
            box-shadow: 0px 0px 20px #3a3a3a33;
            border-radius:10px !important;
            border:1px solid #ec8608 !important;
            background-color: #ffe8d9 !important;
        }
        #btnLanguage_shortcut_dropdown i{
        color:#ec8608 !important;
        }
        #btnLanguage_shortcut_dropdown{
            box-shadow: 0px 0px 20px #3a3a3a33;
            border-radius:10px !important;
            border:1px solid #ec8608 !important;
            background-color: #ffe8d9 !important;
        }
        .parent_right_header .item-one{
            display:inline-block;
            /* border:1px solid black; */
            /* padding:10px; */
            /* border-radius:10px; */
        }
        .parent_right_header{
            width: 100%;
            height: 801x;
            padding: 10px 10px;
            text-align: {{$txt}};
            font-size: 20px;
            /* border: 1px solid black; */
        }
        .icon-user{
            /* padding: 10px; */
        }
        .parent_left_header{
            width: 45%;
            padding:15px 50px;
       }
        .form-control-search{
            border: 2px solid #3a3a3a33; 
            border-radius: 10px;
            width:100%;
            /* background-color: #f7f7f7; */
            padding: 10px;
        }
        .user-image{
            margin-left: 10px !important;
            box-shadow: 0px 0px 10px #3a3a3a33 !important;
            outline:2px solid #000 !important;
            padding:1px !important;
        }
        .content-header h1{
            color:black !important;
        }
        
        .content-header{
            color:black !important;
            padding: 10px !important;
            border-left:{{$border_left}} solid #3a3a3a !important;
            border-right:{{$border_right}} solid #3a3a3a !important;
            box-shadow: 0px 0px 10px #3a3a3a33 !important;
            margin: 0px 1% !important;
        }
        .main-header-new{
            text-align: center !important;
            position: absolute;
            display: flex;
            z-index: 1002;
            width: 80%;
            right:{{$right_1200}};
            left: {{$left_1200}};
            margin:  10px auto;
            box-shadow: 0px 0px 20px #3a3a3a33  ;
            background-color: #fff;
            height: 80px;
            border-radius: 20px;
            border:1px solid #f7f7f7;
            flex-direction: column-reverse
        }
        .main-header-new>div>div{ 
            /* border: 1px solid black; */
            /* width:calc(100%/3); */
        }
        .company_name_size{
            display: block !important;
        }
        .time_header{
            width:100% !important;
        }
        .row_header{
            display: none;
        }
        .header_mobile{
            display: block !important;
        }
        
    }

</style>
    @php
      $config_languages = config('constants.langs');
      $languages        = [];
      foreach ($config_languages as $key => $value) {
          $languages[$key] = $value['full_name'];
      }
    @endphp
<header class="main-header-new no-print" style="overflow: hidden">
    <div class="row row_header" style="overflow: hidden">
            <div class="col-md-3 col-3  text-center company_name_style">  <h3  style="font-size: 16px;max-width: 200px;font-weight:bolder;text-overflow: ellipsis;"> {{ Session::get('business.name') }}</h3> </div>
            <div class="col-md-3 col-3 parent_left_header">
                {{-- <input type="search" class="form-control-search" placeholder="Search"> --}}
                {{-- 1 --}}
                {{-- <ul class="nav navbar-nav">
                    @include('layouts.partials.header-notifications')
                </ul>--}}
                {{-- 2 --}}
            
                <div class="col-md-12 m-8  mt-15 hidden-xs time_header"  ><strong>{{  \Carbon::now()->format($date_format) }}</strong></div> 
            </div>
            
            <div class="col-md-6 col-6 parent_right_header" @if(in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl'))) dir="ltr" @else dir="rtl" @endif>
                <div class="item-one" >
                    <ul class="nav navbar-nav">
                        <!-- User Account Menu -->
                        <li class="dropdown user user-menu" style="position: relative">
                            <!-- Menu Toggle Button -->
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <!-- The user image in the navbar-->
                                @php
                                    $profile_photo = auth()->user()->media;
                                @endphp
                                @if(!empty($profile_photo))
                                    <img style="position: relative;" src="{{$profile_photo->display_url}}" class="user-image pull-right"  alt="User Image">
                                @else
                                    <img style="position: relative;" src="https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_640.png" class="user-image pull-right"  alt="User Image">
                                @endif
                                <!-- hidden-xs hides the username on small devices so only the image appears. -->
                                <span style="font-size: 14px;">{{ Auth::User()->first_name }} {{ Auth::User()->last_name }}</span>
                            </a>
                            @php 
                                $company_name = request()->session()->get("user_main.domain");
                            @endphp
                            <ul class="dropdown-menu">
                                <!-- The user image in the menu -->
                                <li class="user-header">
                                    @if(!empty(Session::get('business.logo'))) <img src="{{ asset( 'uploads/companies/'.$company_name.'/business_logo/' . Session::get('business.logo') ) }}" alt="Logo"> @endif
                                </li>
                                <!-- Menu Body -->
                                <li>
                                    <p class="text-center">  {{ Auth::User()->first_name }} {{ Auth::User()->last_name }} </p>
                                </li>
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-left">
                                        <a href="{{action('UserController@getProfile')}}" class="btn btn-default btn-flat">@lang('lang_v1.profile')</a>
                                    </div>
                                    <div class="pull-right">
                                        <a href="{{action('Auth\LoginController@logout')}}" class="btn btn-default btn-flat">@lang('lang_v1.sign_out')</a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <!-- Control Sidebar Toggle Button -->
                    </ul>  
                </div>

                <div class="item-one " style="position:relative;top: -19px !important">
                    <div class="btn-group">
                        <button id="header_shortcut_dropdown" type="button" class="btn btn-success dropdown-toggle btn-flat pull-right m-8 btn-sm mt-10" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-plus-circle fa-lg"></i>
                        </button>
                        <ul class="dropdown-menu">
                        @if(config('app.env') != 'demo')
                            <li><a href="{{route('calendar')}}">
                                <i class="fas fa-calendar-alt" aria-hidden="true"></i> @lang('lang_v1.calendar')
                            </a></li>
                        @endif
                        @if(Module::has('Essentials'))
                            <li><a href="#" class="btn-modal" data-href="{{action('\Modules\Essentials\Http\Controllers\ToDoController@create')}}" data-container="#task_modal">
                                <i class="fas fa-clipboard-check" aria-hidden="true"></i> @lang( 'essentials::lang.add_to_do' )
                            </a></li>
                        @endif
                        <!-- Help Button -->
                        @if(auth()->user()->hasRole('Admin#' . auth()->user()->business_id))
                            <li><a id="start_tour" href="#">
                                <i class="fas fa-question-circle" aria-hidden="true"></i> @lang('lang_v1.application_tour')
                            </a></li>
                        @endif
                        
                        </ul>
                    </div>
                </div>

                <div class="item-one" style="position:relative;top: 0px !important">
                    <button id="btnCalculator" title="@lang('lang_v1.calculator')" type="button" class="btn btn-success btn-flat pull-right m-8 btn-sm mt-10 popover-default hidden-xs" data-toggle="popover" data-trigger="click" data-content='@include("layouts.partials.calculator")' data-html="true" data-placement="bottom">
                        <strong><i class="fa fa-calculator fa-lg" aria-hidden="true"></i></strong>
                    </button>
                </div>
                        
                <div class="item-one" style="position:relative;top: 0px !important">
                    <button title="@lang('home.ItemMove')"  class="btn btn-success btn-flat pull-right m-8 btn-sm mt-10 popover-default hidden-xs" id="btnCalculator" >
                        <a href="{{action('ItemMoveController@showMovement',[1])}}"    
                            <strong><i class="fa fas fa-shopping-bag"  ></i></strong> 
                        </a> 
                    </button>
                </div>

                <div class="item-one" style="position:relative;top: 0px !important">
                    <button title="@lang('account.account_book')"  class="btn btn-success btn-flat pull-right m-8 btn-sm mt-10 popover-default hidden-xs" id="btnCalculator" >
                        <a href="{{action('AccountController@ledgerShow',[1])}}"    
                            <strong><i class="fa fas fa-arrow-right"  ></i></strong> 
                        </a> 
                    </button>
                </div>

                <div class="item-one" style="position:relative;top: 0px !important">
                    <button id="btnLanguage_shortcut_dropdown"  title="@lang('languages')" type="button" class="btn btn-success dropdown-toggle btn-flat  popover-default pull-right m-8 btn-sm mt-10" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fas fa-globe"></i>
                    </button>
                    <ul class="dropdown-menu lang "  >
                        @foreach($languages as $key => $i)
                            @if(in_array($key,['en','ar']))
                                <li>
                                <a  href="{{action("ItemMoveController@showMovement",[$key])}}"    >
                                <a  href="{{action("HomeController@changeLanguage",["lang"=>$key])}}"    >
                                        {{$i}}
                                </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="header_mobile" style="display:none ; text-align:center !important; ">
            <div class="parent_right_header" @if(in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl'))) dir="ltr" @else dir="rtl" @endif>
                <div class="item-one" style="margin:0px ;" >
                    <ul class="nav navbar-nav">
                        <!-- User Account Menu -->
                        <li class="dropdown user user-menu" style="position: relative">
                            <!-- Menu Toggle Button -->
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <!-- The user image in the navbar-->
                                @php
                                $profile_photo = auth()->user()->media;
                                @endphp
                                @if(!empty($profile_photo))
                                    <img style="position: relative;" src="{{$profile_photo->display_url}}" class="user-image pull-right"  alt="User Image">
                                @else
                                    <img style="position: relative;" src="https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_640.png" class="user-image pull-right"  alt="User Image">
                                @endif
                                <!-- hidden-xs hides the username on small devices so only the image appears. -->
                                <span style="font-size: 14px;">{{ Auth::User()->first_name }} {{ Auth::User()->last_name }}</span>
                            </a>
                            @php 
                            $company_name = request()->session()->get("user_main.domain");
                            @endphp
                            <ul class="dropdown-menu">
                                <!-- The user image in the menu -->
                                <li class="user-header">
                                @if(!empty(Session::get('business.logo')))
                                    <img src="{{ asset( 'uploads/companies/'.$company_name.'/business_logo/' . Session::get('business.logo') ) }}" alt="Logo">
                                @endif
                                
                                </li>
                                <li>
                                <p class="text-center">
                                    {{ Auth::User()->first_name }} {{ Auth::User()->last_name }}
                                </p>
                                </li>
                                <!-- Menu Body -->
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                <div class="pull-left">
                                    <a href="{{action('UserController@getProfile')}}" class="btn btn-default btn-flat">@lang('lang_v1.profile')</a>
                                </div>
                                <div class="pull-right">
                                    <a href="{{action('Auth\LoginController@logout')}}" class="btn btn-default btn-flat">@lang('lang_v1.sign_out')</a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <!-- Control Sidebar Toggle Button -->
                    </ul>  
                </div>

                <div class="item-one " style="position:relative;top: -19px !important">
                    <div class="btn-group">
                        <button id="header_shortcut_dropdown" type="button" class="btn btn-success dropdown-toggle btn-flat pull-right m-8 btn-sm mt-10" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-plus-circle fa-lg"></i>
                        </button>
                        <ul class="dropdown-menu">
                        @if(config('app.env') != 'demo')
                            <li><a href="{{route('calendar')}}">
                                <i class="fas fa-calendar-alt" aria-hidden="true"></i> @lang('lang_v1.calendar')
                            </a></li>
                        @endif
                        @if(Module::has('Essentials'))
                            <li><a href="#" class="btn-modal" data-href="{{action('\Modules\Essentials\Http\Controllers\ToDoController@create')}}" data-container="#task_modal">
                                <i class="fas fa-clipboard-check" aria-hidden="true"></i> @lang( 'essentials::lang.add_to_do' )
                            </a></li>
                        @endif
                        <!-- Help Button -->
                        @if(auth()->user()->hasRole('Admin#' . auth()->user()->business_id))
                            <li><a id="start_tour" href="#">
                                <i class="fas fa-question-circle" aria-hidden="true"></i> @lang('lang_v1.application_tour')
                            </a></li>
                        @endif
                        
                        </ul>
                    </div>
                </div>
                    
                <div class="item-one" style="position:relative;top: 0px !important">
                    <button id="btnCalculator" title="@lang('lang_v1.calculator')" type="button" class="btn btn-success btn-flat pull-right m-8 btn-sm mt-10 popover-default hidden-xs" data-toggle="popover" data-trigger="click" data-content='@include("layouts.partials.calculator")' data-html="true" data-placement="bottom">
                        <strong><i class="fa fa-calculator fa-lg" aria-hidden="true"></i></strong>
                    </button>
                </div>
                        
                <div class="item-one" style="position:relative;top: 0px !important">
                    <button title="@lang('home.ItemMove')"  class="btn btn-success btn-flat pull-right m-8 btn-sm mt-10 popover-default hidden-xs" id="btnCalculator" >
                        <a href="{{action('ItemMoveController@showMovement',[1])}}"    
                            <strong><i class="fa fas fa-shopping-bag"  ></i></strong> 
                        </a> 
                    </button>
                </div>
                <div class="item-one" style="position:relative;top: 0px !important">
                    <button title="@lang('account.account_book')"  class="btn btn-success btn-flat pull-right m-8 btn-sm mt-10 popover-default hidden-xs" id="btnCalculator" >
                        <a href="{{action('AccountController@ledgerShow',[1])}}"    
                            <strong><i class="fa fas fa-arrow-right"  ></i></strong> 
                        </a> 
                    </button>
                </div>
                <div class="item-one" style="position:relative;top: 0px !important">
                    <button id="btnLanguage_shortcut_dropdown"  title="@lang('languages')" type="button" class="btn btn-success dropdown-toggle btn-flat  popover-default pull-right m-8 btn-sm mt-10" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fas fa-globe"></i>
                    </button>
                    <ul class="dropdown-menu lang "  >
                        @foreach($languages as $key => $i)
                            @if(in_array($key,['en','ar']))
                            <li>
                            <a  href="{{action("ItemMoveController@showMovement",[$key])}}"    >
                                <a  href="{{action("HomeController@changeLanguage",["lang"=>$key])}}"    >
                                    {{$i}}
                                </a>
                            </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
                <div class="item-one" style="text-align:center;width:150px !important;position:relative;top: -20px !important;height:80px;background-color:#f7f7f7;">
                    <div  > <h3  style=" font-size: 10px; font-weight: bolder;max-width: 200px;overflow: hidden;  text-overflow: ellipsis;"> {{ Session::get('business.name') }}</h3> </div>
                </div>
                <div class="item-one" style="position:relative;top: -17px !important">
                    <div class="time_header"  ><strong>{{  \Carbon::now()->format($date_format) }}</strong></div>
                </div>
            </div>
        </div>
        {{-- mobile-sec --}}
        <div class="line_mobile" style="display: none;">
            <div class="menu_button">
                <span class="menu_button_span"></span>
                <span class="menu_button_span"></span>
                <span class="menu_button_span"></span>
            </div>
            <div class="profile">
                <ul class="nav navbar-nav">
                    <!-- User Account Menu -->
                    <li class="dropdown user user-menu" style="position: relative">
                        <!-- Menu Toggle Button -->
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <!-- The user image in the navbar-->
                            @php
                            $profile_photo = auth()->user()->media;
                            @endphp
                            @if(!empty($profile_photo))
                                <img style="position: relative;" src="{{$profile_photo->display_url}}" class="user-image pull-right"  alt="User Image">
                            @else
                                <img style="position: relative;" src="https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_640.png" class="user-image pull-right"  alt="User Image">
                            @endif
                            <!-- hidden-xs hides the username on small devices so only the image appears. -->
                            {{-- <span style="font-size: 14px;">{{ Auth::User()->first_name }} {{ Auth::User()->last_name }}</span> --}}
                        </a>
                        @php 
                        $company_name = request()->session()->get("user_main.domain");
                        @endphp
                        <ul class="dropdown-menu">
                            <!-- The user image in the menu -->
                            <li class="user-header">
                            @if(!empty(Session::get('business.logo')))
                                <img src="{{ asset( 'uploads/companies/'.$company_name.'/business_logo/' . Session::get('business.logo') ) }}" alt="Logo">
                            @endif
                            
                            </li>
                            <li>
                            <p class="text-center">
                                {{ Auth::User()->first_name }} {{ Auth::User()->last_name }}
                            </p>
                            </li>
                            <!-- Menu Body -->
                            <!-- Menu Footer-->
                            <li class="user-footer">
                            <div class="pull-left">
                                <a href="{{action('UserController@getProfile')}}" class="btn btn-default btn-flat">@lang('lang_v1.profile')</a>
                            </div>
                            <div class="pull-right">
                                <a href="{{action('Auth\LoginController@logout')}}" class="btn btn-default btn-flat">@lang('lang_v1.sign_out')</a>
                                </div>
                            </li>
                        </ul>
                    </li>
                    <!-- Control Sidebar Toggle Button -->
                </ul>  
            </div>
            <div class="company_name {{$pull}}">
                
                <h2  class="company {{$pull}}" style="max-width: 200px;overflow: hidden;  text-overflow: ellipsis;"> 
                    
                    <b style="white-space: nowrap;">
                        {{ Session::get('business.name') }}
                    </b>
                </h2>
            </div>
        </div>
 
</header>
 

 