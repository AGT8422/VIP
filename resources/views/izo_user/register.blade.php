@extends('izo_user.layouts.app')

@section('title','register')
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
    $padding_left        = in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')) ? 'initial' : '130px';
    $padding_right       = in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')) ? '130px' : 'initial';
    $left_mobile         = in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')) ? 'initial' : '20px';
    $right_mobile        = in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')) ? '20px' : 'initial';
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
            .izo-form-input{
                width: 100%;
                border-radius: 0px;
                padding: 10px;
                margin: 10px auto ;
                font-size: 17px;
                border-radius: 10px;
                border: 1px solid #3a3a3a33;
            }
            .izo-form-input-password{
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
            .izo-form-input{
                width: 100%;
                border-radius: 0px;
                padding: 10px;
                margin: 10px auto ;
                font-size: 17px;
                border-radius: 10px;
                border: 1px solid #3a3a3a33;
            }
            .izo-form-input-password{
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
                background-image:url("../../../uploads/IZO-D2.gif");
                background-size: cover;
                background-attachment: fixed;
                background-repeat:  no-repeat; 
                background-position: center;    
                }
                .left_form_first_login{
                    border: 0px solid black;
                    border-radius: 10px 10px 10px 10px;
                    /* border-radius: 10px; */
                    padding: 10px;
                    box-shadow:1px 1px 100px #797979;
                    background: rgb(255, 255, 255);

                }
                .right_form_first_login{
                    display: none !important;
                    border: 2px solid black;
                    border-radius: 10px;
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
                    border-radius: 10px;
                    padding: 10px; 
                    background: rgba(64, 64, 110, 0);
                    display: none;
                }
                .mainbox{
                        border: 0px solid rgb(4, 0, 249);
                        border-radius: 10px;
                        padding: 0px 0px;
                        background: rgba(0, 0, 0, 0);
                        width: 100%;
                        display: block; 
                }
                .childbox{
                    border: 0px solid rgb(4, 0, 249);
                    width: 100%;
                    border-radius: 10px;
                    padding: 10px 10px;
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
                .izo-form-input{
                    width: 100%;
                    border-radius: 0px;
                    padding: 10px;
                    margin: 10px auto ;
                    font-size: 17px;
                    border-radius: 10px;
                    border: 1px solid #3a3a3a33;
                }
                .izo-form-input-password{
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
                    cursor: pointer;
                    text-align: center;   
                    width: 100%;
                    border-radius: 10px;
                    padding: 10px;
                    margin: 10px auto ;
                    border: 1px solid #ec6808;
                    background-color: #ec6808;
                    color: white !important;
                    font-size: 10px;
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
                background-image:url("../../../uploads/IZO-D2.gif");
                background-size: cover;
                background-attachment: fixed;
                background-repeat:  no-repeat; 
                background-position: center;    
                }
                .left_form_first_login{
                    border: 0px solid black;
                    border-radius: 10px 10px 10px 10px;
                    /* border-radius: 10px; */
                    padding: 10px;
                    box-shadow:1px 1px 100px #797979;
                    background: rgb(255, 255, 255);
                }
                .right_form_first_login{
                    display: none !important;
                    border: 2px solid black;
                    border-radius: 10px;
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
                    border-radius: 10px;
                    padding: 10px; 
                    background: rgba(64, 64, 110, 0);
                    display: none;
                }
                .mainbox{
                    border: 0px solid rgb(66, 249, 0);
                    border-radius: 10px;
                    padding: 3%;
                    background: rgba(0, 0, 0, 0);
                    width: 100%; 
                    display: block; 
                }
                .childbox{
                    border: 0px solid rgb(0, 249, 0);
                    width: 100%;
                    border-radius: 10px;
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
                .izo-form-input{
                    width: 100%;
                    border-radius: 0px;
                    padding: 10px;
                    margin: 10px auto ;
                    font-size: 17px;
                    border-radius: 10px;
                    border: 1px solid #3a3a3a33;
                }
                .izo-form-input-password{
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
                .izo-form-input{
                    width: 100%;
                    border-radius: 0px;
                    padding: 10px;
                    margin: 10px auto ;
                    font-size: 17px;
                    border-radius: 10px;
                    border: 1px solid #3a3a3a33;
                }
                .izo-form-input-password{
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
                    cursor: pointer;
                    text-align: center;   
                    width: 100%;
                    border-radius: 10px;
                    padding: 10px;
                    margin: 10px auto ;
                    border: 1px solid #ec6808;
                    background-color: #ec6808;
                    color: white !important;
                    font-size: 10px;
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
                background-image:url("../../../uploads/IZO-D2.gif");
                background-size: cover;
                background-attachment: fixed;
                background-repeat:  no-repeat; 
                background-position: center;    
                }
                .left_form_first_login{
                    border: 0px solid black;
                    border-radius: 10px 10px 10px 10px;
                    /* border-radius: 10px; */
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
                    border-radius: 10px;
                    padding: 10px; 
                    font-family:Georgia, 'Times New Roman', Times, serif;
                    background: rgba(64, 64, 110, 0);
                    display: block;
                }
                .mainbox{
                    border: 0px solid rgb(249, 0, 0);
                    border-radius: 10px;
                    padding: 10px ;
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
                    border-radius: 10px;
                    padding: 10px 30px;
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
                .izo-form-input{
                    width: 100%;
                    border-radius: 0px;
                    padding: 10px;
                    margin: 10px auto ;
                    font-size: 17px;
                    border-radius: 10px;
                    border: 1px solid #3a3a3a33;
                }
                .izo-form-input-password{
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
             
        </style>
    @endsection
    <body  >
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
            $subdomain = $subdomain; 
       
            
        @endphp
        <div class="loading">
            <div class="loading-content">
                <h1>IZO <small>waiting.....</small></h1>
            </div>
        </div>
        <form hidden action="https://localhost:8000/register-account" id="go-home" method="GET">
            <button id="go_home"  type="submit">Go Home</button>
        </form>
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
                        <div class="col-xl-12 col-md-12 text-left" style=" text-transform: capitalize;font-weight:bolder;font-family:Georgia, 'Times New Roman', Times, serif;">
                            <h3  style="font-weight:600 ;color:#666666fd;font-family:Georgia, 'Times New Roman', Times, serif;">{{__("izo.register_now")}}</h3>
                            <small style="font-weight:600 ;color:#666666fd;font-family:Georgia, 'Times New Roman', Times, serif;">
                        
                            </small>
                        </div> 
                        <br>
                        {!! Form::open(['url' => route('izoSaveAccount'), 'method' => 'post', 'id' => 'first_register_form','files' => true ]) !!}
                        
                        
                        <input type="hidden" id="domain_name_array" value="{{json_encode($list_domains)}}">
                        <input type="hidden" id="domain_name_current" value="{{parse_url(request()->root(),PHP_URL_HOST)}}">
                            <div class="col-xl-12 col-md-12 field_icon">
                                {!! Form::text('company_name',null,['class' => 'izo-form-input', 'min' => 3 ,'id'=>'company_name', 'placeholder' => __('izo.company_name_placeholder') ]) !!}
                                <i class="fas fa-spinner fa-spin spinner hide_icon"></i>
                                <i class="fas fa-check success-icon hide_icon"></i>
                                <i class="fa fa-times-circle hide_icon"></i>
                                 <span class="error" id="nameError"></span>
                            </div>  

                            <div class="col-xl-12 col-md-12 field_icon">
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
                                <div class="col-xl-12 col-md-12 field_icon_mobile">
                                    <i class="fas fa-spinner fa-spin spinner hide_icon"></i>
                                    <i class="fas fa-check success-icon hide_icon"></i>
                                    <i class="fa fa-times-circle hide_icon"></i>
                                    <div class="izo-group-form">
                                        {!! Form::select('mobile_code',['+971'=>' +971'],null,['class' => 'izo-form-input-mobile',  'id'=>'mobile_code' , 'style' => 'font-size:13px;position:absolute;right:'.$right_mobile.' !important;left:'.$left_mobile.' !important;top:5px;width:20%'  ]) !!}
                                        {!! Form::number('mobile',null,['class' => 'izo-form-input',  'id'=>'mobile' ,'data-max' => "9" , 'min'=>0,'max'=>9999999999 ,'style' => 'padding-left:'.$padding_left.';padding-right:'.$padding_right.';width:100%' , 'placeholder' => __('00 0000 000')  ]) !!}
                                    </div>
                                    <span class="error" id="mobileError"></span>
                                    @if ($errors->has('mobile'))
                                        <span class="text-danger">
                                            {{ $errors->first('mobile') }}
                                        </span>
                                    @endif
                                </div>
                                
                            <div class="col-xl-12 col-md-12 field_icon_domain " dir="ltr">
                                <i class="fas fa-spinner fa-spin spinner hide_icon"></i>
                                <i class="fas fa-check success-icon hide_icon"></i>
                                <i class="fa fa-times-circle hide_icon"></i>
                                <div class="izo-group-form">
                                    <div class="izo-group-form">
                                        {!! Form::text('domain_title',"https://",['class' => 'izo-form-input-readOnly', 'style' => 'width:40% ;font-size:15px;border-top-left-radius:10px;border-bottom-left-radius:10px;' ,'readOnly' ]) !!}
                                        {!! Form::text('domain_name',null,['class' => 'izo-form-input', 'min' => 3 , 'id'=>'domain_name' , 'style' => 'border-radius: 0px !important;width:80%', 'placeholder' => __('YourDomainName') ]) !!}
                                    </div>
                                    {!! Form::text('domain_title',".izocloud.com",['class' => 'izo-form-input-readOnly' , 'style' => ' border-top-right-radius:10px;border-bottom-right-radius:10px;' ,'readOnly' ]) !!}
                                </div>
                                <span class="error" id="domainError"></span>
                            </div>


                            <div class="col-xl-6 col-md-6 field_icon">
                                <input type="password" class="izo-form-input-password" id="password" name="password" placeholder="{{__('izo.password_placeholder')}}">
                                <i class="fas fa-spinner fa-spin spinner hide_icon"></i>
                                <i class="fas fa-check success-icon hide_icon"></i>
                                <i class="fa fa-times-circle hide_icon"></i>
                                <span class="error" id="passwordError"></span>
                            </div>

                            <div class="col-xl-6 col-md-6 field_icon">
                                <input type="password" class="izo-form-input-password" id="confirm-password" name="confirm-password" placeholder="{{__('izo.confirm_password_placeholder')}}">
                                <i class="fas fa-spinner fa-spin spinner hide_icon "></i>
                                <i class="fas fa-check success-icon hide_icon"></i>
                                <i class="fa fa-times-circle hide_icon"></i>
                                <span class="error" id="passwordConfirmError"></span>
                            </div> 
                            <div class="col-xl-12 col-md-12">
                                {{-- <div class="g-recaptcha" data-sitekey="6LczVIkqAAAAADNyyHb1kHh9okAGJuqAOG9YgUM3" data-action="LOGIN"></div>
                                <br/> --}}
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
        {!! NoCaptcha::renderJs() !!}
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
@endsection
@include('izo_user.layouts.js.register')
