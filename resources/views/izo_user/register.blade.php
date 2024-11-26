@extends('izo_user.layouts.app')

@section('title','register')

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
                font-size: 15px;
                border: 1px solid #e86000
            }
            .izo-form-input-password{
                width: 100% !important;
                border-radius: 0px !important;
                padding: 10px !important;
                margin: 10px auto  !important;
                font-size:  15px !important;
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
                font-size: 18px;
                font-weight: bolder;
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
            cursor: pointer;
            text-align: center;    
                width: 100%;
                border-radius: 0px;
                padding: 10px;
                margin: 10px auto ;
                border: 1px solid #303030;
                background-color: #2c2c2c;
                color: white !important;
                font-size: 27px;
                font-weight: bolder;
            /* } */  
            
            .loading{
                display: none;
                position: absolute;
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
                font-size: 18px;
                border: 1px solid #e86000
            }
            .izo-form-input-password{
                width: 100% !important;
                border-radius: 0px !important;
                padding: 10px !important;
                margin: 10px auto  !important;
                font-size: 18px !important;
                border: 1px solid #e86000  
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
                font-size: 18px;
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
                cursor: pointer;
                text-align: center;   
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
                    font-size: 16px;
                    border: 1px solid #e86000
                }
                .izo-form-input-password{
                    width: 100% !important;
                    border-radius: 0px !important;
                    padding: 10px !important;
                    margin: 10px auto  !important;
                    font-size: 16px !important;
                    border: 1px solid #e86000 
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
                    cursor: pointer;
                    text-align: center;   
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
                    font-size: 23px;
                    border: 1px solid #e86000
                }
                .izo-form-input-password{
                    width: 100% !important;
                    border-radius: 0px !important;
                    padding: 10px !important;
                    margin: 10px auto  !important;
                    font-size: 23px !important;
                    border: 1px solid #e86000  
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
                    cursor: pointer;
                    text-align: center;   
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
                    font-size: 16px;
                    border: 1px solid #e86000
                }
                .izo-form-input-password{
                    width: 100% !important;
                    border-radius: 0px !important;
                    padding: 10px !important;
                    margin: 10px auto  !important;
                    font-size: 16px !important;
                    border: 1px solid #e86000  
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
                    cursor: pointer;
                    text-align: center;   
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
                    font-size: 23px;
                    border: 1px solid #e86000
                }
                .izo-form-input-password{
                    width: 100% !important;
                    border-radius: 0px !important;
                    padding: 10px !important;
                    margin: 10px auto  !important;
                    font-size: 23px !important;
                    border: 1px solid #e86000  
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
                    cursor: pointer;
                    text-align: center;   
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
        <div class="loading">
            <div class="loading-content">
                <h1>IZO <small>waiting.....</small></h1>
            </div>
        </div>
        <form hidden action="https://izocloud.com/register-account" id="go-home" method="GET">
            <button id="go_home"  type="submit">Go Home</button>
        </form>
        <div class="container mainbox">
            <div class="row childbox">
                {{-- <div class=""> --}}
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 col-12 left_form_first_login">
                        <div class="col-xl-12 col-md-12 text-left" style=" text-transform: capitalize;font-weight:bolder;font-family:Georgia, 'Times New Roman', Times, serif;">
                            <h3  style="font-weight:600 ;color:#666666fd;font-family:Georgia, 'Times New Roman', Times, serif;">{{"create new account"}}</h3>
                            <small style="font-weight:600 ;color:#666666fd;font-family:Georgia, 'Times New Roman', Times, serif;">
                                @lang('It is the ideal choice for managing your facility and companies smoothly, simplified and without complexity')
                            </small>
                        </div> 
                        <br>
                        {!! Form::open(['url' => route('izoSaveAccount'), 'method' => 'post', 'id' => 'first_register_form','files' => true ]) !!}
                        {!! Anhskohbo\NoCaptcha\Facades\NoCaptcha::display() !!}
                        <input type="hidden" id="domain_name_array" value="{{json_encode($list_domains)}}">
                        <input type="hidden" id="domain_name_current" value="{{parse_url(request()->root(),PHP_URL_HOST)}}">
                            <div class="col-xl-12 col-md-12 field_icon">
                                {!! Form::text('company_name',null,['class' => 'izo-form-input', 'min' => 3 ,'id'=>'company_name', 'placeholder' => __('Company Name') ]) !!}
                                <i class="fas fa-spinner fa-spin spinner hide_icon"></i>
                                <i class="fas fa-check success-icon hide_icon"></i>
                                <i class="fa fa-times-circle hide_icon"></i>
                                 <span class="error" id="nameError"></span>
                            </div>  

                            <div class="col-xl-12 col-md-12 field_icon">
                                {!! Form::text('email',null,['class' => 'izo-form-input', 'id'=>'email' , 'placeholder' => __('email') ]) !!}
                                <span class="error" id="emailError"></span>
                                <i class="fas fa-spinner fa-spin spinner hide_icon"></i>
                                <i class="fas fa-check success-icon hide_icon"></i>
                                <i class="fa fa-times-circle hide_icon"></i>
                            </div>

                            <div class="col-xl-12 col-md-12 field_icon_domain ">
                                <i class="fas fa-spinner fa-spin spinner hide_icon"></i>
                                <i class="fas fa-check success-icon hide_icon"></i>
                                <i class="fa fa-times-circle hide_icon"></i>
                                <div class="izo-group-form">
                                    <div class="izo-group-form">
                                        {!! Form::text('domain_title',"https://",['class' => 'izo-form-input-readOnly', 'style' => 'width:30% ;font-size:16px' ,'readOnly' ]) !!}
                                        {!! Form::text('domain_name',null,['class' => 'izo-form-input', 'min' => 3 , 'id'=>'domain_name' , 'style' => 'width:70%', 'placeholder' => __('YourDomainName') ]) !!}
                                    </div>
                                    {!! Form::text('domain_title',".izocloud.com",['class' => 'izo-form-input-readOnly' ,'readOnly' ]) !!}
                                </div>
                                <span class="error" id="domainError"></span>
                            </div>

                            <div class="col-xl-12 col-md-12 field_icon_mobile">
                                <i class="fas fa-spinner fa-spin spinner hide_icon"></i>
                                <i class="fas fa-check success-icon hide_icon"></i>
                                <i class="fa fa-times-circle hide_icon"></i>
                                <div class="izo-group-form">
                                    {!! Form::select('mobile_code',['+971'=>'AED - +971'],null,['class' => 'izo-form-input-mobile',  'id'=>'mobile_code' , 'style' => 'width:20%'  ]) !!}
                                    {!! Form::number('mobile',null,['class' => 'izo-form-input',  'id'=>'mobile' ,'data-max' => "9" , 'min'=>0,'max'=>9999999999 ,'style' => 'width:80%' , 'placeholder' => __('00 0000 000')  ]) !!}
                                </div>
                                <span class="error" id="mobileError"></span>
                            </div>

                            <div class="col-xl-12 col-md-12 field_icon">
                                <input type="password" class="izo-form-input-password" id="password" name="password" placeholder="password">
                                <i class="fas fa-spinner fa-spin spinner hide_icon"></i>
                                <i class="fas fa-check success-icon hide_icon"></i>
                                <i class="fa fa-times-circle hide_icon"></i>
                                <span class="error" id="passwordError"></span>
                            </div>

                            <div class="col-xl-12 col-md-12 field_icon">
                                <input type="password" class="izo-form-input-password" id="confirm-password" name="confirm-password" placeholder="confirm password">
                                <i class="fas fa-spinner fa-spin spinner hide_icon "></i>
                                <i class="fas fa-check success-icon hide_icon"></i>
                                <i class="fa fa-times-circle hide_icon"></i>
                                <span class="error" id="passwordConfirmError"></span>
                            </div> 

                            <div class="col-xl-12 col-md-12">
                                {{-- <div class="izo-form-input-save">Sign Up</div> --}}
                                {!! Form::submit('Sign Up',['class' => 'izo-form-input-save']) !!}
                            </div>
                            <div class="col-xl-12 col-md-12 text-center"> 
                                <p style="color:#838383;font-weight:600;font-family:Georgia, 'Times New Roman', Times, serif;">
                                    @lang('I have Already Account')
                                    <a href="{{route('izoLogin')}}" style="font-size:18px;text-decoration:underline">SignIn</a>
                                </p>
                            </div>
                            <div class="clearfix"></div>
                            @if ($errors->has('g-recaptcha-response'))
                                <span class="text-danger">
                                    {{ $errors->first('g-recaptcha-response') }}
                                </span>
                            @endif
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
