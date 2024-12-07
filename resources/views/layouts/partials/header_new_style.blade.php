@inject('request', 'Illuminate\Http\Request')

@php
    $right =  in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) ? 'initial' : '10%'  ;
    $left  =  in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) ? '10%' : 'initial'  ;
@endphp

<style>
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
        padding: 15px 30px;
        text-align: right;
        font-size: 20px;
        /* border: 1px solid black; */
    }
    .icon-user{
        /* padding: 10px; */
    }
    .parent_left_header{

        padding:15px 50px;
    }
    .form-control-search{
        border: 2px solid #3a3a3a33; 
        border-radius: 10px;
        width:100%;
        /* background-color: #f7f7f7; */
        padding: 10px;
    }
    .content-header{
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
    }
    @media (max-width: 600px) {
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
            padding: 15px 30px;
            text-align: right;
            font-size: 20px;
            /* border: 1px solid black; */
        }
        .icon-user{
            /* padding: 10px; */
        }
        .parent_left_header{

            padding:15px 50px;
        }
        .form-control-search{
            border: 2px solid #3a3a3a33; 
            border-radius: 10px;
            width:100%;
            /* background-color: #f7f7f7; */
            padding: 10px;
        }
        .content-header{
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
        }
    }
    @media (min-width: 600px)  and  (max-width: 900px) {
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
            padding: 15px 30px;
            text-align: right;
            font-size: 20px;
            /* border: 1px solid black; */
        }
        .icon-user{
            /* padding: 10px; */
        }
        .parent_left_header{

            padding:15px 50px;
        }
        .form-control-search{
            border: 2px solid #3a3a3a33; 
            border-radius: 10px;
            width:100%;
            /* background-color: #f7f7f7; */
            padding: 10px;
        }
        .content-header{
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
        }
    }
    @media (min-width: 1024px) and  (max-width:1400px) {
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
            padding: 15px 30px;
            text-align: right;
            font-size: 20px;
            /* border: 1px solid black; */
        }
        .icon-user{
            /* padding: 10px; */
        }
        .parent_left_header{

            padding:15px 50px;
        }
        .form-control-search{
            border: 2px solid #3a3a3a33; 
            border-radius: 10px;
            width:100%;
            /* background-color: #f7f7f7; */
            padding: 10px;
        }
        .content-header{
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
        }
    }
    @media (min-width: 900px)  and  (max-width: 1024px){
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
            padding: 15px 30px;
            text-align: right;
            font-size: 20px;
            /* border: 1px solid black; */
        }
        .icon-user{
            /* padding: 10px; */
        }
        .parent_left_header{

            padding:15px 50px;
        }
        .form-control-search{
            border: 2px solid #3a3a3a33; 
            border-radius: 10px;
            width:100%;
            /* background-color: #f7f7f7; */
            padding: 10px;
        }
        .content-header{
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
<header class="main-header-new no-print">
     <div class="row">
        <div class="col-md-6 parent_left_header">
            {{-- <input type="search" class="form-control-search" placeholder="Search"> --}}
            {{-- 1 --}}
            <ul class="nav navbar-nav">
                @include('layouts.partials.header-notifications')
            </ul>
            {{-- 2 --}}
            <div class="col-md-4 m-8  mt-15 hidden-xs " style="margin-top:6px;color: #ee6800; background:#ffe8d9;border:1px solid #ee6800;border-radius:5px;padding:10px 20px 10px 20px;"><strong>{{ date('d - m - Y', strtotime(@format_date('now'))) }}</strong></div>
            

        </div>
        
        <div class="col-md-6 parent_right_header" dir="rtl">
                 <div class="item-one">
                    <ul class="nav navbar-nav">
                        <!-- User Account Menu -->
                        <li class="dropdown user user-menu">
                            <!-- Menu Toggle Button -->
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <!-- The user image in the navbar-->
                                @php
                                $profile_photo = auth()->user()->media;
                                @endphp
                                @if(!empty($profile_photo))
                                <img src="{{$profile_photo->display_url}}" class="user-image" alt="User Image">
                                @endif
                                <!-- hidden-xs hides the username on small devices so only the image appears. -->
                                <span>{{ Auth::User()->first_name }} {{ Auth::User()->last_name }}</span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- The user image in the menu -->
                                <li class="user-header">
                                @if(!empty(Session::get('business.logo')))
                                    <img src="{{ asset( 'public/uploads/business_logos/' . Session::get('business.logo') ) }}" alt="Logo">
                                @endif
                                
                                </li>
                                <li>
                                <p>
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
                        <li><a  href="{{action("ItemMoveController@showMovement",[1])}}"    >
                                <i class="fa fas fa-shopping-bag"  ></i> @lang('home.ItemMove')
                            </a></li>
                        <li><a aria-haspopup="true" href="{{action("AccountController@ledgerShow",[1])}}"   >
                            <i class="fa fas fa-arrow-right "  ></i> @lang('account.account_book')
                            </a></li>
                        </ul>
                    </div>
                 </div>
                 
            
                
                 <div class="item-one">
                    <button id="btnCalculator" title="@lang('lang_v1.calculator')" type="button" class="btn btn-success btn-flat pull-right m-8 btn-sm mt-10 popover-default hidden-xs" data-toggle="popover" data-trigger="click" data-content='@include("layouts.partials.calculator")' data-html="true" data-placement="bottom">
                        <strong><i class="fa fa-calculator fa-lg" aria-hidden="true"></i></strong>
                    </button>
                 </div>

                 <div class="item-one">
                    <button id="btnLanguage_shortcut_dropdown"  title="@lang('languages')" type="button" class="btn btn-success dropdown-toggle btn-flat  popover-default pull-right m-8 btn-sm mt-10" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-language fa-lg"></i>
                    </button>
                   <ul class="dropdown-menu lang "  >
                       @foreach($languages as $key => $i)
                            @if(in_array($key,['en','ar']))
                                <li>
                                <a  href="{{action("ItemMoveController@showMovement",[$key])}}"    >
                                <a  href="{{action("HomeController@changeLanguage",["lang"=>$key])}}"    >
                                    <i class="fa fas fa-language"  ></i> {{$i}}
                                </a>
                                </li>
                            @endif
                       @endforeach
                       </ul>
                </div>
            
           

             
            
        </div>
 
</header>