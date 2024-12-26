@extends('izo_user.layouts.app')

@section('title',__('izo.login'))


@php 
    $left_box            = in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')) ? 'initial' : '50px';
    $right_box           = in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')) ? '50px' : 'initial';
    $translate           = in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')) ? '50%' : '-50%';
    $margin_left         = in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')) ? 'initial' : '50%';
    $margin_right        = in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')) ? '50%' : 'initial';
    $left_margin         = in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')) ? 'initial' : '10px';
    $right_margin        = in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')) ? '10px' : 'initial';
    $parent_left_margin  = in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')) ? 'initial' : '-40px';
    $parent_right_margin = in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')) ? '-40px' : 'initial';
@endphp

@section('content')
    @section('app_css')
        <style>
                .pas {
                position: relative;
                }
                .toggle-password {
                    position: absolute;
                    top: 55px;
                    right: 20px;
                    z-index:10000;
                    transform: translateY(-50%);
                    cursor: pointer;
                }

                /* Optional: Style for the eye icon */
                .eye-icon::before {
                    content: '\1F441'; /* Unicode character for an eye symbol */
                    font-size: 1.5em;
                }
                /* Hide the default checkbox */
                input[type="checkbox"] {
                    position: absolute;
                    opacity: 0;
                    width: 0;
                    height: 0;
                }

                /* Create a custom checkbox */
                .custom-checkbox {
                    position: relative;
                    display: inline-block;
                    width: 20px;  /* Adjust the width */
                    height: 20px; /* Adjust the height */
                    background-color: #ffffff; /* Default background color */
                    border: 1px solid #000; /* Optional: Make it rounded */
                    border-radius: 0px; /* Optional: Make it rounded */
                }

                /* Create the checkmark (hidden by default) */
                .custom-checkbox::after {
                    content: "";
                    position: absolute;
                    display: none;
                    left: 7px;
                    top: 3px;
                    width: 5px;
                    height: 10px;
                    border: solid white;
                    border-width: 0 3px 3px 0;
                    transform: rotate(45deg);
                }

                /* Show the checkmark when the checkbox is checked */
                input[type="checkbox"]:checked + .custom-checkbox::after {
                    display: block;
                }

                /* Change background color when checked */
                input[type="checkbox"]:checked + .custom-checkbox {
                    background-color: #ec6808;
                    border:1px solid #ec6808;
                }

            .loading{
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
                /* background-image:url("../../../uploads/IZO-D2.gif"); */
                background-size: cover;
                background-attachment: fixed;
                background-repeat:  no-repeat; 
                background-position: center;
                
                background-color:#fff !important;
                /* background:linear-gradient(to top left ,rgb(235, 249, 255) 10% , rgb(255, 239, 223) 40% ,#cecece 100%); */
            }
            .left_form_first_login h1{
                padding:20px 10px;
                color:#fefefe;
                position: absolute;
                top: -31px !important;
                left: -10.5px !important;
                border-top-right-radius:10px;
                border-top-left-radius:10px;
                width: calc(100% + 21.9px) ;
                background-color: #303030;
            }
            .left_form_first_login{
                border: 0px solid black;
                border-radius: 10px;
                padding: 10px;
                box-shadow:1px 1px 100px #797979;
                background: rgb(255, 255, 255);
            }
            .right_form_first_login{
                display: none !important;
                border: 0px solid black;
                border-radius: 10px;
                padding: 10px; 
                background: rgba(64, 64, 110, 0);
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
                width: 68%;
                border-radius: 10px;
                padding: 10px 30px;
                height: auto;
                transform: translateY(15%);
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
                background-position:  top;
                background-repeat: no-repeat; 
                /* box-shadow: 1px 1px 10px black  ; */
                text-align: center;
                line-height:30px;
                font-family:Georgia, 'Times New Roman', Times, serif;
                font-size: 20px;
                text-transform: capitalize;
                font-weight: 300;
                /* margin:50px auto ; */
                height: 10px; ;
                left: -25.0192px;
                /* border:1px solid #303030; */
                box-shadow: 0px 1px 2px black;
                margin-bottom: 60px;
                top: 40px;
                width: calc(100% + 51px);
                position: relative;
            }
            .title_izo{
                margin:20px 5% ;
                text-align: left;
                line-height:70px;
                padding-left: 20px;
                font-family:Georgia, 'Times New Roman', Times, serif;
                font-size: 75px;
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
            .izo-form-input{
                font-family:Georgia, 'Times New Roman', Times, serif;
                width: 100%;
                border-radius: 10px;
                padding: 10px;
                margin: 10px auto ;
                font-size: 17px;
                border: 1px solid #3a3a3a33;
            }
            .izo-form-input-password{
                font-family:Georgia, 'Times New Roman', Times, serif;
                width: 100% !important;
                border-radius: 10px !important;
                padding: 10px !important;
                margin: 10px auto  !important;
                font-size: 17px !important;
                border: 1px solid #3a3a3a33 !important
            }
            .izo-group-form{  
                display: flex;
                justify-items: center; 
            }
            .izo-form-input-readOnly{   
                width: 100%;
                border-radius: 0px;
                padding: 10px;
                margin: 10px auto ;
                border: 1px solid #e86000;
                background-color: #e86000;
                color: white !important;
                font-size: 27px;
                font-weight: bolder;
            }
            :-ms-input-placeholder{
                 color:#e86000;
            }
            .izo-form-input-mobile{   
                width: 100%;
                border-radius: 0px;
                padding: 10px;
                margin: 10px auto ;
                border: 1px solid #313131;
                background-color: #fefefe;
                color: rgb(70, 70, 70) !important;
                font-size: 16px;
                font-weight: bolder;
            }
            .logo-style{                  margin: 15px auto  ;
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
            
            .izo-form-input-save{
                font-family:Georgia, 'Times New Roman', Times, serif;   
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
                display: none !important;
                    display: none;
            }
            .right_form_first_login_top{
                display: none !important;
                border: 2px solid black;
                border-radius: 10px;
                padding: 10px; 
                background: rgba(64, 64, 110, 0);
                display: none;
            }
            @media (max-width: 600px) {
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
                    display: none !important;
                    font-weight: 800;
                    text-align: center;
                    display: block;
                    color: #313131;
                    letter-spacing: 1px;
                    font-family:Georgia, 'Times New Roman', Times, serif;
                }
                body{
                    box-sizing: border-box;
                    /* background-image:url("../../../uploads/IZO-D2.gif"); */
                    background-size: cover;
                    background-attachment: fixed;
                    background-repeat:  no-repeat; 
                    background-position: center;
                    background-color:#fff !important;
                    /* background:linear-gradient(to top left ,rgb(235, 249, 255) 10% , rgb(255, 239, 223) 100% ,#cecece 90%); */
                }
                .left_form_first_login h1{
                    padding:20px 10px;
                    color:#fefefe;
                    position: absolute;
                    top: -59px !important;
                    left: -10.5px !important;
                    border-top-right-radius:10px;
                    border-top-left-radius:10px;
                    width: calc(100% + 21.9px) ;
                    background-color: #303030;
                }
                .left_form_first_login{
                    border: 0px solid black;
                    border-radius: 00px;
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
                }
                .right_form_first_login_top{
                    display: none !important;
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
                    background-position:  top;
                    background-repeat: no-repeat; 
                    /* box-shadow: 1px 1px 10px black  ; */
                    text-align: center;
                    line-height:30px;
                    font-family:Georgia, 'Times New Roman', Times, serif;
                    font-size: 20px;
                    text-transform: capitalize;
                    font-weight: 300;
                    /* margin:50px auto ; */
                    height: 10px; ;
                    left: -25.0192px;
                    /* border:0px solid #30303011; */
                    box-shadow: 0px 1px 2px black;
                    margin-bottom: 60px;
                    top: 40px;
                    width: calc(100% + 51px);
                    position: relative;
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
                .izo-form-input{
                    font-family:Georgia, 'Times New Roman', Times, serif;
                    width: 100%;
                    border-radius: 10px;
                    padding: 10px;
                    margin: 10px auto ;
                    font-size: 17px;
                    border: 1px solid #3a3a3a33;
                }
                .izo-form-input-password{
                    font-family:Georgia, 'Times New Roman', Times, serif;
                    width: 100% !important;
                    border-radius: 10px !important;
                    padding: 10px !important;
                    margin: 10px auto  !important;
                    font-size: 17px !important;
                    border: 1px solid #3a3a3a33 !important
                }
                .izo-group-form{  
                    display: flex;
                    justify-items: center; 
                }
                .izo-form-input-readOnly{   
                    width: 100%;
                    border-radius: 0px;
                    padding: 10px;
                    margin: 10px auto ;
                    border: 1px solid #e86000;
                    background-color: #e86000;
                    color: white !important;
                    font-size: 16px;
                    font-weight: bolder;
                }
                :-ms-input-placeholder{
                    color:#e86000;
                }
                .izo-form-input-mobile{   
                    width: 100%;
                    border-radius: 0px;
                    padding: 10px;
                    margin: 10px auto ;
                    border: 1px solid #313131;
                    background-color: #fefefe;
                    color: rgb(70, 70, 70) !important;
                    font-size: 16px;
                    font-weight: bolder;
                }
                .logo-style{                      margin: 15px auto  ;
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
                
                .izo-form-input-save{
                    font-family:Georgia, 'Times New Roman', Times, serif;   
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
                    display: none !important;
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
                    /* background-image:url("../../../uploads/IZO-D2.gif"); */
                    background-size: cover;
                    background-attachment: fixed;
                    background-repeat:  no-repeat; 
                    background-position: center;
                    background-color:#fff !important;
                    /* background:linear-gradient(to top left ,rgb(235, 249, 255) 10% , rgb(255, 239, 223) 100% ,#cecece 90%); */
                }
                .left_form_first_login h1{
                    padding:20px 10px;
                    color:#fefefe;
                    position: absolute;
                    top: -59px !important;
                    left: -10.5px !important;
                    border-top-right-radius:10px;
                    border-top-left-radius:10px;
                    width: calc(100% + 21.9px) ;
                    background-color: #303030;
                }
                .left_form_first_login{
                    border: 0px solid black;
                    border-radius: 00px;
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
                }
                .right_form_first_login_top{
                    display: none !important;
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
                    background-position:  top;
                    background-repeat: no-repeat; 
                    /* box-shadow: 1px 1px 10px black  ; */
                    text-align: center;
                    line-height:30px;
                    font-family:Georgia, 'Times New Roman', Times, serif;
                    font-size: 25px;
                    text-transform: capitalize;
                    font-weight: 300;
                    /* margin:50px auto ; */
                    height: 10px; ;
                    left: -25.0192px;
                    /* border:1px solid #303030; */
                    box-shadow: 0px 1px 2px black;
                    margin-bottom: 60px;
                    top: 40px;
                    width: calc(100% + 51px);
                    position: relative;
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
                .izo-form-input{
                    font-family:Georgia, 'Times New Roman', Times, serif;
                    width: 100%;
                    border-radius: 10px;
                    padding: 10px;
                    margin: 10px auto ;
                    font-size: 17px;
                    border: 1px solid #3a3a3a33;
                }
                .izo-form-input-password{
                    font-family:Georgia, 'Times New Roman', Times, serif;
                    width: 100% !important;
                    border-radius: 10px !important;
                    padding: 10px !important;
                    margin: 10px auto  !important;
                    font-size: 17px !important;
                    border: 1px solid #3a3a3a33 !important
                }
                .izo-group-form{  
                    display: flex;
                    justify-items: center; 
                }
                .izo-form-input-readOnly{   
                    width: 100%;
                    border-radius: 0px;
                    padding: 10px;
                    margin: 10px auto ;
                    border: 1px solid #e86000;
                    background-color: #e86000;
                    color: white !important;
                    font-size: 23px;
                    font-weight: bolder;
                }
                :-ms-input-placeholder{
                    color:#e86000;
                }
                .izo-form-input-mobile{   
                    width: 100%;
                    border-radius: 0px;
                    padding: 10px;
                    margin: 10px auto ;
                    border: 1px solid #313131;
                    background-color: #fefefe;
                    color: rgb(70, 70, 70) !important;
                    font-size: 23px;
                    font-weight: bolder;
                }
                .logo-style{                      margin: 15px auto  ;
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
                
                .izo-form-input-save{
                    font-family:Georgia, 'Times New Roman', Times, serif;   
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
                    display: none !important;
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
                    background-image:url("../../../uploads/IZO-D2.gif");
                    background-size: cover;
                    background-attachment: fixed;
                    background-repeat:  no-repeat; 
                    background-position: center;
                    /* background:linear-gradient(to top left ,rgb(255, 239, 223) 90% , rgb(255, 239, 223) 100% ,#cecece 60%); */
                }
                .left_form_first_login h1{
                    padding:20px 10px;
                    color:#fefefe;
                    position: absolute;
                    top: -31px !important;
                    left: -10.5px !important;
                    border-top-right-radius:10px;
                    border-top-left-radius:10px;
                    width: calc(100% + 21.9px) ;
                    background-color: #303030;
                }
                .left_form_first_login{
                    border: 0px solid black;
                    border-radius: 10px;
                    padding: 10px;
                    box-shadow:1px 1px 100px #79797900;
                    background: rgb(255, 255, 255);
                }
                .right_form_first_login{
                    display: none !important;
                    border: 0px solid black;
                    border-radius: 10px;
                    padding: 10px; 
                    background: rgba(64, 64, 110, 0);
                    display: block;
                }
                .right_form_first_login_top{
                    display: none !important;
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
                    background-position:  top;
                    background-repeat: no-repeat; 
                    /* box-shadow: 1px 1px 10px black  ; */
                    text-align: center;
                    line-height:20px;
                    font-family:Georgia, 'Times New Roman', Times, serif;
                    font-size: 20px;
                    text-transform: capitalize;
                    font-weight: 300;
                    /* margin:50px auto ; */
                    height: 10px; ;
                    left: -25.0192px;
                    /* border:1px solid #303030; */
                    box-shadow: 0px 1px 2px black;
                    margin-bottom: 60px;
                    top: 40px;
                    width: calc(100% + 51px);
                    position: relative;
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
                .izo-form-input{
                    font-family:Georgia, 'Times New Roman', Times, serif;
                    width: 100%;
                    border-radius: 10px;
                    padding: 10px;
                    margin: 10px auto ;
                    font-size: 17px;
                    border: 1px solid #3a3a3a33;
                }
                .izo-form-input-password{
                    font-family:Georgia, 'Times New Roman', Times, serif;
                    width: 100% !important;
                    border-radius: 10px !important;
                    padding: 10px !important;
                    margin: 10px auto  !important;
                    font-size: 17px !important;
                    border: 1px solid #3a3a3a33 !important
                }
                .izo-group-form{  
                    display: flex;
                    justify-items: center; 
                }
                .izo-form-input-readOnly{   
                    width: 100%;
                    border-radius: 0px;
                    padding: 10px;
                    margin: 10px auto ;
                    border: 1px solid #e86000;
                    background-color: #e86000;
                    color: white !important;
                    font-size: 16px;
                    font-weight: bolder;
                }
                :-ms-input-placeholder{
                    color:#e86000;
                }
                .izo-form-input-mobile{   
                    width: 100%;
                    border-radius: 0px;
                    padding: 10px;
                    margin: 10px auto ;
                    border: 1px solid #313131;
                    background-color: #fefefe;
                    color: rgb(70, 70, 70) !important;
                    font-size: 16px;
                    font-weight: bolder;
                }
                .logo-style{                      margin: 15px auto  ;
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
                
                .izo-form-input-save{
                    font-family:Georgia, 'Times New Roman', Times, serif;   
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
                        display: none !important;
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
                        /* background-image:url("../../../uploads/IZO-D2.gif"); */
                        background-size: cover;
                        background-attachment: fixed;
                        background-repeat:  no-repeat; 
                        background-position: center;
                        background-color:#fff !important;
                        /* background:linear-gradient(to top left ,rgb(235, 249, 255) 10% , rgb(255, 239, 223) 100% ,#cecece 90%); */
                    }
                    .left_form_first_login h1{
                        padding:20px 10px;
                        color:#fefefe;
                        position: absolute;
                        top: -59px !important;
                        left: -10.5px !important;
                        border-top-right-radius:10px;
                        border-top-left-radius:10px;
                        width: calc(100% + 21.9px) ;
                        background-color: #303030;
                    }
                    .left_form_first_login{
                        border: 0px solid black;
                        border-radius: 00px;
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
                    }
                    .right_form_first_login_top{
                        display: none !important;
                        border: 0px solid black;
                        border-radius: 00px;
                        padding: 10px; 
                        font-family:Georgia, 'Times New Roman', Times, serif;
                        background: rgba(64, 64, 110, 0);
                        display: block;
                    }
                    .mainbox{
                        border: 0px solid rgb(249, 0, 0);
                        border-radius: 0px;
                        padding: 0px ;
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
                        padding: 0px 00px;
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
                        background-position:  top;
                        background-repeat: no-repeat; 
                        /* box-shadow: 1px 1px 10px black  ; */
                        text-align: center;
                        line-height:30px;
                        font-family:Georgia, 'Times New Roman', Times, serif;
                        font-size: 25px;
                        text-transform: capitalize;
                        font-weight: 300;
                        /* margin:50px auto ; */
                        height: 10px; ;
                        left: -25.0192px;
                        /* border:1px solid #303030; */
                        box-shadow: 0px 1px 2px black;
                        margin-bottom: 60px;
                        top: 40px;
                        width: calc(100% + 51px);
                        position: relative;
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
                    .izo-form-input{
                        font-family:Georgia, 'Times New Roman', Times, serif;
                        width: 100%;
                        border-radius: 10px;
                        padding: 10px;
                        margin: 10px auto ;
                        font-size: 17px;
                        border: 1px solid #3a3a3a33;
                    }
                    .izo-form-input-password{
                        font-family:Georgia, 'Times New Roman', Times, serif;
                        width: 100% !important;
                        border-radius: 10px !important;
                        padding: 10px !important;
                        margin: 10px auto  !important;
                        font-size: 17px !important;
                        border: 1px solid #3a3a3a33 !important
                    }
                    .izo-group-form{  
                        display: flex;
                        justify-items: center; 
                    }
                    .izo-form-input-readOnly{   
                        width: 100%;
                        border-radius: 0px;
                        padding: 10px;
                        margin: 10px auto ;
                        border: 1px solid #e86000;
                        background-color: #e86000;
                        color: white !important;
                        font-size: 23px;
                        font-weight: bolder;
                    }
                    :-ms-input-placeholder{
                        color:#e86000;
                    }
                    .izo-form-input-mobile{   
                        width: 100%;
                        border-radius: 0px;
                        padding: 10px;
                        margin: 10px auto ;
                        border: 1px solid #313131;
                        background-color: #fefefe;
                        color: rgb(70, 70, 70) !important;
                        font-size: 23px;
                        font-weight: bolder;
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
                    
                    .izo-form-input-save{
                        font-family:Georgia, 'Times New Roman', Times, serif;   
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
    @php
        
    @endphp
    <body lang="{{ session()->get('lang', config('app.locale')) }}" dir="{{in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')) ? 'rtl' : 'ltr'}}">
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
       		
            //  dd(session()->all());
        @endphp
        @if(isset($email))
            @if($email != null && $password != null)
                <div class="loading">
                    <div class="loading-content">
                        <h1 class="text-center">
                            <img class="logo-style" style='width:200px !important' height=75 src="{{asset('logo-white.png')}}" alt="logo">
                            <br>
                            <small>{!!__('izo.waiting')!!}</small>
                        </h1>
                    </div>
                </div>
            @endif
        @endif
        <form hidden action="https://izocloud.com" id="go-home" method="GET">
            <button id="go_home"  type="submit">Go Home</button>
        </form>
        <form hidden action="https://izocloud.com" id="go-home-2" method="GET">
            <input type="hidden" name="delete_log_out_back" value="yes">
            <button id="go_home"  type="submit">Go Home</button>
        </form>
        @php
            $dr = isset($logoutBack)?$logoutBack:'';
        @endphp
        <input type="hidden" name="log_out" id="log_out" value="{{$dr}}">
        {{-- @if(request()->session()->get('url.intended') != null && request()->session()->get('url.intended') != 'http://localhost:8000')
            @if( request()->session()->get('url.intended') != null && !in_array(request()->session()->get('url.intended'),$list_domains))
            @endif
        @endif --}}
        @if(isset($list_domains))
            <input type="hidden" id="domain_name_array" value="{{json_encode($list_domains)}}">
        @else
            <input type="hidden" id="domain_name_array" value="{{json_encode([])}}">
        @endif
        <input type="hidden" id="domain_name_current" value="{{parse_url(request()->root(),PHP_URL_HOST)}}">
        
       

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


        <h3 class="title_main" > {{"IZO CLOUD"}}</h3>
        <div class="container mainbox">
            <div class="row childbox">
                {{-- <div class=""> --}}
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 col-12 right_form_first_login_top">
                        <H1 class="text-center title_izo">
                            @lang('IZO CLOUD')
                            
                        </H1>
                        <p class="description">
                            @lang('It is the ideal choice for managing your facility and companies smoothly, simplified and without complexity')
                        </p>
                        
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 col-12 left_form_first_login">
                        
                        <div class="col-xl-12 col-md-12 text-center" style="text-transform: uppercase;background-color:#fff;font-weight:bolder;">
                        
                            <img class="logo-style"  src="{{asset('logo.png')}}" alt="logo">
                            
                             
                        </div> 
                        <br>
                        {!! Form::open(['url' => route('izoLoginAccount'), 'method' => 'post', 'id' => 'first_login_form','files' => true ]) !!}
                        <input type="hidden" id="domain_name_sub" name="domain_name_sub" value="{{$subdomain}}">
                        @if(isset($email))
                            @if($email != null && $password != null)
                                <input type="hidden" id="redirect" name="redirect" value="{{$redirect}}">
                            @endif
                        @endif
                        
                        <div class="col-xs-12">
                            <h3 style="color: #000 !important;font-family:Georgia, 'Times New Roman', Times, serif;" ><b>{{__('izo.login')}}</b></h3>
                            <br>
                        </div>
                        <div class="col-xl-12 col-md-12">
                                @if(isset($list))
                                    <input type="hidden" name="info_database"      value="{{$list['database']}}" >
                                    <input type="hidden" name="info_database_user" value="{{$list['database_user']}}" >
                                    <input type="hidden" name="info_domain_url"    value="{{$list['domain_url']}}" >
                                    <input type="hidden" name="info_domain"        value="{{$list['domain']}}" >
                                @endif
                                <b style="font-size:17px;color: #ec6808 !important;font-family:Georgia, 'Times New Roman', Times, serif;">{{__('izo.email_address')}}</b>
                                {!! Form::text('email',(isset($email))?$email:null,['class' => 'izo-form-input','id'=>'email', 'placeholder' => __('izo.email_placeholder') ]) !!}
                                <span class="error" id="emailError"></span>
                            </div>
                            @php 
                                $pass = (isset($password))?$password:null;
                                @endphp 
                            <div class="col-xl-12 col-md-12 pas">
                                <b  style="font-size:17px;color: #ec6808 !important;font-family:Georgia, 'Times New Roman', Times, serif;">{{__('izo.password')}}</b>
                                <input type="password" class="izo-form-input-password" id='password' value="{{$pass}}" name="password" placeholder="{{__('izo.password_placeholder')}}">
                                <span class="error" id="passwordError"></span>
                                <span class="toggle-password">
                                    <i class="eye-icon" id="togglePassword"></i>
                                </span>
                            </div>
                            <div class="col-xl-12 col-md-12">
                                <b  style="font-size:17px;color: #ec6808 !important;font-family:Georgia, 'Times New Roman', Times, serif;"><a href="{{\URL::to('/forget-password')}}">{{__('izo.forget_password')}}</a></b>
                            </div>
                            <div class="col-xl-12 col-md-12">
                                <div class="form-group">
                                    <div class="checkbox icheck col-md-12 col-12 text-left py-5">
                                    <div class=" col-12 ">
                                        <label style="color: #0c0c0c ; font-size:15px;margin-left:{{$parent_left_margin}};margin-right:{{$parent_right_margin}} ">
                                            <small>&nbsp;</small>    
                                            {!! Form::checkbox('logout_other', 1, (isset($logoutOther))?$logoutOther:null, [ 'class' => 'input-icheck', 'id' => 'logout_other']); !!}
                                            <span class="custom-checkbox"></span>
                                            <span style="position:relative;font-family:Georgia, 'Times New Roman', Times, serif;top:-5px;left:{{$left_margin}};right:{{$right_margin}}">{{ __( 'izo.logout_form_other_device' ) }} </span> 
                                        </label>
                                    </div>
                                    <div class=" col-12 " style="line-height: 8px">
                                        &nbsp;
                                    </div>
                                    <div class=" col-12 ">
                                        <label style="color: #0c0c0c; font-size:15px;margin-left:{{$parent_left_margin}};margin-right:{{$parent_right_margin}} ">
                                            <small>&nbsp;</small>   
                                            {!! Form::checkbox('remember', 1, old('remember') ? true : false, [ 'class' => 'input-icheck', 'id' => 'remember']); !!}
                                        <span class="custom-checkbox"></span>
                                        <span style="position:relative;top:-5px;font-family:Georgia, 'Times New Roman', Times, serif;left:{{$left_margin}};right:{{$right_margin}}">{{ __( 'izo.remember_me' ) }} </span>
                                        </label>
                                    </div>
                                    </div>
                                </div>
                                 
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
                            <div class="col-xl-12 col-md-12">
                                {!! Form::submit(__('izo.login'),['class' => 'izo-form-input-save']) !!}
                            </div>
                            <div class="col-xl-12 col-md-12 text-center sign-up-box">
                                <a href="{{route('izoRegister')}}" class="sign-up-form"   >{{__('izo.signup')}}</a>
                            </div>
                            {{-- <div class="col-xl-12 col-md-12 text-center sign-up-box">
                                <a href="{{\URL::to('/send-symfony-email')}}" class="sign-up-form"   >{{__('test')}}</a>
                            </div> --}}
                            
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
                        <p class="description">
                            @lang('It is the ideal choice for managing your facility and companies smoothly, simplified and without complexity')
                        </p>
                        
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

@include('izo_user.layouts.js.login')


 