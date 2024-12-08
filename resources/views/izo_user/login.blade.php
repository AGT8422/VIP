@extends('izo_user.layouts.app')

@section('title','login')

@section('content')
    @section('app_css')
        <style>
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
                background-image:url("../../../uploads/IZO-D2.gif");
                background-size: cover;
                    background-attachment: fixed;
                    background-repeat:  no-repeat; 
                    background-position: center;
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
                width: 100%;
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
            .izo-form-input{
                width: 100%;
                border-radius: 0px;
                padding: 10px;
                margin: 10px auto ;
                font-size: 20px;
                border: 1px solid #e86000
            }
            .izo-form-input-password{
                width: 100% !important;
                border-radius: 0px !important;
                padding: 10px !important;
                margin: 10px auto  !important;
                font-size: 20px !important;
                border: 1px solid #e86000 !important
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
            .izo-form-input-save{   
                width: 100%;
                border-radius: 0px;
                padding: 10px;
                margin: 10px auto ;
                border: 1px solid #303030;
                background-color: #2c2c2c;
                color: white !important;
                font-size: 27px;
                font-weight: bolder;
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
                    background-image:url("../../../uploads/IZO-D2.gif");
                    background-size: cover;
                    background-attachment: fixed;
                    background-repeat:  no-repeat; 
                    background-position: center;
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
                    border-radius: 10px;
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
                }
                .right_form_first_login_top{
                    display: none !important;
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
                .izo-form-input{
                    width: 100%;
                    border-radius: 0px;
                    padding: 10px;
                    margin: 10px auto ;
                    font-size: 16px;
                    border: 1px solid #e86000
                }
                .izo-form-input-password{
                    width: 100% !important;
                    border-radius: 0px !important;
                    padding: 10px !important;
                    margin: 10px auto  !important;
                    font-size: 16px !important;
                    border: 1px solid #e86000 !important
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
                .izo-form-input-save{   
                    width: 100%;
                    border-radius: 0px;
                    padding: 10px;
                    margin: 10px auto ;
                    border: 1px solid #303030;
                    background-color: #2c2c2c;
                    color: white !important;
                    font-size: 18px;
                    font-weight: bolder;
                }
            }
            @media (min-width: 600px) and  (max-width: 900px) {
                
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
                    background-image:url("../../../uploads/IZO-D2.gif");
                    background-size: cover;
                    background-attachment: fixed;
                    background-repeat:  no-repeat; 
                    background-position: center;
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
                    border-radius: 10px;
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
                }
                .right_form_first_login_top{
                    display: none !important;
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
                .izo-form-input{
                    width: 100%;
                    border-radius: 0px;
                    padding: 10px;
                    margin: 10px auto ;
                    font-size: 23px;
                    border: 1px solid #e86000
                }
                .izo-form-input-password{
                    width: 100% !important;
                    border-radius: 0px !important;
                    padding: 10px !important;
                    margin: 10px auto  !important;
                    font-size: 23px !important;
                    border: 1px solid #e86000 !important
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
                .izo-form-input-save{   
                    width: 100%;
                    border-radius: 0px;
                    padding: 10px;
                    margin: 10px auto ;
                    border: 1px solid #303030;
                    background-color: #2c2c2c;
                    color: white !important;
                    font-size: 23px;
                    font-weight: bolder;
                }
            }
            @media (min-width: 1024px) and (max-width:1400px){
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
                    box-shadow:1px 1px 100px #797979;
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
                .izo-form-input{
                    width: 100%;
                    border-radius: 0px;
                    padding: 10px;
                    margin: 10px auto ;
                    font-size: 16px;
                    border: 1px solid #e86000
                }
                .izo-form-input-password{
                    width: 100% !important;
                    border-radius: 0px !important;
                    padding: 10px !important;
                    margin: 10px auto  !important;
                    font-size: 16px !important;
                    border: 1px solid #e86000 !important
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
                .izo-form-input-save{   
                    width: 100%;
                    border-radius: 0px;
                    padding: 10px;
                    margin: 10px auto ;
                    border: 1px solid #303030;
                    background-color: #2c2c2c;
                    color: white !important;
                    font-size: 18px;
                    font-weight: bolder;
                }
            }
            @media (min-width: 900px) and (max-width: 1024px){

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
                        display: none;
                    }
                    .right_form_first_login_top{
                        display: none !important;
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
                    .izo-form-input{
                        width: 100%;
                        border-radius: 0px;
                        padding: 10px;
                        margin: 10px auto ;
                        font-size: 23px;
                        border: 1px solid #e86000
                    }
                    .izo-form-input-password{
                        width: 100% !important;
                        border-radius: 0px !important;
                        padding: 10px !important;
                        margin: 10px auto  !important;
                        font-size: 23px !important;
                        border: 1px solid #e86000 !important
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
                    .izo-form-input-save{   
                        width: 100%;
                        border-radius: 0px;
                        padding: 10px;
                        margin: 10px auto ;
                        border: 1px solid #303030;
                        background-color: #2c2c2c;
                        color: white !important;
                        font-size: 23px;
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
        @if(isset($email))
            @if($email != null && $password != null)
                <div class="loading">
                    <div class="loading-content">
                        <h1>IZO <small>waiting.....</small></h1>
                    </div>
                </div>
            @endif
        @endif
        <form hidden action="http://localhost:8000" id="go-home" method="GET">
            <button id="go_home"  type="submit">Go Home</button>
        </form>
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
                        
                        <div class="col-xl-12 col-md-12 text-center" style="text-transform: uppercase;font-weight:bolder;">
                            <h3 class="" style="font-weight:600"> {{"  "}}</h3>
                            <h1>IZO CLOUD</h1>
                            <p class="description_small">
                                <br>
                            </p>
                        </div> 
                        <br>
                        {!! Form::open(['url' => route('izoLoginAccount'), 'method' => 'post', 'id' => 'first_login_form','files' => true ]) !!}
                        <input type="hidden" id="domain_name_sub" name="domain_name_sub" value="{{$subdomain}}">
                        <div class="col-xl-12 col-md-12">
                                {!! Form::text('email',(isset($email))?$email:null,['class' => 'izo-form-input','id'=>'email', 'placeholder' => __('email') ]) !!}
                                <span class="error" id="emailError"></span>
                            </div>
                             @php 
                                $pass = (isset($password))?$password:null;
                             @endphp 
                            <div class="col-xl-12 col-md-12">
                                <input type="password" class="izo-form-input-password" id='password' value="{{$pass}}" name="password" placeholder="password">
                                <span class="error" id="passwordError"></span>
                            </div>
                            <div class="col-xl-12 col-md-12">
                                <div class="form-group">
                                    <div class="checkbox icheck col-md-6 col-12 text-left py-5">
                                        <label style="color: #0c0c0c">
                                            <small>&nbsp;</small>    
                                            <input type="checkbox" name="logout_other"  >&nbsp;&nbsp; {{"Logout Form Other Device"}}
                                        </label>
                                        <label style="color: #0c0c0c  ">
                                            <small>&nbsp;</small>   
                                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>&nbsp;&nbsp; {{"Remember Me"}}
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="checkbox icheck col-md-6 col-12  text-left py-5">
                                        
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-xl-12 col-md-12">
                                {!! Form::submit('Log In',['class' => 'izo-form-input-save']) !!}
                            </div>
                            
                            <div class="col-xl-12 col-md-12 text-center" style="width: 80% ;margin:auto 10%; font-family:Georgia, 'Times New Roman', Times, serif;"> 
                                    <p style="color:#838383;font-weight:600;text-transform:capitalize">
                                        @lang('If you do not have an account already, create your own account and manage your company from anywhere around the world through the link')
                                        <a href="{{route('izoRegister')}}" style="font-size:18px;text-decoration:underline">SignUp</a>
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
@include('izo_user.layouts.js.login')


 