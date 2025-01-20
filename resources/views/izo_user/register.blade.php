@extends('izo_user.layouts.app')

@section('title','register')
@php
    $left_toggle         = in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')) ? '50px' : 'initial';
    $right_toggle        = in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')) ? 'initial' : '50px';
    $left_box            = in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')) ? 'initial' : '50px';
    $right_box           = in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')) ? '50px' : 'initial';
    $translate           = in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')) ? '50%' : '-50%';
    $margin_left         = in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')) ? 'initial' : '50%';
    $margin_right        = in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')) ? '50%' : 'initial';
    $left_margin         = in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')) ? 'initial' : '10px';
    $right_margin        = in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')) ? '10px' : 'initial';
    $parent_left_margin  = in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')) ? 'initial' : '-40px';
    $parent_right_margin = in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')) ? '-40px' : 'initial';
    $padding_left        = in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')) ? 'initial' : '130px';
    $padding_right       = in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')) ? '130px' : 'initial';
    $left_mobile         = in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')) ? 'initial' : '20px';
    $right_mobile        = in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')) ? '20px' : 'initial';
    $before_left_mobile  = in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')) ? 'initial' : '4px';
    $before_right_mobile = in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')) ? '4px' : 'initial';
@endphp
@section('content')
    @section('app_css')
        <style>
            /* body{
                box-sizing: border-box;
            /* }  
                background-image:url("../../../uploads/IZO-D2.gif");
                background-size: cover;
                background-attachment: fixed;
                background-repeat:  no-repeat; 
                background-position: center;
            .left_form_first_login{
                border: 0px solid black;
                border-radius: 10px 10px 10px 10px;/
                  border-radius: 10px 0px 0px 10px; 
                padding: 10px;
                box-shadow:1px 1px 100px #797979;
                background: rgb(255, 255, 255);
            }
            .right_form_first_login{
            display: none !important;
                border: 0px solid black;
                border-radius: 0px 10px 10px 0px;
                padding: 10px; 
                background: rgba(64, 64, 110, 0);
                background-image: url("../../../uploads/dubai.jpg");
                background-position: right center;
                background-repeat:no-repeat;
                background-attachment:fixed;
                background-size: cover;
                overflow: hidden;
            }
            .mainbox{
                border: 0px solid rgb(249, 0, 0);
                border-radius: 10px;
                padding: 10px 30px;
                background: rgba(0, 0, 0, 0);
                width: 80%;
                height: 100vh;
                display: flex;
                justify-content: center;
            }
            .childbox{
                border: 0px solid rgb(249, 0, 0);
                width: 100%;
                border-radius: 10px;
                padding: 10px 30px;
                max-height: 70%;
                transform: translateY(25%);
                background: rgba(93, 237, 9, 0); 
                display: flex;
                justify-content: center;
            }
            .description{
                text-align: center;
                line-height:30px;
                font-family: Arial, Helvetica, sans-serif;
                font-size: 20px;
                text-transform: capitalize;
                font-weight: bolder;
                margin:50px auto ;
                color: #303030;
            }
            .title_izo{
               
                margin:60px 15% ;
                text-align: left;
                line-height:70px;
                padding-left: 20px;
                font-family: Arial, Helvetica, sans-serif;
                font-size: 75px;
                text-transform: capitalize;
                font-weight: bolder;
                width: auto !important;
                color: antiquewhite;
                /* border-bottom: 10px solid #ff954a; */
            /* }
            .izo-form-i-passwordnput:focus{
            border-color: #ec6808 !important;}
            outline:1px solid #ec6808 !important;}
            .izo-form-input:focus{
                    border-color:#ec6808 !important;
                 outlinel 1px solid or:#ec6808 !important;
                }
            .link_code{
            font-size: 10px14}
                .code_activate_input{
                font-size: 18px !important;
            font-family: arial !important;
            text-align: center;
            max-width:150p20!important;}
                .izo-form-input{
            font-family:Georgia, 'Times New Roman', Times, serif;
                width: 100%;
                border-radius: 0px;
                padding: 10px;
                margin: 10px auto ;
                font-size: 17px;
                border-radius: 10px;
                border: 1px solid #3a3a3a33;
            }
            .izo-form-input-password{
            font-family:Georgia, 'Times New Roman', Times, serif;
                width: 100% !important;
                border-radius: 10px !important;
                padding: 10px !important;
                margin: 10px auto  !important;
                font-size: 165px !important;
                border: 1px solid #3a3a3a33 !important
            }
            .izo-group-form{  
                display: flex;
                justify-items: center; 
            }
            .izo-form-input-readOnly{
            font-family:Georgia, 'Times New Roman', Times, serif;   
                width: 60%;
                border-radius: 1px;
                padding: 10px;
                margin: 10px auto ;
                border: 1px solid #e3a3a3a33
                background-color: 3e3e3e3300
                color: #3a3a3a !important;
                font-size: 15px;
                text-align: center;
                font-weight: bolder;
            }
            .izo-form-input-mobile{
            font-family:Georgia, 'Times New Roman', Times, serif;   
            font-family: arial;    
            width: 100%;
                border-radius: 0px;
                padding: 10px;
                margin: 10px auto ;
                border: 10x solid #313131;
                background-color: #fefefe;
                color: rgb(70, 70, 70) !important;
                font-size: 16px;
                font-weight: bolder;
            }
            
            .izo-form-input-save{
            font-family:Georgia, 'Times New Roman', Times, serif;
            cursor: pointer;
            text-align: center;    
                width: 100%;
                border-radius: 10px;
                padding: 10px;
                margin: 10px auto ;
                border: 1px solid #ec6808;
                background-color: #ec6808;
                color: white !important;
                font-size: 20px;
                font-weight: bolder;
            /* } */  
            .custom-select {
            position: absolute;
            display: inline-block;
            width: 20%;
            top:16px;
            left:{{$left_mobile}};
            right:{{$right_mobile}};
            font-size: 17px; 
        }
        .select-selected .text_flag{
            display: none;
        }
        .select-selected {
            background-color: #f9f9f900;
            border: 1px solid #dddddd00;
            padding: 8px 16px;
            cursor: pointer;
            display: flex;
            font-size: 14px;
            height: 30px;
            align-items: center;
        }
        .select-selected::after {
            content: "";
            position: absolute;
            top: 14px;
            right: {{$before_left_mobile}};
            left: {{$before_right_mobile}};
            border: 4px solid transparent;
            border-color: #000 transparent transparent transparent;
        }
        .select-selected.select-arrow-active::after {
            border-color: transparent transparent #000 transparent;
            top: 7px;
        }
        .select-items  .flag-icon {
            width: 32px !important;
            height: 32px !important;
        }
        .select-items {
            position: absolute;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            width: 100%;
            z-index: 99;
            max-height: 200px;
            overflow-y: auto;
            display: none;
            font-size: 10px ;
        }
        .select-items div {
            padding: 8px 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
        }
        .select-items div:hover {
            background-color: #ddd;
        }
        .same-as-selected {
            background-color: #ddd;
        }
            .toggle-password {
                position: absolute;
                top: 45px;
                left:{{$left_toggle}} ;
                right: {{$right_toggle}};
                transform: translateY(-50%);
                cursor: pointer;
            }

            /* Optional: Style for the eye icon */
            .eye-icon::before {
                content: '\1F441'; /* Unicode character for an eye symbol */
                font-size: 1.5em;
            }

            .flag-container {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }
            .flag-item {
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .flag-icon {
                margin :0% 5px ; 
                width: 22px;
                height: 22px;
            }
            .logo-style{                      
                margin: 15px auto  ;
                width: 30%;
            }
            .sign-up-box a{
                        color: #0c0c0c !important;
                }
                .sign-up-box{
                    position: relative;
                    background-color:#3a3a3a33 !important;
                    padding: 10px !important;
                    margin-left:{{$margin_left}} !important;
                    margin-right:{{$margin_right}} !important;
                    width:94% !important;
                    border-radius: 10px !important;
                    transform: translateX({{$translate}}) !important;
                }
                .sign-up-form{
                    font-family:Georgia, 'Times New Roman', Times, serif;
                    position: relative;
                    font-size:18px;text-decoration:underline;
                    padding: 10px !important;
                        /* background-color: #838383 !important; */
                        width: 100% !important; 
                }
            .loading{
                display: none;
                position: fixed;
                left: 0px;
                right: 0px;
                width: 100%;
                height: 100%;
                z-index: 1000;
                background-color: rgb(0, 0, 0);
            }
            .loading .loading-content{
                position: relative; 
                margin: 50vh auto; 
                transform: translateX(-50%);
                transform: translateY(-50%);
                width: 200px;
                height: 100px;
                z-index: 1000;
                color: #fefefe;
                font-size: 20px;
                font-weight: 700;
                background-color: rgba(175, 28, 28, 0);
            }
            .loading .loading-content h1{ 
                color: #fefefe;  
                font-weight: bold;
            }
            body{
                box-sizing: border-box;
                /* background:linear-gradient(to top left ,rgb(235, 249, 255) 10% , rgb(255, 239, 223) 40% ,#cecece 100%); */
                /* background-image:url("../../../uploads/IZO-D2.gif"); */
                background-size: cover;
                background-attachment: fixed;
                background-repeat:  no-repeat; 
                background-position: center;   
                background-color:#fff !important; 
            }
            .left_form_first_login{
                border: 0px solid black;
                border-radius: 10px 10px 10px 10px;
                /* border-radius: 10px 0px 0px 10px; */
                padding: 10px;
                box-shadow:1px 1px 100px #797979;
                background: rgb(255, 255, 255);
            }
            .right_form_first_login{
                display: none !important;
                border: 0px solid black;
                border-radius: 0px 10px 10px 0px;
                padding: 10px; 
                background: rgba(64, 64, 110, 0);
                background-image: url("../../../uploads/dubai.jpg");
                background-position: right center;
                background-repeat:no-repeat;
                background-attachment:fixed;
                background-size: cover;
                overflow: hidden;
            }
            .mainbox{
                border: 0px solid rgb(249, 0, 0);
                border-radius: 10px;
                padding: 10px 30px;
                background: rgba(0, 0, 0, 0);
                width: 80%;
                height: auto;
                display: flex;
                justify-content: center;
            }
            .childbox{
                border: 0px solid rgb(249, 0, 0);
                width: 80%;
                border-radius: 10px;
                padding: 10px 30px;
                height: auto;
                transform: translateY(0%);
                background: rgba(93, 237, 9, 0); 
                display: flex;
                justify-content: center;
            }
            .description{
                text-align: left;
                line-height:40px;
                font-family:Georgia, 'Times New Roman', Times, serif;
                font-size: 40px;
                padding-left: 20px;
                text-transform: capitalize;
                font-weight: bolder;
                margin:20px 5% ;
            }
            .description_small{
                background-image: url('../../../uploads/dubai.jpg');
                background-size: cover; 
                background-position:  right;
                background-repeat: no-repeat; 
                /* box-shadow: 1px 1px 10px black  ; */
                text-align: center;
                line-height:30px;
                font-family:Georgia, 'Times New Roman', Times, serif;
                font-size: 20px;
                text-transform: capitalize;
                font-weight: 300;
                /* margin:50px auto ; */
                height: 200px; ;
            }
            .title_izo{
                position: absolute;
                bottom:10px;
                right:-10px;
                margin:20px 10% ;
                text-align: left;
                line-height:70px;
                padding-left: 20px;
                font-family:Georgia, 'Times New Roman', Times, serif;
                font-size: 75px;
                text-transform: capitalize;
                font-weight: bolder;
                width: auto !important;
                color: whitesmoke;
                /* border-bottom: 10px solid #ff954a; */
            }
            .izo-form-input-password:focus{
                border-color: #ec6808 !important;
                outline:1px solid #ec6808 !important;
            }
            .izo-form-input:focus{
                    border-color:#ec6808 !important;
                outlineo 1px solid lor:#ec6808 !important;
                }
            .link_code{
                font-size: 14px;
            }
                .code_activate_input{
                    font-size: 18px !important;
                font-family: arial !important;
                text-align: center;
                max-width:200px !important;
            }
                .izo-form-input{
                font-family:Georgia, 'Times New Roman', Times, serif;
                width: 100%;
                border-radius: 0px;
                padding: 10px;
                margin: 10px auto ;
                font-size: 17px;
                border-radius: 10px;
                border: 1px solid #3a3a3a33;
            }
            .izo-form-input-password{
                font-family:Georgia, 'Times New Roman', Times, serif;
                width: 100% !important;
                border-radius: 10px !important;
                padding: 10px !important;
                margin: 10px auto  !important;
                font-size: 16px !important;
                border: 1px solid #3a3a3a33  
            }
            .izo-group-form{  
                display: flex;
                justify-items: center; 
            }
            .izo-form-input-readOnly{
                font-family:Georgia, 'Times New Roman', Times, serif;   
                width: 60%;
                border-radius: 0px;
                padding: 10px;
                margin: 10px auto ;
                border: 1px solid #3a3a3a33;
                background-color: #3e3e3e33;
                color: #3a3a3a !important;
                font-size: 15px;
                text-align: center;
                font-weight: bolder;
            }
            :-ms-input-placeholder{
                 color:#e86000;
            }
            .izo-form-input-mobile{
                font-family:Georgia, 'Times New Roman', Times, serif;   
                font-family: arial;
                width: 100%;
                border-radius: 0px;
                padding: 10px;
                margin: 10px auto ;
                border: 0px solid #313131;
                background-color: #fefefe;
                color: rgb(70, 70, 70) !important;
                font-size: 16px;
                font-weight: bolder;
            }
            
            .izo-form-input-save{
                font-family:Georgia, 'Times New Roman', Times, serif;
                cursor: pointer;
                text-align: center;   
                width: 100%;
                border-radius: 10px;
                padding: 10px;
                margin: 10px auto ;
                border: 1px solid #ec6808;
                background-color: #ec6808;
                color: white !important;
                font-size: 20px;
                font-weight: bolder;
            }
            .form_active h3{
                color:black;
                font-family: Georgia, 'Times New Roman', Times, serif;
            }
            .form_active h1{
                color:black;
                font-family: Georgia, 'Times New Roman', Times, serif;
            }
            .form_active{
                    font-family: Georgia, 'Times New Roman', Times, serif;
                position: absolute;
                transform: translate(-50%,-50%);
                left: 50%;
                top: 50%;
                max-width: 450px;
                border-radius:10px;
                background-color:white;
                box-shadow:0px 0px 10px #3a3a3a33;
                padding:30px;
                padding-top:50px;
                padding-bottom:80px;
            }
            .activation_code_content{
                    overflow: hidden;
                    position: fixed;
                    z-index: 4000;
                    width:100%;
                    height: 100%;
                    background-color: #f7f7f7;
                    color: #3a3a3a;
                    font-size: 20px;
                    font-family: Georgia, 'Times New Roman', Times, serif;
                    text-align: center;

                }
            .activation_code{
                display: none;
                overflow: hidden;
                position: fixed;
                z-index: 4000;
                width:100%;
                height: 100%;
                background-color: #f7f7f700;
                color: #3a3a3a;
                font-size: 20px;
                font-family: Georgia, 'Times New Roman', Times, serif;
                text-align: center;

            }
            .list_of_lang{
                        background-color:#f7f7f7;
                        border-radius:10px;
                        padding:10px ;
                        position:absolute;
                        left:{{$left_box}};
                        right:{{$right_box}};
                        top:80px;
                        width:150px;
                        font-size:20px;
                        box-shadow: 0px 0px 10px #f7f7f7;
                    }
            .language_box i{
                        color:#ec6808;
                }
            .language_box{
                font-size: 18px;
                cursor: pointer;
                font-weight: 700;
                position:absolute;
                box-shadow: 0px 0px 10px #f7f7f7;
                text-align: center;
                left:{{$left_box}};
                right:{{$right_box}};
                top:20px;
                line-height: 50px;
                background-color:white !important;
                height:50px;
                width:50px;
                border-radius:100px !important;
                color:black;
            }
            .title_main{
                    display: none;
            }
            .right_form_first_login_top{
                border: 2px solid black;
                border-radius: 10px;
                padding: 10px; 
                background: rgba(64, 64, 110, 0);
                display: none;
            }
            .field_icon{
                position: relative;
            }
            .field_icon i{
                position: absolute;
                margin: 4% -5%;
            }
            .field_icon_mobile i{
                position: absolute;
                right:0px;
                margin: 4% 5%;
            }
            .field_icon_domain i{
                position: absolute;
                left:0px;
                margin: 4% -.5%;
            }
            .hide_icon{
                    display: none
            }
            .show_icon{
                display: block

            }
            .success-icon{
                color: green;
            }
            .fa-times-circle{
                color: red;
            }
            @media (max-width: 600px) {
                .form_active h3{
                    color:black;
                    font-family: Georgia, 'Times New Roman', Times, serif;
                }
                .form_active h1{
                    color:black;
                    font-family: Georgia, 'Times New Roman', Times, serif;
                }
                .form_active{
                    font-family: Georgia, 'Times New Roman', Times, serif;
                    position: absolute;
                    transform: translate(-50%,-50%);
                    left: 50%;
                    top: 50%;
                    max-width: 450px;
                    border-radius:10px;
                    background-color:white;
                    box-shadow:0px 0px 10px #3a3a3a33;
                    padding:30px;
                    padding-top:50px;
                    padding-bottom:80px;
                }
                .activation_code_content{
                    overflow: hidden;
                    position: fixed;
                    z-index: 4000;
                    width:100%;
                    height: 100%;
                    background-color: #f7f7f7;
                    color: #3a3a3a;
                    font-size: 20px;
                    font-family: Georgia, 'Times New Roman', Times, serif;
                    text-align: center;

                }
                .activation_code{
                    display: none;
                    overflow: hidden;
                    position: fixed;
                    z-index: 4000;
                    width:100%;
                    height: 100%;
                    background-color: #f7f7f700;
                    color: #3a3a3a;
                    font-size: 20px;
                    font-family: Georgia, 'Times New Roman', Times, serif;
                    text-align: center;

                }
                .list_of_lang{
                    background-color:#f7f7f7;
                    border-radius:10px;
                    padding:10px ;
                    position:absolute;
                    left:{{$left_box}};
                    right:{{$right_box}};
                    top:80px;
                    width:150px;
                    font-size:20px;
                    box-shadow: 0px 0px 10px #f7f7f7;
                }
                .language_box i{
                    color:#ec6808;
                }
                .language_box{
                    font-size: 18px;
                    cursor: pointer;
                    font-weight: 700;
                    position:absolute;
                    box-shadow: 0px 0px 10px #f7f7f7;
                    text-align: center;
                    left:{{$left_box}};
                    right:{{$right_box}};
                    top:20px;
                    line-height: 50px;
                    background-color:white !important;
                    height:50px;
                    width:50px;
                    border-radius:100px !important;
                    color:black;
                }
                .title_main{
                    font-weight: 800;
                    text-align: center;
                    display: block;
                    color: #313131;
                    letter-spacing: 1px;
                    font-family:Georgia, 'Times New Roman', Times, serif;
                }
                body{
                    box-sizing: border-box;
                    /* background:linear-gradient(to top left ,rgb(235, 249, 255) 10% , rgb(255, 239, 223) 100% ,#cecece 90%); */
                    /* background-image:url("../../../uploads/IZO-D2.gif"); */
                    background-size: cover;
                    background-attachment: fixed;
                    background-repeat:  no-repeat; 
                    background-position: center;    
                    background-color:#fff !important;
                }
                .left_form_first_login{
                    border: 0px solid black;
                    border-radius: 00px 00px 00px 00px;
                    /* border-radius: 10px; */
                    padding: 10px;
                    box-shadow:1px 1px 100px #79797900;
                    background: rgb(255, 255, 255);

                }
                .right_form_first_login{
                    display: none !important;
                    border: 2px solid black;
                    border-radius: 00px;
                    padding: 10px; 
                    background: rgba(64, 64, 110, 0);
                    display: none;
                    background-image: url("../../../uploads/dubai.jpg");
                    background-position: right center;
                    background-repeat:no-repeat;
                    background-attachment:fixed;
                    background-size: cover;
                    overflow: hidden;
                }
                .right_form_first_login_top{
                    border: 2px solid black;
                    border-radius: 00px;
                    padding: 10px; 
                    background: rgba(64, 64, 110, 0);
                    display: none;
                }
                .mainbox{
                        border: 0px solid rgb(4, 0, 249);
                        border-radius: 00px;
                        padding: 0px 0px;
                        background: rgba(0, 0, 0, 0);
                        width: 100%;
                        display: block; 
                }
                .childbox{
                    border: 0px solid rgb(4, 0, 249);
                    width: 100%;
                    border-radius: 00px;
                    padding: 00px 00px;
                    height: 100%;
                    margin: 0px;
                    transform: translateY(0%);
                    background: rgba(93, 237, 9, 0); 
                    display: block; 
                    
                }
                .description{
                    text-align: center;
                    line-height:30px;
                    font-family:Georgia, 'Times New Roman', Times, serif;
                    font-size: 20px;
                    text-transform: capitalize;
                    font-weight: bolder;
                    margin:50px auto ;
                }
                .description_small{
                    background-image: url('../../../uploads/dubai.jpg');
                    background-size: cover; 
                    background-position:  right;
                    background-repeat: no-repeat; 
                    /* box-shadow: 1px 1px 10px black  ; */
                    text-align: center;
                    line-height:30px;
                    font-family:Georgia, 'Times New Roman', Times, serif;
                    font-size: 20px;
                    text-transform: capitalize;
                    font-weight: 300;
                    /* margin:50px auto ; */
                    height: 200px; ;
                }
                .title_izo{
                    margin:0px   ;
                    text-align: center;
                    line-height:20px;
                    padding-left: 00px;
                    font-family:Georgia, 'Times New Roman', Times, serif;
                    font-size:25px;
                    text-transform: capitalize;
                    font-weight: bolder;
                    width: auto !important;
                    /* border-bottom: 10px solid #ff954a; */
                }
                .izo-form-input-password:focus{
                    border-color: #ec6808 !important;
                    outline:1px solid #ec6808 !important;
                }
                .izo-form-input:focus{
                    border-color:#ec6808 !important;
                    outline: 1px solid #ec6808 !important;
                }
                .link_code{
                    font-size: 14px;
                }
                .code_activate_input{
                    font-size: 18px !important;
                    font-family: arial !important;
                    text-align: center;
                    max-width:200px !important;
                }
                .izo-form-input{
                    font-family:Georgia, 'Times New Roman', Times, serif;
                    width: 100%;
                    border-radius: 0px;
                    padding: 10px;
                    margin: 10px auto ;
                    font-size: 17px;
                    border-radius: 10px;
                    border: 1px solid #3a3a3a33;
                }
                .izo-form-input-password{
                    font-family:Georgia, 'Times New Roman', Times, serif;
                    width: 100% !important;
                    border-radius: 10px !important;
                    padding: 10px !important;
                    margin: 10px auto  !important;
                    font-size: 16px !important;
                    border: 1px solid #3a3a3a33 
                }
                .izo-group-form{  
                    display: flex;
                    justify-items: center; 
                }
                .izo-form-input-readOnly{
                    font-family:Georgia, 'Times New Roman', Times, serif;   
                    width: 60%;
                    border-radius: 0px;
                    padding: 10px;
                    margin: 10px auto ;
                    border: 1px solid #3a3a3a33;
                    background-color: #3e3e3e33;
                    color: #3a3a3a !important;
                    font-size: 15px;
                    text-align: center;
                    font-weight: bolder;
                }
                :-ms-input-placeholder{
                    color:#e86000;
                }
                .izo-form-input-mobile{
                    font-family:Georgia, 'Times New Roman', Times, serif;   
                    font-family: arial;
                    width: 100%;
                    border-radius: 0px;
                    padding: 10px;
                    margin: 10px auto ;
                    border: 0px solid #313131;
                    background-color: #fefefe;
                    color: rgb(70, 70, 70) !important;
                    font-size: 16px;
                    font-weight: bolder;
                }
                
                .izo-form-input-save{
                    font-family:Georgia, 'Times New Roman', Times, serif;
                    cursor: pointer;
                    text-align: center;   
                    width: 100%;
                    border-radius: 10px;
                    padding: 10px;
                    margin: 10px auto ;
                    border: 1px solid #ec6808;
                    background-color: #ec6808;
                    color: white !important;
                    font-size: 20px;
                    font-weight: bolder;
                }
            }
            @media (min-width: 600px) and  (max-width: 900px) {
                
                .form_active h3{
                    color:black;
                    font-family: Georgia, 'Times New Roman', Times, serif;
                }
                .form_active h1{
                    color:black;
                    font-family: Georgia, 'Times New Roman', Times, serif;
                }
                .form_active{
                    font-family: Georgia, 'Times New Roman', Times, serif;
                    position: absolute;
                    transform: translate(-50%,-50%);
                    left: 50%;
                    top: 50%;
                    max-width: 450px;
                    border-radius:10px;
                    background-color:white;
                    box-shadow:0px 0px 10px #3a3a3a33;
                    padding:30px;
                    padding-top:50px;
                    padding-bottom:80px;
                }
                .activation_code_content{
                    overflow: hidden;
                    position: fixed;
                    z-index: 4000;
                    width:100%;
                    height: 100%;
                    background-color: #f7f7f7;
                    color: #3a3a3a;
                    font-size: 20px;
                    font-family: Georgia, 'Times New Roman', Times, serif;
                    text-align: center;

                }
                .activation_code{
                    display: none;
                    overflow: hidden;
                    position: fixed;
                    z-index: 4000;
                    width:100%;
                    height: 100%;
                    background-color: #f7f7f700;
                    color: #3a3a3a;
                    font-size: 20px;
                    font-family: Georgia, 'Times New Roman', Times, serif;
                    text-align: center;

                }
                .list_of_lang{
                    background-color:#f7f7f7;
                    border-radius:10px;
                    padding:10px ;
                    position:absolute;
                    left:{{$left_box}};
                    right:{{$right_box}};
                    top:80px;
                    width:150px;
                    font-size:20px;
                    box-shadow: 0px 0px 10px #f7f7f7;
                }
                .language_box i{
                    color:#ec6808;
                }
                .language_box{
                    font-size: 18px;
                    cursor: pointer;
                    font-weight: 700;
                    position:absolute;
                    box-shadow: 0px 0px 10px #f7f7f7;
                    text-align: center;
                    left:{{$left_box}};
                    right:{{$right_box}};
                    top:20px;
                    line-height: 50px;
                    background-color:white !important;
                    height:50px;
                    width:50px;
                    border-radius:100px !important;
                    color:black;
                }
                .title_main{
                    font-weight: 800;
                    text-align: center;
                    display: block;
                    color: #313131;
                    letter-spacing: 1px;
                    font-size: 30px;
                    font-family:Georgia, 'Times New Roman', Times, serif;
                }
                body{
                    box-sizing: border-box;
                    /* background:linear-gradient(to top left ,rgb(235, 249, 255) 10% , rgb(255, 239, 223) 100% ,#cecece 90%); */
                    /* background-image:url("../../../uploads/IZO-D2.gif"); */
                    background-size: cover;
                    background-attachment: fixed;
                    background-repeat:  no-repeat; 
                    background-position: center; 
                    background-color:#fff !important;   
                }
                .left_form_first_login{
                    border: 0px solid black;
                    border-radius: 00px 00px 00px 00px;
                    /* border-radius: 10px; */
                    padding: 10px;
                    box-shadow:1px 1px 100px #79797900;
                    background: rgb(255, 255, 255);
                }
                .right_form_first_login{
                    display: none !important;
                    border: 2px solid black;
                    border-radius: 00px;
                    padding: 10px; 
                    background: rgba(64, 64, 110, 0);
                    display: none;
                    background-image: url("../../../uploads/dubai.jpg");
                    background-position: right center;
                    background-repeat:no-repeat;
                    background-attachment:fixed;
                    background-size: cover;
                    overflow: hidden;
                }
                .right_form_first_login_top{
                    border: 2px solid black;
                    border-radius: 00px;
                    padding: 10px; 
                    background: rgba(64, 64, 110, 0);
                    display: none;
                }
                .mainbox{
                    border: 0px solid rgb(66, 249, 0);
                    border-radius: 00px;
                    padding: 0%;
                    background: rgba(0, 0, 0, 0);
                    width: 100%; 
                    display: block; 
                }
                .childbox{
                    border: 0px solid rgb(0, 249, 0);
                    width: 100%;
                    border-radius: 00px;
                    padding: 0px; 
                    margin: 0px;
                    transform: translateY(0%);
                    background: rgba(93, 237, 9, 0); 
                    display: block; 
                }
                .description{
                    text-align: center;
                    line-height:30px;
                    font-family:Georgia, 'Times New Roman', Times, serif;
                    font-size: 25px;
                    text-transform: capitalize;
                    font-weight: bolder;
                    margin:50px auto ;
                }
                .description_small{
                    background-image: url('../../../uploads/dubai.jpg');
                    background-size: cover; 
                    background-position:  right;
                    background-repeat: no-repeat; 
                    /* box-shadow: 1px 1px 10px black  ; */
                    text-align: center;
                    line-height:30px;
                    font-family:Georgia, 'Times New Roman', Times, serif;
                    font-size: 25px;
                    text-transform: capitalize;
                    font-weight: 300;
                    /* margin:50px auto ; */
                    height: 200px; ;
                }
                .title_izo{
                    margin:0px   ;
                    text-align: center;
                    line-height:20px;
                    padding-left: 00px;
                    font-family:Georgia, 'Times New Roman', Times, serif;
                    font-size:25px;
                    text-transform: capitalize;
                    font-weight: bolder;
                    width: auto !important;
                    /* border-bottom: 10px solid #ff954a; */
                }
                .izo-form-input-password:focus{
                    border-color: #ec6808 !important;
                    outline:1px solid #ec6808 !important;
                }
                .izo-form-input:focus{
                    border-color:#ec6808 !important;
                    outline: 1px solid #ec6808 !important;
                }
                .link_code{
                    font-size: 14px;
                }
                .code_activate_input{
                    font-size: 18px !important;
                    font-family: arial !important;
                    text-align: center;
                    max-width:200px !important;
                }
                .izo-form-input{
                    font-family:Georgia, 'Times New Roman', Times, serif;
                    width: 100%;
                    border-radius: 0px;
                    padding: 10px;
                    margin: 10px auto ;
                    font-size: 17px;
                    border-radius: 10px;
                    border: 1px solid #3a3a3a33;
                }
                .izo-form-input-password{
                    font-family:Georgia, 'Times New Roman', Times, serif;
                    width: 100% !important;
                    border-radius: 10px !important;
                    padding: 10px !important;
                    margin: 10px auto  !important;
                    font-size: 16px !important;
                    border: 1px solid #3a3a3a33  
                }
                .izo-group-form{  
                    display: flex;
                    justify-items: center; 
                }
                .izo-form-input-readOnly{
                    font-family:Georgia, 'Times New Roman', Times, serif;   
                    width: 60%;
                    border-radius: 0px;
                    padding: 10px;
                    margin: 10px auto ;
                    border: 1px solid #3a3a3a33;
                    background-color: #3e3e3e33;
                    color: #3a3a3a !important;
                    font-size: 15px;
                    text-align: center;
                    font-weight: bolder;
                }
                :-ms-input-placeholder{
                    color:#e86000;
                }
                .izo-form-input-mobile{
                    font-family:Georgia, 'Times New Roman', Times, serif;   
                    font-family: arial;
                    width: 100%;
                    border-radius: 0px;
                    padding: 10px;
                    margin: 10px auto ;
                    border: 0px solid #313131;
                    background-color: #fefefe;
                    color: rgb(70, 70, 70) !important;
                    font-size: 16px;
                    font-weight: bolder;
                }
                
                .izo-form-input-save{
                    font-family:Georgia, 'Times New Roman', Times, serif;
                    cursor: pointer;
                    text-align: center;   
                    width: 100%;
                    border-radius: 10px;
                    padding: 10px;
                    margin: 10px auto ;
                    border: 1px solid #ec6808;
                    background-color: #ec6808;
                    color: white !important;
                    font-size: 20px;
                    font-weight: bolder;
                }
            }
            @media (min-width: 1024px) and (max-width:1400px){
                .form_active h3{
                    color:black;
                    font-family: Georgia, 'Times New Roman', Times, serif;
                }
                .form_active h1{
                    color:black;
                    font-family: Georgia, 'Times New Roman', Times, serif;
                }
                .form_active{
                    font-family: Georgia, 'Times New Roman', Times, serif;
                    position: absolute;
                    transform: translate(-50%,-50%);
                    left: 50%;
                    top: 50%;
                    max-width: 450px;
                    border-radius:10px;
                    background-color:white;
                    box-shadow:0px 0px 10px #3a3a3a33;
                    padding:30px;
                    padding-top:50px;
                    padding-bottom:80px;
                }
                .activation_code_content{
                    overflow: hidden;
                    position: fixed;
                    z-index: 4000;
                    width:100%;
                    height: 100%;
                    background-color: #f7f7f7;
                    color: #3a3a3a;
                    font-size: 20px;
                    font-family: Georgia, 'Times New Roman', Times, serif;
                    text-align: center;

                }
                .activation_code{
                    display: none;
                    overflow: hidden;
                    position: fixed;
                    z-index: 4000;
                    width:100%;
                    height: 100%;
                    background-color: #f7f7f700;
                    color: #3a3a3a;
                    font-size: 20px;
                    font-family: Georgia, 'Times New Roman', Times, serif;
                    text-align: center;

                }
                .list_of_lang{
                        background-color:#f7f7f7;
                        border-radius:10px;
                        padding:10px ;
                        position:absolute;
                        left:{{$left_box}};
                        right:{{$right_box}};
                        top:80px;
                        width:150px;
                        font-size:20px;
                        box-shadow: 0px 0px 10px #f7f7f7;
                    }
                .language_box i{
                        color:#ec6808;
                    }
                    .language_box{
                        font-size: 18px;
                        cursor: pointer;
                        font-weight: 700;
                        position:absolute;
                        box-shadow: 0px 0px 10px #f7f7f7;
                        text-align: center;
                        left:{{$left_box}};
                        right:{{$right_box}};
                        top:20px;
                        line-height: 50px;
                        background-color:white !important;
                        height:50px;
                        width:50px;
                        border-radius:100px !important;
                        color:black;
                    }
                .title_main{
                    font-weight: 800;
                    text-align: center;
                    display: none;
                    color: #313131;
                    letter-spacing: 1px;
                    font-size: 30px;
                    font-family:Georgia, 'Times New Roman', Times, serif;
                }
                body{
                    box-sizing: border-box;
                    /* background:linear-gradient(to top left ,rgb(255, 239, 223) 90% , rgb(255, 239, 223) 100% ,#cecece 60%); */
                background-image:url("../../../uploads/IZO-D2.gif");
                background-size: cover;
                background-attachment: fixed;
                background-repeat:  no-repeat; 
                background-position: center;    
                }
                .left_form_first_login{
                    border: 0px solid black;
                    border-radius: 10px 10px 10px 10px;
                    /* border-radius: 10px 0px 0px 10px; */
                    padding: 10px;
                    box-shadow:1px 1px 100px #797979;
                    background: rgb(255, 255, 255);
                }
                .right_form_first_login{
                    display: none !important;
                    border: 0px solid black;
                    border-radius: 0px 10px 10px 0px;
                    padding: 10px; 
                    background: rgba(64, 64, 110, 0);
                    display: block;
                    background-image: url("../../../uploads/dubai.jpg");
                    background-position: right center;
                    background-repeat:no-repeat;
                    background-attachment:fixed;
                    background-size: cover;
                    overflow: hidden;
                }
                .right_form_first_login_top{
                    border: 0px solid black;
                    border-radius: 10px;
                    padding: 10px; 
                    background: rgba(64, 64, 110, 0);
                    display: none;
                }
                .mainbox{
                    border: 0px solid rgb(249, 0, 249);
                    border-radius: 10px;
                    padding: 30px;
                    background: rgba(0, 0, 0, 0);
                    width: 100%;
                    height: auto;
                    display: flex;
                    justify-content: center;
                }
                .childbox{
                    border: 0px solid rgb(249, 0, 232);
                    width: 100%;
                    border-radius: 10px;
                    padding: 30px 30px;
                    height: auto;
                    transform: translateY(0%);
                    background: rgba(93, 237, 9, 0); 
                    display: flex;
                    justify-content: center;
                }
                .description{
                    text-align: left;
                    line-height:20px;
                    font-family:Georgia, 'Times New Roman', Times, serif;
                    font-size: 20px;
                    padding-left: 20px;
                    text-transform: capitalize;
                    font-weight: bolder; 
                    margin:20px 15% ;
                }
                .description_small{
                    background-image: url('../../../uploads/dubai.jpg');
                    background-size: cover; 
                    background-position:  right;
                    background-repeat: no-repeat; 
                    /* box-shadow: 1px 1px 10px black  ; */
                    text-align: center;
                    line-height:20px;
                    font-family:Georgia, 'Times New Roman', Times, serif;
                    font-size: 20px;
                    text-transform: capitalize;
                    font-weight: 300;
                    /* margin:50px auto ; */
                    height: 150px; ;
                }
                .title_izo{
                    margin:30px 15% ;
                    text-align: left;
                    line-height:50px;
                    padding-left: 20px;
                    font-family:Georgia, 'Times New Roman', Times, serif;
                    font-size: 60px;
                    text-transform: capitalize;
                    font-weight: bolder;
                    width: auto !important;
                    /* border-bottom: 10px solid #ff954a; */
                }
                .izo-form-input-password:focus{
                    border-color: #ec6808 !important;
                    outline:1px solid #ec6808 !important;
                }
                .izo-form-input:focus{
                    border-color:#ec6808 !important;
                    outline: 1px solid #ec6808 !important;
                }
                .link_code{
                    font-size: 14px;
                }
                .code_activate_input{
                    font-size: 18px !important;
                    font-family: arial !important;
                    text-align: center;
                    max-width:200px !important;
                }
                .izo-form-input{
                    font-family:Georgia, 'Times New Roman', Times, serif;
                    width: 100%;
                    border-radius: 0px;
                    padding: 10px;
                    margin: 10px auto ;
                    font-size: 17px;
                    border-radius: 10px;
                    border: 1px solid #3a3a3a33;
                }
                .izo-form-input-password{
                    font-family:Georgia, 'Times New Roman', Times, serif;
                    width: 100% !important;
                    border-radius: 10px !important;
                    padding: 10px !important;
                    margin: 10px auto  !important;
                    font-size: 16px !important;
                    border: 1px solid #3a3a3a33  
                }
                .izo-group-form{  
                    display: flex;
                    justify-items: center; 
                }
                .izo-form-input-readOnly{
                    font-family:Georgia, 'Times New Roman', Times, serif;   
                    width: 60%;
                    border-radius: 0px;
                    padding: 10px;
                    margin: 10px auto ;
                    border: 1px solid #3a3a3a33;
                    background-color: #3e3e3e33;
                    color: #3a3a3a !important;
                    font-size: 15px;
                    text-align: center;
                    font-weight: bolder;
                }
                :-ms-input-placeholder{
                    color:#e86000;
                }
                .izo-form-input-mobile{
                    font-family:Georgia, 'Times New Roman', Times, serif;   
                    font-family: arial;
                    width: 100%;
                    border-radius: 0px;
                    padding: 10px;
                    margin: 10px auto ;
                    border: 0px solid #313131;
                    background-color: #fefefe;
                    color: rgb(70, 70, 70) !important;
                    font-size: 16px;
                    font-weight: bolder;
                }
                
                .izo-form-input-save{
                    font-family:Georgia, 'Times New Roman', Times, serif;
                    cursor: pointer;
                    text-align: center;   
                    width: 100%;
                    border-radius: 10px;
                    padding: 10px;
                    margin: 10px auto ;
                    border: 1px solid #ec6808;
                    background-color: #ec6808;
                    color: white !important;
                    font-size: 20px;
                    font-weight: bolder;
                }
            }
            @media (min-width: 900px) and (max-width: 1024px){

                .form_active h3{
                    color:black;
                    font-family: Georgia, 'Times New Roman', Times, serif;
                }
                .form_active h1{
                    color:black;
                    font-family: Georgia, 'Times New Roman', Times, serif;
                }
                .form_active{
                    font-family: Georgia, 'Times New Roman', Times, serif;
                    position: absolute;
                    transform: translate(-50%,-50%);
                    left: 50%;
                    top: 50%;
                    max-width: 450px;
                    border-radius:10px;
                    background-color:white;
                    box-shadow:0px 0px 10px #3a3a3a33;
                    padding:30px;
                    padding-top:50px;
                    padding-bottom:80px;
                }
                .activation_code_content{
                    overflow: hidden;
                    position: fixed;
                    z-index: 4000;
                    width:100%;
                    height: 100%;
                    background-color: #f7f7f7;
                    color: #3a3a3a;
                    font-size: 20px;
                    font-family: Georgia, 'Times New Roman', Times, serif;
                    text-align: center;

                }
                .activation_code{
                    display: none;
                    overflow: hidden;
                    position: fixed;
                    z-index: 4000;
                    width:100%;
                    height: 100%;
                    background-color: #f7f7f700;
                    color: #3a3a3a;
                    font-size: 20px;
                    font-family: Georgia, 'Times New Roman', Times, serif;
                    text-align: center;

                }
                .list_of_lang{
                    background-color:#f7f7f7;
                    border-radius:10px;
                    padding:10px ;
                    position:absolute;
                    left:{{$left_box}};
                    right:{{$right_box}};
                    top:80px;
                    width:150px;
                    font-size:20px;
                    box-shadow: 0px 0px 10px #f7f7f7;
                }
                .language_box i{
                    color:#ec6808;
                }
                .language_box{
                    font-size: 18px;
                    cursor: pointer;
                    font-weight: 700;
                    position:absolute;
                    box-shadow: 0px 0px 10px #f7f7f7;
                    text-align: center;
                    left:{{$left_box}};
                    right:{{$right_box}};
                    top:20px;
                    line-height: 50px;
                    background-color:white !important;
                    height:50px;
                    width:50px;
                    border-radius:100px !important;
                    color:black;
                }
                .title_main{
                    font-weight: 800;
                    text-align: center;
                    display: none;
                    color: #313131;
                    letter-spacing: 1px;
                    font-size: 30px;
                    font-family:Georgia, 'Times New Roman', Times, serif;
                }
                body{
                    box-sizing: border-box;
                    /* background:linear-gradient(to top left ,rgb(235, 249, 255) 10% , rgb(255, 239, 223) 100% ,#cecece 90%); */
                    /* background-image:url("../../../uploads/IZO-D2.gif"); */
                    background-size: cover;
                    background-attachment: fixed;
                    background-repeat:  no-repeat; 
                    background-position: center;
                    background-color:#fff !important;    
                }
                .left_form_first_login{
                    border: 0px solid black;
                    border-radius: 00px 00px 00px 00px;
                    /* border-radius: 10px; */
                    padding: 10px;
                    box-shadow:1px 1px 100px #79797900;
                    background: rgb(255, 255, 255);
                }
                .right_form_first_login{
                    display: none !important;
                    border: 0px solid black;
                    border-radius: 00px;
                    padding: 10px; 
                    background: rgba(64, 64, 110, 0);
                    display: none;
                    background-image: url("../../../uploads/dubai.jpg");
                    background-position: right center;
                    background-repeat:no-repeat;
                    background-attachment:fixed;
                    background-size: cover;
                    overflow: hidden;
                }
                .right_form_first_login_top{
                    border: 0px solid black;
                    border-radius: 00px;
                    padding: 10px; 
                    font-family:Georgia, 'Times New Roman', Times, serif;
                    background: rgba(64, 64, 110, 0);
                    display: block;
                }
                .mainbox{
                    border: 0px solid rgb(249, 0, 0);
                    border-radius: 00px;
                    padding: 00px ;
                    background: rgba(0, 0, 0, 0);
                    width: 100%;
                    height: auto;
                    display:inline-block;
                    justify-content: none;
                }
                .childbox{
                    border: 0px solid rgb(249, 0, 0);
                    width: 100%;
                    margin: 0px;
                    border-radius: 00px;
                    padding: 00px 00px;
                    height: auto;
                    transform: translateY(0%);
                    background: rgba(93, 237, 9, 0); 
                    display: block;
                    justify-content: none;
                }
                .description{
                    text-align: left;
                    line-height:30px;
                    font-family:Georgia, 'Times New Roman', Times, serif;
                    font-size: 30px;
                    padding-left: 00px;
                    text-transform: capitalize;
                    font-weight: bolder;
                    margin:30px auto ;
                }
                .description_small{
                    background-image: url('../../../uploads/dubai.jpg');
                    background-size: cover; 
                    background-position:  right;
                    background-repeat: no-repeat; 
                    /* box-shadow: 1px 1px 10px black  ; */
                    text-align: center;
                    line-height:30px;
                    font-family:Georgia, 'Times New Roman', Times, serif;
                    font-size: 25px;
                    text-transform: capitalize;
                    font-weight: 300;
                    /* margin:50px auto ; */
                    height: 200px; ;
                }
                .title_izo{
                    margin:0px   ;
                    text-align: left;
                    color: #313131;
                    line-height:20px;
                    padding-left: 00px;
                    font-family:Georgia, 'Times New Roman', Times, serif;
                    font-size:25px;
                    text-transform: capitalize;
                    font-weight: bolder;
                    width: auto !important;
                    /* border-bottom: 10px solid #ff954a; */
                }
                .izo-form-input-password:focus{
                    border-color: #ec6808 !important;
                    outline:1px solid #ec6808 !important;
                }
                .izo-form-input:focus{
                    border-color:#ec6808 !important;
                    outline: 1px solid #ec6808 !important;
                }
                .link_code{
                    font-size: 14px;
                }
                .code_activate_input{
                    font-size: 18px !important;
                    font-family: arial !important;
                    text-align: center;
                    max-width:200px !important;
                }
                .izo-form-input{
                    font-family:Georgia, 'Times New Roman', Times, serif;
                    width: 100%;
                    border-radius: 0px;
                    padding: 10px;
                    margin: 10px auto ;
                    font-size: 17px;
                    border-radius: 10px;
                    border: 1px solid #3a3a3a33;
                }
                .izo-form-input-password{
                    font-family:Georgia, 'Times New Roman', Times, serif;
                    width: 100% !important;
                    border-radius: 10px !important;
                    padding: 10px !important;
                    margin: 10px auto  !important;
                    font-size: 16px !important;
                    border: 1px solid #3a3a3a33  
                }
                .izo-group-form{  
                    display: flex;
                    justify-items: center; 
                }
                .izo-form-input-readOnly{
                    font-family:Georgia, 'Times New Roman', Times, serif;   
                    width: 60%;
                    border-radius: 0px;
                    padding: 10px;
                    margin: 10px auto ;
                    border: 1px solid #3a3a3a33;
                    background-color: #3e3e3e33;
                    color: #3a3a3a !important;
                    font-size: 15px;
                    text-align: center;
                    font-weight: bolder;
                }
                :-ms-input-placeholder{
                    color:#e86000;
                }
                .izo-form-input-mobile{
                    font-family:Georgia, 'Times New Roman', Times, serif;   
                    font-family: arial;
                    width: 100%;
                    border-radius: 0px;
                    padding: 10px;
                    margin: 10px auto ;
                    border: 0px solid #313131;
                    background-color: #fefefe;
                    color: rgb(70, 70, 70) !important;
                    font-size: 16px;
                    font-weight: bolder;
                }
                
                .izo-form-input-save{
                    font-family:Georgia, 'Times New Roman', Times, serif;
                    cursor: pointer;
                    text-align: center;   
                    width: 100%;
                    border-radius: 10px;
                    padding: 10px;
                    margin: 10px auto ;
                    border: 1px solid #ec6808;
                    background-color: #ec6808;
                    color: white !important;
                    font-size: 20px;
                    font-weight: bolder;
                }
            }
            @media (min-width:1400px){
                body{
                    box-sizing: border-box;
                    background-image:url("../../../uploads/IZO-D2.gif");
                    background-size: cover;
                    background-attachment: fixed;
                    background-repeat:  no-repeat; 
                    background-position: center;
                    
                    /* background-color:#fff !important; */
                    /* background:linear-gradient(to top left ,rgb(235, 249, 255) 10% , rgb(255, 239, 223) 40% ,#cecece 100%); */
                }
            }
        </style>
    @endsection
    <body>
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
            } else if(count($hostParts) == 3){
                // Remove the last two parts (domain and TLD)
                array_pop($hostParts); // TLD
 
                // The remaining parts are the subdomain
                $subdomain = implode('.', $hostParts);
            } else {
                // No subdomain
                $subdomain = '';

            }
            $subdomain = $subdomain; 
            
            $list_of_code = [
                "+1"       => '<span class="flag-icon flag-icon-us"></span><span>United States (+1)</span>',
                "+44"      => '<span class="flag-icon flag-icon-gb"></span><span>United Kingdom (+44)</span>',
                "+49"      => '<span class="flag-icon flag-icon-de"></span><span>Germany (+49)</span>',
                "+33"      => '<span class="flag-icon flag-icon-fr"></span><span>France (+33)</span>',
                "+971"     => '<span class="flag-icon flag-icon-ae"></span><span>United Arab Emirates (+971)</span>',
                "+61"      => '<span class="flag-icon flag-icon-au"></span><span>Australia (+61)</span>',
                "+81"      => '<span class="flag-icon flag-icon-jp"></span><span>Japan (+81)</span>',
                "+93"      => '<span class="flag-icon flag-icon-af"></span><span>Afghanistan (+93)</span>',
                "+355"     => '<span class="flag-icon flag-icon-al"></span><span>Albania (+355)</span>',
                "+213"     => '<span class="flag-icon flag-icon-dz"></span><span>Algeria (+213)</span>',
                "+376"     => '<span class="flag-icon flag-icon-ad"></span><span>Andorra (+376)</span>',
                "+244"     => '<span class="flag-icon flag-icon-ao"></span><span>Angola (+244)</span>',
                "+54"      => '<span class="flag-icon flag-icon-ar"></span><span>Argentina (+54)</span>',
                "+374"     => '<span class="flag-icon flag-icon-am"></span><span>Armenia (+374)</span>',
                "+61"      => '<span class="flag-icon flag-icon-au"></span><span>Australia (+61)</span>',
                "+43"      => '<span class="flag-icon flag-icon-at"></span><span>Austria (+43)</span>',
                "+994"     => '<span class="flag-icon flag-icon-az"></span><span>Azerbaijan (+994)</span>',
                "+1-242"   => '<span class="flag-icon flag-icon-bs"></span><span>Bahamas (+1-242)</span>',
                "+973"     => '<span class="flag-icon flag-icon-bh"></span><span>Bahrain (+973)</span>',
                "+880"     => '<span class="flag-icon flag-icon-bd"></span><span>Bangladesh (+880)</span>',
                "+1-246"   => '<span class="flag-icon flag-icon-bb"></span><span>Barbados (+1-246)</span>',
                "+375"     => '<span class="flag-icon flag-icon-by"></span><span>Belarus (+375)</span>',
                "+32"      => '<span class="flag-icon flag-icon-be"></span><span>Belgium (+32)</span>',
                "+501"     => '<span class="flag-icon flag-icon-bz"></span><span>Belize (+501)</span>',
                "+229"     => '<span class="flag-icon flag-icon-bj"></span><span>Benin (+229)</span>',
                "+975"     => '<span class="flag-icon flag-icon-bt"></span><span>Bhutan (+975)</span>',
                "+591"     => '<span class="flag-icon flag-icon-bo"></span><span>Bolivia (+591)</span>',
                "+387"     => '<span class="flag-icon flag-icon-ba"></span><span>Bosnia and Herzegovina (+387)</span>',
                "+267"     => '<span class="flag-icon flag-icon-bw"></span><span>Botswana (+267)</span>',
                "+55"      => '<span class="flag-icon flag-icon-br"></span><span>Brazil (+55)</span>',
                "+673"     => '<span class="flag-icon flag-icon-bn"></span><span>Brunei (+673)</span>' ,
                "+359"     => '<span class="flag-icon flag-icon-bg"></span><span>Bulgaria (+359)</span>',
                "+226"     => '<span class="flag-icon flag-icon-bf"></span><span>Burkina Faso (+226)</span>',
                "+257"     => '<span class="flag-icon flag-icon-bi"></span><span>Burundi (+257)</span>' 
            ];
            
        @endphp
        <div class="loading">
            <div class="loading-content">
                <h1 class="text-center">
                    <img  style="width:200px !important" class="logo-style"  height=75 src="{{asset('logo-white.png')}}" alt="logo">
                    <br>
                    <small>{!!__('izo.waiting')!!}</small>
                </h1>
            </div>
        </div>
        <form hidden action="https://izocloud.com/register-account" id="go-home" method="GET">
            <button id="go_home"  type="submit">Go Home</button>
        </form>
        <div class="activation_code"></div>
        <input type="text" id="otp" hidden value="0">
        <div class="language_box">
            <i class="fa fas fa-globe"  ></i>
        </div>
        <div class="list_of_lang hide">
            {{-- <small style="color: #0c0c0c">{{__("izo.change_lang")}}</small><br> --}}
            <p style="line-height: 2px;">&nbsp;</p>
            <a  href="{{action("HomeController@changeLanguageApp",["lang"=>"en"])}}" >  &nbsp;&nbsp; {{__('izo.english')}} </a>
            <p style="line-height: 2px;">&nbsp;</p>
            <a  href="{{action("HomeController@changeLanguageApp",["lang"=>"ar"])}}" > &nbsp;&nbsp; {{__('izo.arabic')}} </a>
        </div>
        <div class="container mainbox">
            <div class="row childbox">
                {{-- <div class=""> --}}
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 col-12 left_form_first_login">
                        <div class="col-xl-12 col-md-12 text-center" style="text-transform: uppercase;background-color:#fff;font-weight:bolder;">
                            <img class="logo-style"  src="{{asset('logo.png')}}" alt="logo">                            
                        </div> 
                        <br>
                        <div class="col-xl-12 col-md-12 text-left" style=" text-transform: capitalize;font-weight:bolder;font-family:Georgia, 'Times New Roman', Times, serif;">
                            <h3  style="font-weight:600 ;color:#000;font-family:Georgia, 'Times New Roman', Times, serif;">{{__("izo.register_now")}}</h3>
                            <small style="font-weight:600 ;color:#666666fd;font-family:Georgia, 'Times New Roman', Times, serif;">
                        
                            </small>
                            <br>
                        </div> 
                        <br>
                        {!! Form::open(['url' => route('izoSaveAccount'), 'method' => 'post', 'id' => 'first_register_form','files' => true ]) !!}
                        
                        
                        <input type="hidden" id="domain_name_array" value="{{json_encode($list_domains)}}">
                        <input type="hidden" id="domain_name_current" value="{{parse_url(request()->root(),PHP_URL_HOST)}}">
                            <div class="col-xl-12 col-md-12 field_icon">
                                <b style="font-size:17px;font-family:Georgia, 'Times New Roman', Times, serif;color: #ec6808 !important">{{__('izo.company_name')}}</b>
                                {!! Form::text('company_name',null,['class' => 'izo-form-input', 'min' => 3 ,'id'=>'company_name', 'placeholder' => __('izo.company_name_placeholder') ]) !!}
                                <i class="fas fa-spinner fa-spin spinner hide_icon"></i>
                                <i class="fas fa-check success-icon hide_icon"></i>
                                <i class="fa fa-times-circle hide_icon"></i>
                                 <span class="error" id="nameError"></span>
                            </div>  

                            <div class="col-xl-12 col-md-12 field_icon">
                                <b style="font-size:17px;font-family:Georgia, 'Times New Roman', Times, serif;color: #ec6808 !important">{{__('izo.email_address')}}</b>
                                {!! Form::text('email',null,['class' => 'izo-form-input', 'id'=>'email' , 'placeholder' => __('izo.email_placeholder') ]) !!}
                                <span class="error" id="emailError"></span>
                                <i class="fas fa-spinner fa-spin spinner hide_icon"></i>
                                <i class="fas fa-check success-icon hide_icon"></i>
                                <i class="fa fa-times-circle hide_icon"></i>
                                @if ($errors->has('email'))
                                    <span class="text-danger">
                                        {{ $errors->first('email') }}
                                    </span>
                                    @endif
                                </div>
                                <div class="col-xl-12 col-md-12 ">
                                    <b style="font-size:17px;font-family:Georgia, 'Times New Roman', Times, serif;color: #ec6808 !important">{{__('izo.mobile')}}</b>
                                </div>
                                <div class="col-xl-12 col-md-12 field_icon_mobile">
                                    <i class="fas fa-spinner fa-spin spinner hide_icon"></i>
                                    <i class="fas fa-check success-icon hide_icon"></i>
                                    <i class="fa fa-times-circle hide_icon"></i>
                                    <div class="izo-group-form">
                                        {{-- <select name="mobile_code" id="mobile_code" class="izo-form-input-mobile" style="font-size:13px;position:absolute;right:{{$right_mobile}} !important;left:{{$left_mobile}} !important;top:5px;width:20%">
                                             
                                                <option value="+1"    class="country-option"> <span class="flag-icon flag-icon-us"></span><span>United States (+1)</span></option>
                                                <option value="+44"   class="country-option"> <span class="flag-icon flag-icon-gb"></span><span>United Kingdom (+44)</span></option>
                                                <option value="+49"   class="country-option"> <span class="flag-icon flag-icon-de"></span><span>Germany (+49)</span></option>
                                                <option value="+33"   class="country-option"> <span class="flag-icon flag-icon-fr"></span><span>France (+33)</span></option>
                                                <option value="+971"  class="country-option"> <span class="flag-icon flag-icon-ae"></span><span>United Arab Emirates (+971)</span></option>
                                                <option value="+61"   class="country-option"> <span class="flag-icon flag-icon-au"></span><span>Australia (+61)</span></option>
                                                <option value="+81"   class="country-option"> <span class="flag-icon flag-icon-jp"></span><span>Japan (+81)</span></option>
                                                <option value="+93"   class="country-option"> <span class="flag-icon flag-icon-af"></span><span>Afghanistan (+93)</span></option>
                                                <option value="+355"  class="country-option"> <span class="flag-icon flag-icon-al"></span><span>Albania (+355)</span></option>
                                                <option value="+213"  class="country-option"> <span class="flag-icon flag-icon-dz"></span><span>Algeria (+213)</span></option>
                                                <option value="+376"  class="country-option"> <span class="flag-icon flag-icon-ad"></span><span>Andorra (+376)</span></option>
                                                <option value="+244"  class="country-option"> <span class="flag-icon flag-icon-ao"></span><span>Angola (+244)</span></option>
                                                <option value="+54"   class="country-option"> <span class="flag-icon flag-icon-ar"></span><span>Argentina (+54)</span></option>
                                                <option value="+374"  class="country-option"> <span class="flag-icon flag-icon-am"></span><span>Armenia (+374)</span></option>
                                                <option value="+61"   class="country-option"> <span class="flag-icon flag-icon-au"></span><span>Australia (+61)</span></option>
                                                <option value="+43"   class="country-option"> <span class="flag-icon flag-icon-at"></span><span>Austria (+43)</span></option>
                                                <option value="+994"  class="country-option"> <span class="flag-icon flag-icon-az"></span><span>Azerbaijan (+994)</span></option>
                                                <option value="+1-242" class="country-option"><span class="flag-icon flag-icon-bs"></span><span>Bahamas (+1-242)</span></option>
                                                <option value="+973"  class="country-option"> <span class="flag-icon flag-icon-bh"></span><span>Bahrain (+973)</span></option>
                                                <option value="+880"  class="country-option"> <span class="flag-icon flag-icon-bd"></span><span>Bangladesh (+880)</span></option>
                                                <option value="+1-246" class="country-option"><span class="flag-icon flag-icon-bb"></span><span>Barbados (+1-246)</span></option>
                                                <option value="+375"  class="country-option"> <span class="flag-icon flag-icon-by"></span><span>Belarus (+375)</span></option>
                                                <option value="+32"   class="country-option"> <span class="flag-icon flag-icon-be"></span><span>Belgium (+32)</span></option>
                                                <option value="+501"  class="country-option"> <span class="flag-icon flag-icon-bz"></span><span>Belize (+501)</span></option>
                                                <option value="+229"  class="country-option"> <span class="flag-icon flag-icon-bj"></span><span>Benin (+229)</span></option>
                                                <option value="+975"  class="country-option"> <span class="flag-icon flag-icon-bt"></span><span>Bhutan (+975)</span></option>
                                                <option value="+591"  class="country-option"> <span class="flag-icon flag-icon-bo"></span><span>Bolivia (+591)</span></option>
                                                <option value="+387"  class="country-option"> <span class="flag-icon flag-icon-ba"></span><span>Bosnia and Herzegovina (+387)</span></option>
                                                <option value="+267"  class="country-option"> <span class="flag-icon flag-icon-bw"></span><span>Botswana (+267)</span></option>
                                                <option value="+55"   class="country-option"> <span class="flag-icon flag-icon-br"></span><span>Brazil (+55)</span></option>
                                                <option value="+673"  class="country-option"> <span class="flag-icon flag-icon-bn"></span><span>Brunei (+673)</span></option>,
                                                <option value="+359"  class="country-option"> <span class="flag-icon flag-icon-bg"></span><span>Bulgaria (+359)</span></option>
                                                <option value="+226"  class="country-option"> <span class="flag-icon flag-icon-bf"></span><span>Burkina Faso (+226)</span></option>
                                                <option value="+257"  class="country-option"> <span class="flag-icon flag-icon-bi"></span><span>Burundi (+257)</span></option>
                                        </select> --}}
                                        <input type="text" hidden id="mobile_code" name="mobile_code" value="+971">
                                        <div class="custom-select">
                                            <div class="select-selected">
                                                <span class="flag-icon flag-icon-ae"></span><span><span class="text_flag">United Arab Emirates</span> (+971)</span>
                                            </div>
                                            <div class="select-items">
                                                <div data-value="+971">
                                                    <span class="flag-icon flag-icon-ae"></span><span><span class="text_flag">United Arab Emirates</span> (+971)</span>
                                                </div>
                                                <div data-value="+1">
                                                    <span class="flag-icon flag-icon-us"></span><span><span class="text_flag">United States</span> (+1)</span>
                                                </div>
                                                <div data-value="+44">
                                                    <span class="flag-icon flag-icon-gb"></span><span><span class="text_flag">United Kingdom</span> (+44)</span>
                                                </div>
                                                <div data-value="+49">
                                                    <span class="flag-icon flag-icon-de"></span><span><span class="text_flag">Germany</span> (+49)</span>
                                                </div>
                                                <div data-value="+33">
                                                    <span class="flag-icon flag-icon-fr"></span><span><span class="text_flag">France</span> (+33)</span>
                                                </div>
                                                <div data-value="+61">
                                                    <span class="flag-icon flag-icon-au"></span><span><span class="text_flag">Australia</span> (+61)</span>
                                                </div>
                                                <div data-value="+81">
                                                    <span class="flag-icon flag-icon-jp"></span><span><span class="text_flag">Japan</span> (+81)</span>
                                                </div>
                                                <div data-value="+93">
                                                    <span class="flag-icon flag-icon-af"></span><span><span class="text_flag">Afghanistan</span> (+93)</span> 
                                                </div>
                                                <div data-value="+355">
                                                    <span class="flag-icon flag-icon-al"></span><span><span class="text_flag">Albania</span> (+355)</span>  
                                                </div>
                                                <div data-value="+213">
                                                    <span class="flag-icon flag-icon-dz"></span><span><span class="text_flag">Algeria</span> (+213)</span>  
                                                </div>
                                                <div data-value="+376">
                                                    <span class="flag-icon flag-icon-ad"></span><span><span class="text_flag">Andorra</span> (+376)</span>   
                                                </div>
                                                <div data-value="+244">
                                                    <span class="flag-icon flag-icon-ao"></span><span><span class="text_flag">Angola</span> (+244)</span>  
                                                </div>
                                                <div data-value="+54">
                                                    <span class="flag-icon flag-icon-ar"></span><span><span class="text_flag">Argentina</span> (+54)</span>  
                                                </div>
                                                <div data-value="+374">
                                                    <span class="flag-icon flag-icon-am"></span><span><span class="text_flag">Armenia</span> (+374)</span>  
                                                </div>
                                                <div data-value="+61">
                                                    <span class="flag-icon flag-icon-au"></span><span><span class="text_flag">Australia</span> (+61)</span>  
                                                </div>
                                                <div data-value="+43">
                                                    <span class="flag-icon flag-icon-at"></span><span><span class="text_flag">Austria</span> (+43)</span>    
                                                </div>
                                                <div data-value="+994">
                                                    <span class="flag-icon flag-icon-az"></span><span><span class="text_flag">Azerbaijan</span> (+994)</span>  
                                                </div>
                                                <div data-value="+1-242">
                                                    <span class="flag-icon flag-icon-bs"></span><span><span class="text_flag">Bahamas</span> (+1-242)</span>  
                                                </div>
                                                <div data-value="+973">
                                                    <span class="flag-icon flag-icon-bh"></span><span><span class="text_flag">Bahrain</span> (+973)</span>  
                                                </div>
                                                <div data-value="+880">
                                                    <span class="flag-icon flag-icon-bd"></span><span><span class="text_flag">Bangladesh</span> (+880)</span>  
                                                </div>
                                                <div data-value="+1-246">
                                                    <span class="flag-icon flag-icon-bb"></span><span><span class="text_flag">Barbados</span> (+1-246)</span>  
                                                </div>
                                                <div data-value="+375">
                                                    <span class="flag-icon flag-icon-by"></span><span><span class="text_flag">Belarus</span> (+375)</span>  
                                                </div>
                                                <div data-value="+32">
                                                    <span class="flag-icon flag-icon-be"></span><span><span class="text_flag">Belgium</span> (+32)</span>  
                                                </div>
                                                <div data-value="+501">
                                                    <span class="flag-icon flag-icon-bz"></span><span><span class="text_flag">Belize</span> (+501)</span> 
                                                </div>
                                                <div data-value="+229">
                                                    <span class="flag-icon flag-icon-bj"></span><span><span class="text_flag">Benin</span> (+229)</span>  
                                                </div>
                                                <div data-value="+975">
                                                    <span class="flag-icon flag-icon-bt"></span><span><span class="text_flag">Bhutan</span> (+975)</span>  
                                                </div>
                                                <div data-value="+591">
                                                    <span class="flag-icon flag-icon-bo"></span><span><span class="text_flag">Bolivia</span> (+591)</span>  
                                                </div>
                                                <div data-value="+387">
                                                    <span class="flag-icon flag-icon-ba"></span><span><span class="text_flag">Bosnia and Herzegovina</span> (+387)</span> 
                                                </div>
                                                <div data-value="+267"> 
                                                    <span class="flag-icon flag-icon-bw"></span><span><span class="text_flag">Botswana</span> (+267)</span>  
                                                </div>
                                                <div data-value="+55"> 
                                                    <span class="flag-icon flag-icon-br"></span><span><span class="text_flag">Brazil</span> (+55)</span> 
                                                </div>
                                                <div data-value="+673"> 
                                                    <span class="flag-icon flag-icon-bn"></span><span><span class="text_flag">Brunei</span> (+673)</span>  
                                                </div>
                                                <div data-value="+359"> 
                                                    <span class="flag-icon flag-icon-bg"></span><span><span class="text_flag">Bulgaria</span> (+359)</span> 
                                                </div>
                                                <div data-value="+226"> 
                                                    <span class="flag-icon flag-icon-bf"></span><span><span class="text_flag">Burkina Faso</span> (+226)</span> 
                                                </div>
                                                <div data-value="+257"> 
                                                    <span class="flag-icon flag-icon-bi"></span><span><span class="text_flag">Burundi</span> (+257)</span> 
                                                </div>
                                                <!-- Add more countries as needed -->
                                            </div>
                                        </div>
                                        {{-- {!! Form::select('mobile_code',$list_of_code,null,['class' => 'izo-form-input-mobile',  'id'=>'mobile_code' , 'style' => 'font-size:13px;position:absolute;right:'.$right_mobile.' !important;left:'.$left_mobile.' !important;top:5px;width:20%'  ]) !!} --}}
                                        {!! Form::number('mobile',null,['class' => 'izo-form-input',  'id'=>'mobile' ,'data-max' => "9" , 'min'=>0,'max'=>9999999999 ,'style' => 'font-family:arial !important;padding-left:'.$padding_left.';padding-right:'.$padding_right.';width:100%' , 'placeholder' => __('00 0000 000')  ]) !!}
                                    </div>
                                    <span class="error" id="mobileError"></span>
                                    @if ($errors->has('mobile'))
                                        <span class="text-danger">
                                            {{ $errors->first('mobile') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="col-xl-12 col-md-12">
                                    <b  style="font-size:17px;font-family:Georgia, 'Times New Roman', Times, serif;color: #ec6808 !important" >{{__('izo.domain_name')}}</b>
                                </div>
                            <div class="col-xl-12 col-md-12 field_icon_domain" dir="ltr">
                                <i class="fas fa-spinner fa-spin spinner hide_icon"></i>
                                <i class="fas fa-check success-icon hide_icon"></i>
                                <i class="fa fa-times-circle hide_icon"></i>
                                <div class="izo-group-form">
                                    <div class="izo-group-form">
                                        {!! Form::text('domain_title',"https://",['class' => 'izo-form-input-readOnly', 'style' => 'width:40% ;font-size:15px;border-top-left-radius:10px;border-bottom-left-radius:10px;' ,'readOnly' ]) !!}
                                        {!! Form::text('domain_name',null,['class' => 'izo-form-input', 'min' => 3 , 'id'=>'domain_name' , 'style' => 'border-radius: 0px !important;width:80%', 'placeholder' => __('your domain name') ]) !!}
                                    </div>
                                    {!! Form::text('domain_title',".izocloud.com",['class' => 'izo-form-input-readOnly' , 'style' => ' border-top-right-radius:10px;border-bottom-right-radius:10px;' ,'readOnly' ]) !!}
                                </div>
                                <span class="error" id="domainError"></span>
                            </div>


                            <div class="col-xl-6 col-md-6 field_icon">
                                <b style="font-size:17px;font-family:Georgia, 'Times New Roman', Times, serif;color: #ec6808 !important">{{__('izo.password')}}</b>
                                <input type="password" class="izo-form-input-password" id="password" name="password" placeholder="{{__('izo.password_placeholder')}}">
                                <span class="toggle-password" data-password="hidden">
                                    <i  id="togglePassword">
                                        <svg fill="#000000" height="20px" width="20px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"  viewBox="0 0 512 512" xml:space="preserve">
                                            <g>
                                                <g>
                                                    <path d="M507.418,241.382C503.467,235.708,409.003,102.4,256,102.4S8.533,235.708,4.582,241.382c-6.11,8.789-6.11,20.446,0,29.235
                                                        C8.533,276.292,102.997,409.6,256,409.6s247.467-133.308,251.418-138.982C513.528,261.828,513.528,250.172,507.418,241.382z
                                                        M256,384C114.62,384,25.6,256,25.6,256S114.62,128,256,128s230.4,128,230.4,128S397.38,384,256,384z"/>
                                                </g>
                                            </g>
                                            <g>
                                                <g>
                                                    <path d="M256,153.6c-56.55,0-102.4,45.841-102.4,102.4S199.441,358.4,256,358.4c56.559,0,102.4-45.841,102.4-102.4
                                                        S312.55,153.6,256,153.6z M256,332.8c-42.351,0-76.8-34.449-76.8-76.8s34.449-76.8,76.8-76.8c42.351,0,76.8,34.449,76.8,76.8
                                                        C332.8,298.351,298.351,332.8,256,332.8z"/>
                                                </g>
                                            </g>
                                    </svg>
                                    </i>
                                </span>
                                <i class="fas fa-spinner fa-spin spinner hide_icon"></i>
                                <i class="fas fa-check success-icon hide_icon"></i>
                                <i class="fa fa-times-circle hide_icon"></i>
                                <span class="error" id="passwordError"></span>
                            </div>

                            <div class="col-xl-6 col-md-6 field_icon">
                                <b style="font-size:17px;font-family:Georgia, 'Times New Roman', Times, serif;color: #ec6808 !important">{{__('izo.confirm_password')}}</b>
                                <input type="password" class="izo-form-input-password" id="confirm-password" name="confirm-password" placeholder="{{__('izo.confirm_password_placeholder')}}">
                      
                                <i class="fas fa-spinner fa-spin spinner hide_icon "></i>
                                <i class="fas fa-check success-icon hide_icon"></i>
                                <i class="fa fa-times-circle hide_icon"></i>
                                <span class="error" id="passwordConfirmError"></span>
                            </div> 
                            <div class="col-xl-12 col-md-12">
                                 {{-- <div class="g-recaptcha" data-sitekey="6LczVIkqAAAAADNyyHb1kHh9okAGJuqAOG9YgUM3" data-action="LOGIN"></div>
                                <br/>  --}}
                                
                                {{-- {!! NoCaptcha::renderJs() !!} --}}
                                {!! NoCaptcha::display() !!}
                                @if ($errors->has('g-recaptcha-response'))
                                    <span class="text-danger">
                                        {{ $errors->first('g-recaptcha-response') }}
                                    </span>
                                @endif
                            </div> 

                            <div class="col-xl-12 col-md-12 ">
                                {{-- <div class="izo-form-input-save">Sign Up</div> --}}
                                {!! Form::submit(__('izo.signup'),['class' => 'izo-form-input-save ']) !!}
                            </div>
                            <div class="col-xl-12 col-md-12 text-center  sign-up-box">  
                                   
                                <a href="{{route('izoLogin')}}" class="sign-up-form" style="font-size:18px;text-decoration:underline">{{__('izo.signin')}}</a>
                               
                            </div>
                            <div class="col-xl-12 col-md-12 text-center" style="width: 80% ;margin:auto 10%; font-family:Georgia, 'Times New Roman', Times, serif;"> 
                                <p style="color:#838383;font-weight:600;text-transform:capitalize">
                                    {{-- @lang('If you do not have an account already, create your own account and manage your company from anywhere around the world through the link') --}}
                                    <br>
                                    <span dir="ltr">&copy; Copyright   {{ date('Y') }} All rights reserved. </span> <br>
                                    <span>{{  "IZO" }} - v5.12.24 </span> <br>
                                </p>
                             
                        </div>
                            <div class="clearfix"></div>
                            
                        {!! Form::close(); !!}
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 col-12 right_form_first_login">
                        <H1 class="text-center title_izo">
                            @lang('IZO CLOUD')
                        </H1>
                        
                        
                    </div>
                     
                     
                {{-- </div> --}}
            </div>
        </div>
        <script src="{{ asset('/sw.js') }}"></script>
        <script>
        if ("serviceWorker" in navigator) {
            // Register a service worker hosted at the root of the
            // site using the default scope.
            navigator.serviceWorker.register("/sw.js").then(
            (registration) => {
                // console.log("Service worker registration succeeded:", registration);
            },
            (error) => {
                // console.error(`Service worker registration failed: ${error}`);
            },
            );
        } else {
            // console.error("Service workers are not supported.");
        }
        </script>
    </body>
@endsection
@include('izo_user.layouts.js.register')
