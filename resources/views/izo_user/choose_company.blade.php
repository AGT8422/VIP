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
            
            .item_domains{
                box-shadow: 0px 0px 10px #3a3a3a33;
                background-color:#fff;
                margin:3px 0px;
                padding:10px;
            }
            .box_domains{
                outline:2px solid black;
                border-radius:10px;
                padding:10px;
                height:300px;
                overflow-y:scroll;
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
                
                .item_domains{
                    box-shadow: 0px 0px 10px #3a3a3a33;
                    background-color:#fff;
                    margin:3px 0px;
                    padding:10px;
                }
                .box_domains{
                    outline:2px solid black;
                    border-radius:10px;
                    padding:10px;
                    height:300px;
                    overflow-y:scroll;
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
                
                .item_domains{
                    box-shadow: 0px 0px 10px #3a3a3a33;
                    background-color:#fff;
                    margin:3px 0px;
                    padding:10px;
                }
                .box_domains{
                    outline:2px solid black;
                    border-radius:10px;
                    padding:10px;
                    height:300px;
                    overflow-y:scroll;
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
                
                .item_domains{
                    box-shadow: 0px 0px 10px #3a3a3a33;
                    background-color:#fff;
                    margin:3px 0px;
                    padding:10px;
                }
                .box_domains{
                    outline:2px solid black;
                    border-radius:10px;
                    padding:10px;
                    height:300px;
                    overflow-y:scroll;
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
                    
                    .item_domains{
                        box-shadow: 0px 0px 10px #3a3a3a33;
                        background-color:#fff;
                        margin:3px 0px;
                        padding:10px;
                    }
                    .box_domains{
                        outline:2px solid black;
                        border-radius:10px;
                        padding:10px;
                        height:300px;
                        overflow-y:scroll;
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
    <body lang="{{ session()->get('lang', config('app.locale')) }}" dir="{{in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')) ? 'rtl' : 'ltr'}}">
        
         
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
                         
                            <div class="col-xs-12">
                                <h3 style="color: #ec6808 !important;font-family:Georgia, 'Times New Roman', Times, serif;" ><b>{{__('izo.choose_website')}}</b> <small class="pull-right">{{__('Administrator')}}</small></h3>
                                <br>
                            </div>
                   

                            <div class="col-xl-12 col-md-12">
                                 <div class="box_domains">
                                    @foreach($list_domains as $l =>  $i)
                                        @if($i != "izo.izocloud.com")
                                            <div class="col-xl-12 col-md-12 item_domains">
                                                @php
                                                    $domain_name = "";
                                                    $text        = "email=".request()->session()->get("login_info.email")."_##password=".request()->session()->get("login_info.password")."_##logoutOther=1_##administrator=1_##database=".$list_database[$l]."_##domain_url=".$i."_##domain=".$list_dom[$l]."_##redirect=admin";
                                                    $text        =  Illuminate\Support\Facades\Crypt::encryptString($text);
                                                    $url         = $domain_name."/login-account-redirect"."/".$text;
                                                    
                                                @endphp
                                                <b  style="font-size:17px;color: #000 !important;font-family:Georgia, 'Times New Roman', Times, serif;"><a style="color: #000 !important;" href="{{\URL::to($url)}}">{{$i}}</a></b>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
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
         
         
    </body>
@endsection
 


 