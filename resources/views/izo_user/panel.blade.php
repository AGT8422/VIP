@extends('izo_user.layouts.app')

@section('title','panel')
@section('app_css')
 <style>

            .active_input_focus{
                outline:2px solid #ff0000 !important;
            }
            .list_of_lang{
                background-color:#f7f7f7;
                border-radius:10px;
                padding:10px ;
                position:absolute; 
                top:0px;
                left:50px;
                width:150px;
                z-index:3004;
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
                position: absolute;;
                /* box-shadow: 0px 0px 10px #f7f7f7; */
                text-align: center;
                left:10px; 
                top:0px;
                z-index:3000;
                line-height: 50px;
                /* background-color:white !important; */
                height:50px;
                width:50px;
                border-radius:100px !important;
                color:black;
            }
    .submit_panel{
        background-color: transparent;
        border: 0px solid black;
        padding: 0px;
    }
    .loading{
        position: fixed;
        left: 50%;
        font-size: xx-large;
        transform: translateX(-50%);
        top: 50%
    }
    @media (max-width: 600px) {
        /* **** */
        * {box-sizing:border-box}

        /* Slideshow container */
        .slideshow-container {
            position: relative;
            max-width: 90%;
            padding: 1px;
            height: 20px !important;
            top: -36px;
            left: 80px;
            /* border: 1px solid red; */
        }

        /* Hide the images by default */
        .mySlides {
            /* border: 1px solid red; */
            display: black;
            height: 10px;
            position: absolute;
        }
            
        /* **** */
        @page{
            margin: 100px 100px 10px 20px ;
        }
        header{
            top: 0px;
            background:linear-gradient(to right bottom ,#ffe9d2 10% , #ffe9d2 40% ,#fff3f3 100%);
            height: 200px;
            width: 100%;
            padding: 10px;
            position: fixed;
            justify-content: center
        }
        .contents{
            position: relative;
        }
        footer{
            bottom: 0px;
            background:#616161;
            height: 100%;
            width: 100%;
            padding: 10px;
            position: fixed;
        }
        body{
            box-sizing: border-box;
            background:linear-gradient(to top left ,rgb(255, 244, 235) 90% , rgb(255, 239, 223) 400% ,#cecece 100%);
            /* background-image:url("../../../uploads/IZO-D2.gif"); */
        }
        .left_form_first_login{
            border: 10px solid black;
            border-radius: 10px;
            padding: 10px;
            box-shadow:1px 1px 100px rgb(197, 197, 197);
            background: rgb(255, 255, 255);
        }
        .right_form_first_login{
            border: 10px solid black;
            border-radius: 10px;
            padding: 10px; 
            background: rgba(64, 64, 110, 0);
        }
        .mainTopbox{
            border-bottom: 10px solid #e86000;
            background-color: #303030;
            width: 100%;
            box-shadow: 1px 1px 10px black;
            height: 47px;
        }
        .mainBottombox{
            border-top: 5px solid;
            /* border-top-color: linear-gradient(to top left ,rgb(235, 249, 255) 10% , rgb(255, 239, 223) 40% ,#cecece 100%); */
            border-image: linear-gradient(to right,#e68000 100%,#eeddee00,#d80b7800 100%) 1 0 0 0;
            width: 100%;
            background-color: #ffffff;
            height: auto;
        }
        .mainbox{
            display: flex;
            padding: 10px 30px;
            
            width: 100%;
            background: rgba(0, 0, 0, 0);
            justify-content: center;
            border: 0px solid #f90000;
            height: auto;
            /* margin-top: 100px; */
            /* margin-bottom: 100px; */
            /* border-radius: 10px; */
        }
        .childbox{
            border: 0px solid rgb(249, 0, 0);
            width: 100%;
            margin-top: 0px;
            border-radius: 10px;
            padding: 10px 10px;
            height: auto;
            transform: translateY(0%);
            background: rgba(93, 237, 9, 0); 
            display: flex;
            justify-content: center;
        }
        .childbox .leftChild{
            position: absolute;
            padding: 10px;
            left: -100%;
            width: 100%;
            border: 0px solid black;
            display: flex;
            flex-direction: column;
            justify-content: start  ;
            justify-items: center;

        }
        .childbox .leftChild div{
            padding: 10px;
            margin: 1px 2px;
            width: 100%;
            text-align: left;
            font-weight: 700;
            color: #303030;
            border: 0px solid rgba(0, 0, 0, 0.603); 
            cursor: pointer;
            background-color: #f7f7f7;
        }
        .childbox .rightChild{
            padding: 10px;
            width: 90%;
            background-color: rgba(255, 255, 255, 0);
            border: 0px solid black;
            /* box-shadow: 3px 0px 10px rgb(168, 168, 168); */
            border-radius: 0px;
        }
        .active_div{
            
            color: white !important;
            border: 0px solid rgba(0, 0, 0, 0.603) !important; 
            background-color: #e86000 !important;
            box-shadow: 0px 1px 10px black !important;  
        } 
        .active_div h5{
            color: white !important;
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
        .izo-form-input:focus{
            border:1px solid #ec6808 !important;
            outline:1px solid #ec6808 !important;
        }
        .izo-form-input{
            width: 100%;
            border-radius: 10px;
            padding: 10px;
            margin: 10px auto ;
            font-size: 17px;
            border: 1px solid #3a3a3a33;
            /* outline: 1px solid #3a3a3a33 */
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
                display: none;
        }
        .right_form_first_login_top{
            border: 2px solid black;
            border-radius: 10px;
            padding: 10px; 
            background: rgba(64, 64, 110, 0);
            display: none;
        }
        ul , .header_nav{
            list-style: none;
            margin: 0px;
            width: 100%;
            padding-left: 0%;
            border:0px solid rgb(255, 255, 255);
        }
        ul li{
            padding :5px;

        }
        .header_nav{
            text-align: center;
        }
        .header_nav li{
            padding :10px;
            margin :10px;
            text-align: right;
            border: 0px solid black;
            display: inline-block;
            color: white;
            font-weight: 700;
            font-size: 18px;
            width: calc(90%/7);
        }
        .logo_text{
            margin-left:-10px !important;
            padding-top: 4px;
            padding-left: 1px !important;
            font-weight: 700;
            color: black;
            border-left :5px solid #e68000;      
        }
        .logo_text_header{
            padding-top: 0px;
            padding-left: 2px;
            font-weight: 700;
            color: white;
            margin-top:-0px !important;
            margin-left:-5px !important;
            font-size: 5px;
            border-left :1px solid #e68000;      
        }
        .title_color{
            color: #2c2c2c;
        }
        .BOX_DOMAIN{
            cursor: pointer;
            background-color: #e68000;
            border: 1px solid #e68000;
            border-radius: 10px;
            width: 80% ;
            padding: 10px;
            color: white;
            margin: 1% auto; 
            /* transform: translateX(-50%); */
            box-shadow: 1px 1px 10px rgb(122, 122, 122);
            color: #2c2c2c;
            font-size: 8px;
            overflow-x: hidden;
        } 
        .font_style{
            font-family:Georgia, 'Times New Roman', Times, serif;
        }
        .footerLogo{
            padding: 0PX !important;
        }
        .footerLogo img{
            height: 10px;
            width: 30px !important; 
            transform: translateX(20%);
        }
        .right-box{
            height: 100%;
            border-left:1px solid black;
        }
        .board{
            display: block;
            justify-content: center;
            /* height: 100%; */
        }
        .board>div{
            width: 100%;
            background-color: rgba(255, 197, 142, 0);
            height: 100%;
        }
        .board div.left_board{
                display: flex;
                /* justify-content: center; */
            /* border-radius:10px; */
            padding: 10px;
            background-color: #ffffff;
            width: 120%;
            text-align: center;
        }
        .board div.right_board{
            /* border-left:1px  solid #2c2c2c; */
            /* box-shadow: -10px 0px 10px #2c2c2c; */
            background-color: white;
            width: 120%;
            padding: 0px;
        }
        .board div.right_board img{
            /* font-size: 20px; */
            width:50%; 
            height: 50%;
        }
        .board div.right_board h1{
            font-size: 20px;
        }
        .board div.right_board h5{
            font-size: 10px;
        }
        /* slide */
        .image_1{
            position: relative;
            width: 90%;
            height: 200%;
            background-image: url('../../../uploads/image_1.jpg');
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
        }
        .image_2{
            position: relative;
            width: 90%;
            height: 200%;
            background-image: url('../../../uploads/image_1.jpg');
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
        }
        .image_3{
            position: relative;
            width: 90%;
            height: 200%;
            background-image: url('../../../uploads/image_3.jpg');
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
        }
        .image_4{
            position: relative;
            width: 90%;
            height: 200%;
            background-image: url('../../../uploads/image_4.jpg');
            background-position: center;
            background-size: cover  ;
            background-repeat: no-repeat;
        }
        /* platform */
        .plartform {
            padding:20px;
        }
        .custom-radio label{

            position:relative !important;
        }
        .custom-radio {
            display: flex;
            justify-content: space-around;
            /* flex-direction: column; */
            position:relative;
        }
        .custom-radio>div{
            /* background-color: red; */
            display:flex;
            position:relative !important;
            width: 100%;
        }
        .custom-radio>div label{
            /* display:flex; */
            position:absolute;
            width:100%; 
            padding-top: 20px;
            bottom:-100px;
        }
        .custom-radio>div input{
            /* width:100%; */
            font-size:10px;
            height:20px;
            background-color:red;
        }
        .custom-radio input:checked ~ .checkmark {
            background-color: #ff0000;
        }
        .custom-radio .checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 25px;
            width: 25px;
            background-color: #eee;
            border-radius: 50%;
        }
        .box_sections{
            cursor: pointer;
            position:relative;
            display: flex;
            width:100%;
            /* margin: 30px; */
            padding:30px;
            justify-content: space-between;
        }
        .box_sections:hover{
            background-color:#e86000;
            color:#fff;
        }
        .box_sections:hover h5{
            /* background-color:#e86000; */
            color:#fff;
        }
        .box_sections h5{
            padding:30px;
            left:100px;
            top:-10px;
            position:absolute;
        }
        .plartform label{
            position:absolute;
            top:-10px;
            left:8px;
            letter-spacing:1px;
            font-size:13px;
            color:#e68000;
        }
        .form_btn_primary{
            /* height: 100%; */
            box-shadow: 1px 1px 10px rgba(155, 155, 155, 0.226);
            background-color: #e68000 !important;
            border-radius: 5px;
            font-weight: 700;
            color: #f7f7f7 !important;
            padding: 10px 25px;
            width: 100px; 
        }
        .form_btn_close{
            box-shadow: 1px 1px 10px rgba(155, 155, 155, 0.226);
            /* height: 100%; */
            background-color: #f7f7f7;
            border-radius: 5px;
            color: #303030;
            padding: 10px 20px;
            width: 80px; 

        }
        .bottom_platform{
            display: flex;
            justify-items:center;
            justify-content: space-between;
            justify-items: flex-start;
            text-justify: auto;
            /* justify-content: space-between; */
            /* height: 8%; */
            padding:0px 100px; 
            /* width: 100% !; */
            /* background-color: #e68000; */
        }
    }
    @media (min-width: 600px) and  (max-width: 900px) {
        /* **** */
        * {box-sizing:border-box}

        /* Slideshow container */
        .slideshow-container { 
            position: relative;
            max-width: 76%;
            padding: 1px;
            height: 20px !important;
            top: -76px;
            left: 155px; 
        }

        /* Hide the images by default */
        .mySlides {
            /* border: 1px solid red; */
            display: black;
            height: 10px;
            position: absolute;
        }
            
        /* **** */
        @page{
            margin: 100px 100px 10px 20px ;
        }
        header{
            top: 0px;
            background:linear-gradient(to right bottom ,#ffe9d2 10% , #ffe9d2 40% ,#fff3f3 100%);
            height: 100px;
            width: 100%;
            padding: 10px;
            position: fixed;
            justify-content: center
        }
        .contents{
            position: relative;
        }
        footer{
            bottom: 0px;
            background:#616161;
            height: 100px;
            width: 100%;
            padding: 10px;
            position: fixed;
        }
        body{
            box-sizing: border-box;
            background:linear-gradient(to top left ,rgb(255, 244, 235) 90% , rgb(255, 239, 223) 100% ,#cecece 100%);
            /* background-image:url("../../../uploads/IZO-D2.gif"); */
        }
        .left_form_first_login{
            border: 10px solid black;
            border-radius: 10px;
            padding: 10px;
            box-shadow:1px 1px 100px rgb(197, 197, 197);
            background: rgb(255, 255, 255);
        }
        .right_form_first_login{
            border: 10px solid black;
            border-radius: 10px;
            padding: 10px; 
            background: rgba(64, 64, 110, 0);
        }
        /* page style */
        .mainTopbox{
            border-bottom: 10px solid #e86000;
            background-color: #303030;
            width: 100%;
            box-shadow: 1px 1px 10px black;
            height: 68px;
        }
        .mainBottombox{
            border-top: 5px solid;
            /* border-top-color: linear-gradient(to top left ,rgb(235, 249, 255) 10% , rgb(255, 239, 223) 40% ,#cecece 100%); */
            border-image: linear-gradient(to right,#e68000 10%,#eeddee00,#d80b7800 100%) 1 0 0 0;
            width: 100%;
            background-color: #ffffff;
            height: auto;
        }
        .mainbox{
            display: flex;
            padding: 10px 30px;
            height: auto;
            width: 100%;
            background: rgba(0, 0, 0, 0);
            justify-content: center;
            border: 0px solid #f90000;
            
            /* margin-top: 100px; */
            /* margin-bottom: 100px; */
            /* border-radius: 10px; */
        }
        .childbox{
            border: 0px solid rgb(249, 0, 0);
            width: 100%;
            margin-top: 0px;
            border-radius: 10px;
            padding: 10px 10px;
            height: auto;
            transform: translateY(0%);
            background: rgba(93, 237, 9, 0); 
            display: flex;
            justify-content: center;
        }
        .childbox .leftChild{
            position: absolute;
            padding: 10px;
            left: -100%;
            width: 100%;
            border: 0px solid black;
            display: flex;
            flex-direction: column;
            justify-content: start  ;
            justify-items: center;

        }
        .childbox .leftChild div{
            padding: 10px;
            margin: 1px 2px;
            width: 100%;
            text-align: left;
            font-weight: 700;
            color: #303030;
            border: 0px solid rgba(0, 0, 0, 0.603); 
            cursor: pointer;
            background-color: #f7f7f7;
        }
        .childbox .rightChild{
            padding: 10px;
            width: 90%;
            background-color: rgba(255, 255, 255, 0);
            border: 0px solid black;
            /* box-shadow: 3px 0px 10px rgb(168, 168, 168); */
            border-radius: 0px;
        }
        .active_div{
            
            color: white !important;
            border: 0px solid rgba(0, 0, 0, 0.603) !important; 
            background-color: #e86000 !important;
            box-shadow: 0px 1px 10px black !important;  
        }
        .active_div h5{
            color: white !important;
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
        .izo-form-input:focus{
            border:1px solid #ec6808 !important;
            outline:1px solid #ec6808 !important;
        }
        .izo-form-input{
            width: 100%;
            border-radius: 10px;
            padding: 10px;
            margin: 10px auto ;
            font-size: 17px;
            border: 1px solid #3a3a3a33;
            /* outline: 1px solid #3a3a3a33 */
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
                display: none;
        }
        .right_form_first_login_top{
            border: 2px solid black;
            border-radius: 10px;
            padding: 10px; 
            background: rgba(64, 64, 110, 0);
            display: none;
        }
        ul , .header_nav{
            list-style: none;
            margin: 0px;
            width: 100%;
            padding-left: 0%;
            border:0px solid rgb(255, 255, 255);
        }
        ul li{
            padding :5px;

        }
        .header_nav{
            text-align: center;
        }
        .header_nav li{
            padding :10px;
            margin :10px;
            text-align: right;
            border: 0px solid black;
            display: inline-block;
            color: white;
            font-weight: 700;
            font-size: 18px;
            width: calc(90%/7);
        }
        /* header logo  */
        .logo_text{
            margin-left:10px;
            padding-top: 4px;
            padding-left: 10px;
            font-weight: 700;
            color: black;
            border-left :5px solid #e68000;      
        }
        .logo_text_header{
            padding-top: 0px;
            padding-left: 5px;
            font-weight: 700;
            font-size: 10px;
            color: white;
            margin-top:3px;
            margin-left:-5px;
            border-left :2px solid #e68000;      
        }
        .title_color{
            color: #2c2c2c;
        }
        .BOX_DOMAIN{
            cursor: pointer;
            background-color: #e68000;
            border: 1px solid #e68000;
            border-radius: 10px;
            width: 80% ;
            padding: 10px;
            color: white;
            margin: 1% auto; 
            /* transform: translateX(-50%); */
            box-shadow: 1px 1px 10px rgb(122, 122, 122);
            color: #2c2c2c;
            font-size: 16px;
            overflow-x: hidden;
        } 
        .font_style{
            font-family:Georgia, 'Times New Roman', Times, serif;
        }
        .footerLogo img{
            transform: translateX(40%);
        }
        .footerLogo2 img{
            transform: translateX(40%);
        }
        .right-box{
            height: 100%;
            border-left:1px solid black;
        }
        .board{
            display: block;
            justify-content: center;
            /* height: 100%; */
        }
        .board>div{
            width: 100%;
            background-color: rgba(255, 197, 142, 0);
            height: 100%;
        }
        .board div.left_board{
                display: flex;
                /* justify-content: center; */
            /* border-top-left-radius:10px; */
            border-top-right-radius:10px;
            padding: 10px;
    background-color: #ffffff75;
            width: 110%;
        }
        .board div.right_board{
            /* border-left:1px  solid #2c2c2c; */
            /* box-shadow: -10px 0px 10px #2c2c2c; */
            background-color: white;
            width: 110%;
        }
        .board div.right_board img{
            width: 30%;
            height: 30%;
        }
        .board div.right_board h1{
            font-size: 26px;
        }
        .board div.right_board h5{
            font-size: 13px;
        }
        /* slide */
        .image_1{ 
            position: relative;
            width: 100%;
            height: 380%;
            background-image: url('../../../uploads/image_1.jpg');
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
        }
        .image_2{ 
            position: relative;
            width: 100%;
            height: 380%;
            background-image: url('../../../uploads/image_1.jpg');
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
        }
        .image_3{ 
            position: relative;
            width: 100%;
            height: 380%;
            background-image: url('../../../uploads/image_3.jpg');
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
        }
        .image_4{ 
            position: relative;
            width: 100%;
            height: 380%;
            background-image: url('../../../uploads/image_4.jpg');
            background-position: center;
            background-size: cover  ;
            background-repeat: no-repeat;
        }
        /* platform */
        .plartform {
            padding:20px;
        }
        .custom-radio label{

            position:relative !important;
        }
        .custom-radio {
            display: flex;
            justify-content: space-around;
            /* flex-direction: column; */
            position:relative;
        }
        .custom-radio>div{
            /* background-color: red; */
            display:flex;
            position:relative !important;
            width: 100%;
        }
        .custom-radio>div label{
            /* display:flex; */
            position:absolute;
            width:100%; 
            padding-top: 20px;
            bottom:-100px;
        }
        .custom-radio>div input{
            /* width:100%; */
            font-size:10px;
            height:20px;
            background-color:red;
        }
        .custom-radio input:checked ~ .checkmark {
            background-color: #ff0000;
        }
        .custom-radio .checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 25px;
            width: 25px;
            background-color: #eee;
            border-radius: 50%;
        }
        .box_sections{
            cursor: pointer;
            position:relative;
            display: flex;
            width:100%;
            /* margin: 30px; */
            padding:30px;
            justify-content: space-between;
        }
        .box_sections:hover{
            background-color:#e86000;
            color:#fff;
        }
        .box_sections:hover h5{
            /* background-color:#e86000; */
            color:#fff;
        }
        .box_sections h5{
            padding:30px;
            left:100px;
            top:-10px;
            position:absolute;
        }
        .plartform label{
            position:absolute;
            top:-10px;
            left:8px;
            letter-spacing:1px;
            font-size:13px;
            color:#e68000;
        }
        .form_btn_primary{
            /* height: 100%; */
            box-shadow: 1px 1px 10px rgba(155, 155, 155, 0.226);
            background-color: #e68000 !important;
            border-radius: 5px;
            font-weight: 700;
            color: #f7f7f7 !important;
            padding: 10px 25px;
            width: 100px; 
        }
        .form_btn_close{
            box-shadow: 1px 1px 10px rgba(155, 155, 155, 0.226);
            /* height: 100%; */
            background-color: #f7f7f7;
            border-radius: 5px;
            color: #303030;
            padding: 10px 20px;
            width: 80px; 

        }
        .bottom_platform{
            display: flex;
            justify-items:center;
            justify-content: space-between;
            justify-items: flex-start;
            text-justify: auto;
            /* justify-content: space-between; */
            /* height: 8%; */
            padding:0px 100px; 
            /* width: 100% !; */
            /* background-color: #e68000; */
        }
        .left_board form{
            width:100%;
        }
        .next:hover,
        .finish:hover{
            color: #fff !important;
        }
        .prev:hover{
            color: #303030;
        }
        .platforms{
            
            width: 100% !important;
            height: 90%;
            /* position: relative; */
            /* background-color: #f90000; */
        }
        .platforms>div{
            width: 100%;
            /* height: 90%; */
            /* position: relative; */
            /* background-color: #ffffff; */
        }
    }
    @media  (min-width:1700px)  {
                    /* **** */
              * {box-sizing:border-box}
                /* Slideshow container */
                .slideshow-container {
                    position: relative;
                    max-width: 100%;
                    padding: 1px;
                    height: 10px !important;
                    /* top: -20px; */
                    /* border: 1px solid red; */
                }

                /* Hide the images by default */
                .mySlides {
                    /* border: 1px solid red; */
                    display: black;
                    height: 10px;
                    position: absolute;
                }
                    
                /* **** */
                @page{
                    margin: 100px 100px 10px 20px ;
                }
                header{
                    top: 0px;
                    background:linear-gradient(to right bottom ,#ffe9d2 10% , #ffe9d2 40% ,#fff3f3 100%);
                    height: 100px;
                    width: 100%;
                    padding: 10px;
                    position: fixed;
                    justify-content: center
                }
                .contents{
                    position: relative;
                }
                footer{
                    bottom: 0px;
                    background:#616161;
                    height: 100px;
                    width: 100%;
                    padding: 10px;
                    position: fixed;
                }
                body{
                    box-sizing: border-box;
                    background:linear-gradient(to top left ,rgb(235, 249, 255) 10% , rgb(255, 239, 223) 40% ,#cecece 100%);
                    /* background-image:url("../../../uploads/IZO-D2.gif") */
                }
                .left_form_first_login{
                    border: 10px solid black;
                    border-radius: 10px;
                    padding: 10px;
                    box-shadow:1px 1px 100px rgb(197, 197, 197);
                    background: rgb(255, 255, 255);
                }
                .right_form_first_login{
                    border: 10px solid black;
                    border-radius: 10px;
                    padding: 10px; 
                    background: rgba(64, 64, 110, 0);
                }
                .mainTopbox{
                    border-bottom: 10px solid #e86000;
                    background-color: #303030;
                    width: 100%;
                    box-shadow: 1px 1px 10px black;
                    height: 92px;
                }
                .mainBottombox{
                    border-top: 5px solid;
                    /* border-top-color: linear-gradient(to top left ,rgb(235, 249, 255) 10% , rgb(255, 239, 223) 40% ,#cecece 100%); */
                    border-image: linear-gradient(to right,#e68000 10%,#eeddee00,#d80b7800 100%) 1 0 0 0;
                    width: 100%;
                    background-color: #ffffff;
                    height: auto;
                }
                .mainbox{
                    display: flex;
                    padding: 10px 30px;
                    height: 60%;
                    width: 100%;
                    background: rgba(0, 0, 0, 0);
                    justify-content: center;
                    border: 0px solid #f90000;
                    
                    /* margin-top: 100px; */
                    /* margin-bottom: 100px; */
                    /* border-radius: 10px; */
                }
                .childbox{
                    border: 0px solid rgb(249, 0, 0);
                    width: 100%;
                    margin-top: 0px;
                    border-radius: 10px;
                    padding: 10px 10px;
                    height: auto;
                    transform: translateY(0%);
                    background: rgba(93, 237, 9, 0); 
                    display: flex;
                    justify-content: center;
                }
                .childbox .leftChild{
                    padding: 10px;
                    width: 10%;
                    border: 0px solid black;
                    display: flex;
                    flex-direction: column;
                    justify-content: start  ;
                    justify-items: center;

                }
                .childbox .leftChild div{
                    padding: 10px;
                    margin: 1px 2px;
                    width: 100%;
                    text-align: left;
                    font-weight: 700;
                    color: #303030;
                    border: 0px solid rgba(0, 0, 0, 0.603); 
                    cursor: pointer;
                    background-color: #f7f7f7;
                }
                .childbox .rightChild{
                    padding: 10px;
                    width: 90%;
                    background-color: rgba(255, 255, 255, 0);
                    border: 0px solid black;
                    /* box-shadow: 3px 0px 10px rgb(168, 168, 168); */
                    border-radius: 0px;
                }
                .active_div{
                    
                    color: white !important;
                    border: 0px solid rgba(0, 0, 0, 0.603) !important; 
                    background-color: #e86000 !important;
                    box-shadow: 0px 1px 10px black !important;  
                }
                .active_div h5{
                    color: white !important;
                }
                /* .navbar{
                    padding: 10px;
                    text-align: center;
                    border: 10px solid rgb(196, 9, 9);
                }
                .navbar-list{
                    border: 10px solid rgb(2, 253, 178);
                    margin:0px;
                    width: 80%;
                    transform: translateX(12%);
                    padding: 0px;
                    text-align: center;
                }
                .navbar-list li{
                    display: inline-block;
                    width:calc(78%/4);
                    margin:1px;
                    background: red;
                } */
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
                .izo-form-input:focus{
                    border:1px solid #ec6808 !important;
                    outline:1px solid #ec6808 !important;
                }
                .izo-form-input{
                    width: 100%;
                    border-radius: 10px;
                    padding: 10px;
                    margin: 10px auto ;
                    font-size: 17px;
                    border: 1px solid #3a3a3a33;
                    /* outline: 1px solid #3a3a3a33 */
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
                        display: none;
                }
                .right_form_first_login_top{
                    border: 2px solid black;
                    border-radius: 10px;
                    padding: 10px; 
                    background: rgba(64, 64, 110, 0);
                    display: none;
                }
                ul , .header_nav{
                    list-style: none;
                    margin: 0px;
                    width: 100%;
                    padding-left: 0%;
                    border:0px solid rgb(255, 255, 255);
                }
                ul li{
                    padding :5px;

                }
                .header_nav{
                    text-align: center;
                }
                .header_nav li{
                    padding :10px;
                    margin :10px;
                    text-align: right;
                    border: 0px solid black;
                    display: inline-block;
                    color: white;
                    font-weight: 700;
                    font-size: 18px;
                    width: calc(90%/7);
                }
                .logo_text{
                    margin-left:10px;
                    padding-top: 4px;
                    padding-left: 10px;
                    font-weight: 700;
                    color: black;
                    border-left :5px solid #e68000;      
                }
                .logo_text_header{
                    padding-top: 0px;
                    padding-left: 5px;
                    font-weight: 700;
                    font-size: 20px;
                    color: white;
                    /* margin-left:5px; */
                    border-left :5px solid #e68000;      
                }
                .title_color{
                    color: #2c2c2c;
                }
                .BOX_DOMAIN{
                    cursor: pointer;
                    background-color: #e68000;
                    border: 1px solid #e68000;
                    border-radius: 10px;
                    width: 80% ;
                    padding: 10px;
                    color: white;
                    margin: 1% auto; 
                    /* transform: translateX(-50%); */
                    box-shadow: 1px 1px 10px rgb(122, 122, 122);
                    color: #2c2c2c;
                    font-size: 20px;
                } 
                .font_style{
                    font-family:Georgia, 'Times New Roman', Times, serif;
                }
                .footerLogo img{
                    height: 20px;
                    width: 60px;
                    transform: translateX(100%);
                }
                .right-box{
                    height: 100%;
                    border-left:1px solid black;
                }
                .board{
                    display: flex;
                    justify-content: center;
                    height: 100%;
                }
                .board>div{
                width: 100%;
                background-color: rgba(255, 197, 142, 0);
                height: 100%;
                }
                .board div.left_board{
                display: flex;
                /* justify-content: center; */
                    /* border-radius:10px; */
                    padding: 10px;
                    background-color: #ffffff;
                    width: 110%;
                }
                .board div.right_board{
                    /* border-left:1px  solid #2c2c2c; */
                    /* box-shadow: -10px 0px 10px #2c2c2c; */
                    background-color: white;
                    width: 90%;
                }
                .image_1{ 
                    position: relative;
                    width: 100%;
                    height: 80px;
                    background-image: url('../../../uploads/image_1.jpg');
                    background-position: center;
                    background-size: cover;
                    background-repeat: no-repeat;
                }
                .image_2{ 
                    position: relative;
                    width: 100%;
                    height: 80px;
                    background-image: url('../../../uploads/image_1.jpg');
                    background-position: center;
                    background-size: cover;
                    background-repeat: no-repeat;
                }
                .image_3{ 
                    position: relative;
                    width: 100%;
                    height: 80px;
                    background-image: url('../../../uploads/image_3.jpg');
                    background-position: center;
                    background-size: cover;
                    background-repeat: no-repeat;
                }
                .image_4{ 
                    position: relative;
                    width: 100%;
                    height: 80px;
                    background-image: url('../../../uploads/image_4.jpg');
                    background-position: center;
                    background-size: cover  ;
                    background-repeat: no-repeat;
                }
        /* platform */
        .plartform {
            padding:20px;
        }
        .custom-radio label{

            position:relative !important;
        }
        .custom-radio {
            display: flex;
            justify-content: space-around;
            /* flex-direction: column; */
            position:relative;
        }
        .custom-radio>div{
            /* background-color: red; */
            display:flex;
            position:relative !important;
            width: 100%;
        }
        .custom-radio>div label{
            /* display:flex; */
            position:absolute;
            width:100%; 
            padding-top: 20px;
            bottom:-100px;
        }
        .custom-radio>div input{
            /* width:100%; */
            font-size:10px;
            height:20px;
            background-color:red;
        }
        .custom-radio input:checked ~ .checkmark {
            background-color: #ff0000;
        }
        .custom-radio .checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 25px;
            width: 25px;
            background-color: #eee;
            border-radius: 50%;
        }
        .box_sections{
            cursor: pointer;
            position:relative;
            display: flex;
            width:100%;
            /* margin: 30px; */
            padding:30px;
            justify-content: space-between;
        }
        .box_sections:hover{
            background-color:#e86000;
            color:#fff;
        }
        .box_sections:hover h5{
            /* background-color:#e86000; */
            color:#fff;
        }
        .box_sections h5{
            padding:30px;
            left:100px;
            top:-10px;
            position:absolute;
        }
        .plartform label{
            position:absolute;
            top:-10px;
            left:8px;
            letter-spacing:1px;
            font-size:13px;
            color:#e68000;
        }
        .form_btn_primary{
            /* height: 100%; */
            box-shadow: 1px 1px 10px rgba(155, 155, 155, 0.226);
            background-color: #e68000 !important;
            border-radius: 5px;
            font-weight: 700;
            color: #f7f7f7 !important;
            padding: 10px 25px;
            width: 100px; 
        }
        .form_btn_close{
            box-shadow: 1px 1px 10px rgba(155, 155, 155, 0.226);
            /* height: 100%; */
            background-color: #f7f7f7;
            border-radius: 5px;
            color: #303030;
            padding: 10px 20px;
            width: 80px; 

        }
        .bottom_platform{
            display: flex;
            justify-items:center;
            justify-content: space-between;
            justify-items: flex-start;
            text-justify: auto;
            /* justify-content: space-between; */
            /* height: 8%; */
            padding:0px 100px; 
            /* width: 100% !; */
            /* background-color: #e68000; */
        }
        .left_board form{
            width:100%;
        }
        .next:hover,
        .finish:hover{
            color: #fff;
        }
        .prev:hover{
            color: #303030;
        }
        .platforms{
            
            width: 100% !important;
            height: 90%;
            /* position: relative; */
            /* background-color: #f90000; */
        }
        .platforms>div{
            width: 100%;
            /* height: 90%; */
            /* position: relative; */
            /* background-color: #ffffff; */
        }
    }
    @media  (min-width:1400px) and  (max-width: 1700px) {
                    /* **** */
              * {box-sizing:border-box}

                /* Slideshow container */
                .slideshow-container {
                    position: relative;
                    max-width: 100%;
                    padding: 1px;
                    height: 10px !important;
                    /* top: -20px; */
                    /* border: 1px solid red; */
                }

                /* Hide the images by default */
                .mySlides {
                    /* border: 1px solid red; */
                    display: black;
                    height: 10px;
                    position: absolute;
                }
                    
                /* **** */
                @page{
                    margin: 100px 100px 10px 20px ;
                }
                header{
                    top: 0px;
                    background:linear-gradient(to right bottom ,#ffe9d2 10% , #ffe9d2 40% ,#fff3f3 100%);
                    height: 100px;
                    width: 100%;
                    padding: 10px;
                    position: fixed;
                    justify-content: center
                }
                .contents{
                    position: relative;
                }
                footer{
                    bottom: 0px;
                    background:#616161;
                    height: 100px;
                    width: 100%;
                    padding: 10px;
                    position: fixed;
                }
                body{
                    box-sizing: border-box;
                    background:linear-gradient(to top left ,rgb(235, 249, 255) 10% , rgb(255, 239, 223) 40% ,#cecece 100%);
                    /* background-image:url("../../../uploads/IZO-D2.gif") */
                }
                .left_form_first_login{
                    border: 10px solid black;
                    border-radius: 10px;
                    padding: 10px;
                    box-shadow:1px 1px 100px rgb(197, 197, 197);
                    background: rgb(255, 255, 255);
                }
                .right_form_first_login{
                    border: 10px solid black;
                    border-radius: 10px;
                    padding: 10px; 
                    background: rgba(64, 64, 110, 0);
                }
                .mainTopbox{
                    border-bottom: 10px solid #e86000;
                    background-color: #303030;
                    width: 100%;
                    box-shadow: 1px 1px 10px black;
                    height: 92px;
                }
                .mainBottombox{
                    border-top: 5px solid;
                    /* border-top-color: linear-gradient(to top left ,rgb(235, 249, 255) 10% , rgb(255, 239, 223) 40% ,#cecece 100%); */
                    border-image: linear-gradient(to right,#e68000 10%,#eeddee00,#d80b7800 100%) 1 0 0 0;
                    width: 100%;
                    background-color: #ffffff;
                    height: auto;
                }
                .mainbox{
                    display: flex;
                    padding: 10px 30px;
                    height: 60%;
                    width: 100%;
                    background: rgba(0, 0, 0, 0);
                    justify-content: center;
                    border: 0px solid #f90000;
                    
                    /* margin-top: 100px; */
                    /* margin-bottom: 100px; */
                    /* border-radius: 10px; */
                }
                .childbox{
                    border: 0px solid rgb(249, 0, 0);
                    width: 100%;
                    margin-top: 0px;
                    border-radius: 10px;
                    padding: 10px 10px;
                    height: auto;
                    transform: translateY(0%);
                    background: rgba(93, 237, 9, 0); 
                    display: flex;
                    justify-content: center;
                }
                .childbox .leftChild{
                    padding: 10px;
                    width: 10%;
                    border: 0px solid black;
                    display: flex;
                    flex-direction: column;
                    justify-content: start  ;
                    justify-items: center;

                }
                .childbox .leftChild div{
                    padding: 10px;
                    margin: 1px 2px;
                    width: 100%;
                    text-align: left;
                    font-weight: 700;
                    color: #303030;
                    border: 0px solid rgba(0, 0, 0, 0.603); 
                    cursor: pointer;
                    background-color: #f7f7f7;
                }
                .childbox .rightChild{
                    padding: 10px;
                    width: 90%;
                    background-color: rgba(255, 255, 255, 0);
                    border: 0px solid black;
                    /* box-shadow: 3px 0px 10px rgb(168, 168, 168); */
                    border-radius: 0px;
                }
                .active_div{
                    
                    color: white !important;
                    border: 0px solid rgba(0, 0, 0, 0.603) !important; 
                    background-color: #e86000 !important;
                    box-shadow: 0px 1px 10px black !important;  
                }
                .active_div h5{
                    color: white !important;
                }
                /* .navbar{
                    padding: 10px;
                    text-align: center;
                    border: 10px solid rgb(196, 9, 9);
                }
                .navbar-list{
                    border: 10px solid rgb(2, 253, 178);
                    margin:0px;
                    width: 80%;
                    transform: translateX(12%);
                    padding: 0px;
                    text-align: center;
                }
                .navbar-list li{
                    display: inline-block;
                    width:calc(78%/4);
                    margin:1px;
                    background: red;
                } */
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
                .izo-form-input:focus{
                    border:1px solid #ec6808 !important;
                    outline:1px solid #ec6808 !important;
                }
                .izo-form-input{
                    width: 100%;
                    border-radius: 10px;
                    padding: 10px;
                    margin: 10px auto ;
                    font-size: 17px;
                    border: 1px solid #3a3a3a33;
                    /* outline: 1px solid #3a3a3a33 */
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
                        display: none;
                }
                .right_form_first_login_top{
                    border: 2px solid black;
                    border-radius: 10px;
                    padding: 10px; 
                    background: rgba(64, 64, 110, 0);
                    display: none;
                }
                ul , .header_nav{
                    list-style: none;
                    margin: 0px;
                    width: 100%;
                    padding-left: 0%;
                    border:0px solid rgb(255, 255, 255);
                }
                ul li{
                    padding :5px;

                }
                .header_nav{
                    text-align: center;
                }
                .header_nav li{
                    padding :10px;
                    margin :10px;
                    text-align: right;
                    border: 0px solid black;
                    display: inline-block;
                    color: white;
                    font-weight: 700;
                    font-size: 18px;
                    width: calc(90%/7);
                }
                .logo_text{
                    margin-left:10px;
                    padding-top: 4px;
                    padding-left: 10px;
                    font-weight: 700;
                    color: black;
                    border-left :5px solid #e68000;      
                }
                .logo_text_header{
                    padding-top: 0px;
                    padding-left: 5px;
                    font-weight: 700;
                    font-size: 18px;
                    color: white;
                    /* margin-left:5px; */
                    border-left :5px solid #e68000;      
                }
                .title_color{
                    color: #2c2c2c;
                }
                .BOX_DOMAIN{
                    cursor: pointer;
                    background-color: #e68000;
                    border: 1px solid #e68000;
                    border-radius: 10px;
                    width: 80% ;
                    padding: 10px;
                    color: white;
                    margin: 1% auto; 
                    /* transform: translateX(-50%); */
                    box-shadow: 1px 1px 10px rgb(122, 122, 122);
                    color: #2c2c2c;
                    font-size: 20px;
                } 
                .font_style{
                    font-family:Georgia, 'Times New Roman', Times, serif;
                }
                .footerLogo img{
                    height: 20px;
                    width: 60px;
                    transform: translateX(100%);
                }
                .right-box{
                    height: 100%;
                    border-left:1px solid black;
                }
                .board{
                    display: flex;
                    justify-content: center;
                    height: 100%;
                }
                .board>div{
                width: 100%;
                background-color: rgba(255, 197, 142, 0);
                height: 100%;
                }
                .board div.left_board{
                display: flex;
                /* justify-content: center; */
                    /* border-radius:10px; */
                    padding: 10px;
                    background-color: #ffffff;
                    width: 110%;
                }
                .board div.right_board{
                    /* border-left:1px  solid #2c2c2c; */
                    /* box-shadow: -10px 0px 10px #2c2c2c; */
                    background-color: white;
                    width: 90%;
                }
                .image_1{ 
                    position: relative;
                    width: 100%;
                    height: 80px;
                    background-image: url('../../../uploads/image_1.jpg');
                    background-position: center;
                    background-size: cover;
                    background-repeat: no-repeat;
                }
                .image_2{ 
                    position: relative;
                    width: 100%;
                    height: 80px;
                    background-image: url('../../../uploads/image_1.jpg');
                    background-position: center;
                    background-size: cover;
                    background-repeat: no-repeat;
                }
                .image_3{ 
                    position: relative;
                    width: 100%;
                    height: 80px;
                    background-image: url('../../../uploads/image_3.jpg');
                    background-position: center;
                    background-size: cover;
                    background-repeat: no-repeat;
                }
                .image_4{ 
                    position: relative;
                    width: 100%;
                    height: 80px;
                    background-image: url('../../../uploads/image_4.jpg');
                    background-position: center;
                    background-size: cover  ;
                    background-repeat: no-repeat;
                }
        /* platform */
        .plartform {
            padding:20px;
        }
        .custom-radio label{

            position:relative !important;
        }
        .custom-radio {
            display: flex;
            justify-content: space-around;
            /* flex-direction: column; */
            position:relative;
        }
        .custom-radio>div{
            /* background-color: red; */
            display:flex;
            position:relative !important;
            width: 100%;
        }
        .custom-radio>div label{
            /* display:flex; */
            position:absolute;
            width:100%; 
            padding-top: 20px;
            bottom:-100px;
        }
        .custom-radio>div input{
            /* width:100%; */
            font-size:10px;
            height:20px;
            background-color:red;
        }
        .custom-radio input:checked ~ .checkmark {
            background-color: #ff0000;
        }
        .custom-radio .checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 25px;
            width: 25px;
            background-color: #eee;
            border-radius: 50%;
        }
        .box_sections{
            cursor: pointer;
            position:relative;
            display: flex;
            width:100%;
            /* margin: 30px; */
            padding:30px;
            justify-content: space-between;
        }
        .box_sections:hover{
            background-color:#e86000;
            color:#fff;
        }
        .box_sections:hover h5{
            /* background-color:#e86000; */
            color:#fff;
        }
        .box_sections h5{
            padding:30px;
            left:100px;
            top:-10px;
            position:absolute;
        }
        .plartform label{
            position:absolute;
            top:-10px;
            left:8px;
            letter-spacing:1px;
            font-size:13px;
            color:#e68000;
        }
        .form_btn_primary{
            /* height: 100%; */
            box-shadow: 1px 1px 10px rgba(155, 155, 155, 0.226);
            background-color: #e68000 !important;
            border-radius: 5px;
            font-weight: 700;
            color: #f7f7f7 !important;
            padding: 10px 25px;
            width: 100px; 
        }
        .form_btn_close{
            box-shadow: 1px 1px 10px rgba(155, 155, 155, 0.226);
            /* height: 100%; */
            background-color: #f7f7f7;
            border-radius: 5px;
            color: #303030;
            padding: 10px 20px;
            width: 80px; 

        }
        .bottom_platform{
            display: flex;
            justify-items:center;
            justify-content: space-between;
            justify-items: flex-start;
            text-justify: auto;
            /* justify-content: space-between; */
            /* height: 8%; */
            padding:0px 100px; 
            /* width: 100% !; */
            /* background-color: #e68000; */
        }
        .left_board form{
            width:100%;
        }
        .next:hover,
        .finish:hover{
            color: #fff;
        }
        .prev:hover{
            color: #303030;
        }
        .platforms{
            
            width: 100% !important;
            height: 90%;
            /* position: relative; */
            /* background-color: #f90000; */
        }
        .platforms>div{
            width: 100%;
            /* height: 90%; */
            /* position: relative; */
            /* background-color: #ffffff; */
        }
    }
    @media (min-width: 1024px) and (max-width:1400px){
            /* **** */
            * {box-sizing:border-box}

            /* Slideshow container */
            .slideshow-container {
                position: relative;
                max-width: 100%;
                padding: 1px;
                height: 10px !important;
                /* top: -20px; */
                /* border: 1px solid red; */
            }

            /* Hide the images by default */
            .mySlides {
                /* border: 1px solid red; */
                display: black;
                height: 10px;
                position: absolute;
            }
                
            /* **** */
            @page{
                margin: 100px 100px 10px 20px ;
            }
            header{
                top: 0px;
                background:linear-gradient(to right bottom ,#ffe9d2 10% , #ffe9d2 40% ,#fff3f3 100%);
                height: 100px;
                width: 100%;
                padding: 10px;
                position: fixed;
                justify-content: center
            }
            .contents{
                position: relative;
            }
            footer{
                bottom: 0px;
                background:#616161;
                height: 100px;
                width: 100%;
                padding: 10px;
                position: fixed;
            }
            body{
                box-sizing: border-box;
                background:linear-gradient(to top left ,rgb(235, 249, 255) 10% , rgb(255, 239, 223) 40% ,#cecece 100%);
                /* background-image:url("../../../uploads/IZO-D2.gif") */
            }
            .left_form_first_login{
                border: 10px solid black;
                border-radius: 10px;
                padding: 10px;
                box-shadow:1px 1px 100px rgb(197, 197, 197);
                background: rgb(255, 255, 255);
            }
            .right_form_first_login{
                border: 10px solid black;
                border-radius: 10px;
                padding: 10px; 
                background: rgba(64, 64, 110, 0);
            }
            .mainTopbox{
                border-bottom: 10px solid #e86000;
                background-color: #303030;
                width: 100%;
                box-shadow: 1px 1px 10px black;
                height: 100px;
            }
            .mainBottombox{
                border-top: 5px solid;
                /* border-top-color: linear-gradient(to top left ,rgb(235, 249, 255) 10% , rgb(255, 239, 223) 40% ,#cecece 100%); */
                border-image: linear-gradient(to right,#e68000 10%,#eeddee00,#d80b7800 100%) 1 0 0 0;
                width: 100%;
                background-color: #ffffff;
                height: auto;
            }
            .mainbox{
                display: flex;
                padding: 10px 30px;
                height:auto;
                width: 100%;
                background: rgba(0, 0, 0, 0);
                justify-content: center;
                border: 0px solid #f90000;
                
                /* margin-top: 100px; */
                /* margin-bottom: 100px; */
                /* border-radius: 10px; */
            }
            .childbox{
                border: 0px solid rgb(249, 0, 0);
                width: 100%;
                margin-top: 0px;
                border-radius: 10px;
                padding: 10px 10px;
                height: auto;
                transform: translateY(0%);
                background: rgba(93, 237, 9, 0); 
                display: flex;
                justify-content: center;
            }
            .childbox .leftChild{
                padding: 10px;
                width: 15%;
                border: 0px solid black;
                display: flex;
                flex-direction: column;
                justify-content: start  ;
                justify-items: center;

            }
            .childbox .leftChild div{
                padding: 10px;
                margin: 1px 2px;
                width: 100%;
                text-align: left;
                font-weight: 700;
                color: #303030;
                border: 0px solid rgba(0, 0, 0, 0.603); 
                cursor: pointer;
                background-color: #f7f7f7;
            }
            .childbox .rightChild{
                padding: 10px;
                width: 85%;
                background-color: rgba(255, 255, 255, 0);
                border: 0px solid black;
                /* box-shadow: 3px 0px 10px rgb(168, 168, 168); */
                border-radius: 0px;
            }
            .active_div{
                
                color: white !important;
                border: 0px solid rgba(0, 0, 0, 0.603) !important; 
                background-color: #e86000 !important;
                box-shadow: 0px 1px 10px black !important;  
            }
            .active_div h5{
                color: white !important;
            }
            /* .navbar{
                padding: 10px;
                text-align: center;
                border: 10px solid rgb(196, 9, 9);
            }
            .navbar-list{
                border: 10px solid rgb(2, 253, 178);
                margin:0px;
                width: 80%;
                transform: translateX(12%);
                padding: 0px;
                text-align: center;
            }
            .navbar-list li{
                display: inline-block;
                width:calc(78%/4);
                margin:1px;
                background: red;
            } */
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
            .izo-form-input:focus{
                border:1px solid #ec6808 !important;
                outline:1px solid #ec6808 !important;
            }
            .izo-form-input{
                width: 100%;
                border-radius: 10px;
                padding: 10px;
                margin: 10px auto ;
                font-size: 17px;
                border: 1px solid #3a3a3a33;
                /* outline: 1px solid #3a3a3a33 */
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
                    display: none;
            }
            .right_form_first_login_top{
                border: 2px solid black;
                border-radius: 10px;
                padding: 10px; 
                background: rgba(64, 64, 110, 0);
                display: none;
            }
            ul , .header_nav{
                list-style: none;
                margin: 0px;
                width: 100%;
                padding-left: 0%;
                border:0px solid rgb(255, 255, 255);
            }
            ul li{
                padding :5px;

            }
            .header_nav{
                text-align: center;
            }
            .header_nav li{
                padding :10px;
                margin :10px;
                text-align: right;
                border: 0px solid black;
                display: inline-block;
                color: white;
                font-weight: 700;
                font-size: 18px;
                width: calc(90%/7);
            }
            .logo_text{
                margin-left:00px;
                padding-top: 0px;
                padding-left: 5px;
                font-weight: 700;
                color: black;
                font-size: 20px;
                border-left :5px solid #e68000;      
            }
            .logo_text_header{
                padding-top: 0px;
                padding-left: 5px;
                font-weight: 700;
                color: white;
                font-size: 12.5px !important;
                margin-left:-10px;
                border-left :3px solid #e68000;      
            }
            .title_color{
                color: #2c2c2c;
            }
            .BOX_DOMAIN{
                cursor: pointer;
                background-color: #e68000;
                border: 1px solid #e68000;
                border-radius: 10px;
                width: 80% ;
                padding: 10px;
                color: white;
                margin: 1% auto; 
                /* transform: translateX(-50%); */
                box-shadow: 1px 1px 10px rgb(122, 122, 122);
                color: #2c2c2c;
                font-size: 13px;
                overflow: hidden;
            } 
            .font_style{
                font-family:Georgia, 'Times New Roman', Times, serif;
            }
            .footerLogo img{
                height: 15px;
                width: 45px;
                transform: translateX(55%);
            }
            .footerLogo2 img{
                transform: translateX(20%);
            }
            .right-box{
                height: 100%;
                border-left:1px solid black;
            }
            .board{
                display: flex;
                justify-content: center;
                height: 100%;
            }
            .board>div{
            width: 100%;
            background-color: rgba(255, 197, 142, 0);
            height: 100%;
            }
            .board div.left_board{
                display: flex;
                /* justify-content: center; */
                /* border-radius:10px; */
                padding: 10px;
                background-color: #ffffff;
                width: 110%;
            }
            .board div.right_board{
                /* border-left:1px  solid #2c2c2c; */
                /* box-shadow: -10px 0px 10px #2c2c2c; */
                background-color: white;
                width: 90%;
            }
            .board div.right_board img{
                height: 30% !important ;
                width: 30% !important;
            }
            .board div.right_board h1{
               font-size: 26px;
               margin-top: -1px ;
            }
            .board div.right_board h5{
                font-size: 10px;
            }
            /* slide */
            .image_1{ 
                position: relative;
                width: 100%;
                height: 88.5px;
                background-image: url('../../../uploads/image_1.jpg');
                background-position: center;
                background-size: cover;
                background-repeat: no-repeat;
            }
            .image_2{ 
                position: relative;
                width: 100%;
                height: 88.5px;
                background-image: url('../../../uploads/image_1.jpg');
                background-position: center;
                background-size: cover;
                background-repeat: no-repeat;
            }
            .image_3{ 
                position: relative;
                width: 100%;
                height: 88.5px;
                background-image: url('../../../uploads/image_3.jpg');
                background-position: center;
                background-size: cover;
                background-repeat: no-repeat;
            }
            .image_4{ 
                position: relative;
                width: 100%;
                height: 88.5px;
                background-image: url('../../../uploads/image_4.jpg');
                background-position: center;
                background-size: cover  ;
                background-repeat: no-repeat;
            }
        /* platform */
        .plartform {
            padding:20px;
        }
        .custom-radio label{

            position:relative !important;
        }
        .custom-radio {
            display: flex;
            justify-content: space-around;
            /* flex-direction: column; */
            position:relative;
        }
        .custom-radio>div{
            /* background-color: red; */
            display:flex;
            position:relative !important;
            width: 100%;
        }
        .custom-radio>div label{
            /* display:flex; */
            position:absolute;
            width:100%; 
            padding-top: 20px;
            bottom:-100px;
        }
        .custom-radio>div input{
            /* width:100%; */
            font-size:10px;
            height:20px;
            background-color:red;
        }
        .custom-radio input:checked ~ .checkmark {
            background-color: #ff0000;
        }
        .custom-radio .checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 25px;
            width: 25px;
            background-color: #eee;
            border-radius: 50%;
        }
        .box_sections{
            cursor: pointer;
            position:relative;
            display: flex;
            width:100%;
            /* margin: 30px; */
            padding:30px;
            justify-content: space-between;
        }
        .box_sections:hover{
            background-color:#e86000;
            color:#fff;
        }
        .box_sections:hover h5{
            /* background-color:#e86000; */
            color:#fff;
        }
        .box_sections h5{
            padding:30px;
            left:100px;
            top:-10px;
            position:absolute;
        }
        .plartform label{
            position:absolute;
            top:-10px;
            left:8px;
            letter-spacing:1px;
            font-size:13px;
            color:#e68000;
        }
        .form_btn_primary{
            /* height: 100%; */
            box-shadow: 1px 1px 10px rgba(155, 155, 155, 0.226);
            background-color: #e68000 !important;
            border-radius: 5px;
            font-weight: 700;
            color: #f7f7f7 !important;
            padding: 10px 25px;
            width: 100px; 
        }
        .form_btn_close{
            box-shadow: 1px 1px 10px rgba(155, 155, 155, 0.226);
            /* height: 100%; */
            background-color: #f7f7f7;
            border-radius: 5px;
            color: #303030;
            padding: 10px 20px;
            width: 80px; 

        }
        .bottom_platform{
            display: flex;
            justify-items:center;
            justify-content: space-between;
            justify-items: flex-start;
            text-justify: auto;
            /* justify-content: space-between; */
            /* height: 8%; */
            padding:0px 100px; 
            /* width: 100% !; */
            /* background-color: #e68000; */
        }
        .left_board form{
            width:100%;
        }
        .next:hover,
        .finish:hover{
            color: #fff;
        }
        .prev:hover{
            color: #303030 !important;
        }
        .platforms{
            
            width: 100% !important;
            height: 90%;
            /* position: relative; */
            /* background-color: #f90000; */
        }
        .platforms>div{
            width: 100%;
            /* height: 90%; */
            /* position: relative; */
            /* background-color: #ffffff; */
        }
    }
    @media (min-width: 900px) and (max-width: 1024px){

            /* **** */
            * {box-sizing:border-box}

            /* Slideshow container */
            .slideshow-container {
                /* position: relative;
                max-width: 100%;
                padding: 1px;
                height: 100px !important; */
                position: relative;
                max-width: 83.5%;
                padding: 1px;
                height: 20px !important;
                top: -87.5px;
                left: 165px;
                /* top: -20px; */
                /* border: 1px solid red; */
            }

            /* Hide the images by default */
            .mySlides {
                /* border: 1px solid red; */
                display: block;
                height: 100px !important;
                position: absolute;
            }

            
                
            /* **** */
            @page{
                margin: 100px 100px 10px 20px ;
            }
            header{
                top: 10px;
                background:linear-gradient(to right bottom ,#ffe9d2 10% , #ffe9d2 40% ,#fff3f3 100%);
                height: 300px !important;
                width: 100%;
                padding: 10px;
                position: fixed;
                justify-content: center
            }
            .contents{
                position: relative;
            }
            footer{
                bottom: 0px;
                background:#616161;
                height: 200px !important;
                width: 100%;
                padding: 10px;
                position: fixed;
            }
            body{
                box-sizing: border-box;
                background:linear-gradient(to top left ,rgb(235, 249, 255) 1% , rgb(255, 239, 223) 100% ,#cecece 100%);
                /* background-image:url("../../../uploads/IZO-D2.gif") */
            }
            .left_form_first_login{
                border: 10px solid black;
                border-radius: 10px;
                padding: 10px;
                box-shadow:1px 1px 100px rgb(197, 197, 197);
                background: rgb(255, 255, 255);
            }
            .right_form_first_login{
                border: 10px solid black;
                border-radius: 10px;
                padding: 10px; 
                background: rgba(64, 64, 110, 0);
            }
            .mainTopbox{
                border-bottom: 10px solid #e86000;
                background-color: #303030;
                width: 100%;
                box-shadow: 1px 1px 10px black;
                height: 102.5px;
            }
            .mainBottombox{
                border-top: 5px solid;
                /* border-top-color: linear-gradient(to top left ,rgb(235, 249, 255) 10% , rgb(255, 239, 223) 40% ,#cecece 100%); */
                border-image: linear-gradient(to right,#e68000 10%,#eeddee00,#d80b7800 100%) 1 0 0 0;
                width: 100%;
                background-color: #ffffff;
                height: auto;
            }
            .mainbox{
                display: flex;
                padding: 10px 30px;
                height: auto;
                width: 100%;
                background: rgba(0, 0, 0, 0);
                justify-content: center;
                border: 0px solid #f90000;
                
                /* margin-top: 100px; */
                /* margin-bottom: 100px; */
                /* border-radius: 10px; */
            }
            .childbox{
                border: 0px solid rgb(249, 0, 0);
                width: 100%;
                margin-top: 0px;
                border-radius: 10px;
                padding: 10px 10px;
                height: auto;
                transform: translateY(0%);
                background: rgba(93, 237, 9, 0); 
                display: flex;
                justify-content: center;
            }
            .childbox .leftChild{
                position: absolute;
                padding: 10px;
                left: -100%;
                width: 100%;
                border: 0px solid black;
                display: flex;
                flex-direction: column;
                justify-content: start  ;
                justify-items: center;

            }
            .childbox .leftChild div{
                padding: 10px;
                margin: 1px 2px;
                width: 100%;
                text-align: left;
                font-weight: 700;
                color: #303030;
                border: 0px solid rgba(0, 0, 0, 0.603); 
                cursor: pointer;
                background-color: #f7f7f7;
            }
            .childbox .rightChild{
                padding: 10px;
                width: 90%;
                background-color: rgba(255, 255, 255, 0);
                border: 0px solid black;
                /* box-shadow: 3px 0px 10px rgb(168, 168, 168); */
                border-radius: 0px;
            }
            .active_div{
                
                color: white !important;
                border: 0px solid rgba(0, 0, 0, 0.603) !important; 
                background-color: #e86000 !important;
                box-shadow: 0px 1px 10px black !important;  
            } 
            .active_div h5{
                color: white !important;
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
            .izo-form-input:focus{
                border:1px solid #ec6808 !important;
                outline:1px solid #ec6808 !important;
            }
            .izo-form-input{
                width: 100%;
                border-radius: 10px;
                padding: 10px;
                margin: 10px auto ;
                font-size: 17px;
                border: 1px solid #3a3a3a33;
                /* outline: 1px solid #3a3a3a33 */
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
                    display: none;
            }
            .right_form_first_login_top{
                border: 2px solid black;
                border-radius: 10px;
                padding: 10px; 
                background: rgba(64, 64, 110, 0);
                display: none;
            }
            ul , .header_nav{
                list-style: none;
                margin: 0px;
                width: 100%;
                padding-left: 0%;
                border:0px solid rgb(255, 255, 255);
            }
            ul li{
                padding :5px;

            }
            .header_nav{
                text-align: center;
            }
            .header_nav li{
                padding :10px;
                margin :10px;
                text-align: right;
                border: 0px solid black;
                display: inline-block;
                color: white;
                font-weight: 700;
                font-size: 18px;
                width: calc(90%/7);
            }
            .logo_text{
                margin-left:10px;
                padding-top: 4px;
                padding-left: 10px;
                font-weight: 700;
                color: black;
                border-left :5px solid #e68000;      
            }
            .logo_text_header{
                padding-top: 0px;
                padding-left: 5px;
                font-weight: 700;
                font-size: 15px;
                color: white;
                margin-left:-17px;
                border-left :3px solid #e68000;      
            }
            .title_color{
                color: #2c2c2c;
            }
            .BOX_DOMAIN{
                cursor: pointer;
                background-color: #e68000;
                border: 1px solid #e68000;
                border-radius: 10px;
                width: 80% ;
                padding: 10px;
                color: white;
                margin: 1% auto; 
                /* transform: translateX(-50%); */
                box-shadow: 1px 1px 10px rgb(122, 122, 122);
                color: #2c2c2c;
                font-size: 13px;
                overflow: hidden;
            } 
            .font_style{
                font-family:Georgia, 'Times New Roman', Times, serif;
            }
            .footerLogo img{
                height: 20px;
                width:60px;
                transform: translateX(50%);
            }
            .footerLogo2 img{
                transform: translateX(35%);
            }
            .right-box{
                height: 100%;
                border-left:1px solid black;
            }
            .board{
                display: flex;
                justify-content: center;
                height: 100%;
            }
            .board>div{
                width: 100%;
                background-color: rgba(255, 197, 142, 0);
                height: 100%;
            }
            .board div.left_board{
                display: flex;
                /* justify-content: center; */
                /* border-radius:10px; */
                padding: 10px;
                background-color: #ffffff;
                width: 110%;
            }
            .board div.right_board{
                /* border-left:1px  solid #2c2c2c; */
                /* box-shadow: -10px 0px 10px #2c2c2c; */
                background-color: white;
                width: 90%;
            }
            .board div.right_board img{
                width: 50%;
                height: 50%;
            }
            .board div.right_board h1{
               font-size: 20px;
            }
            .board div.right_board h5{
                font-size: 10px;
            }
            /* slide */
            .image_1{ 
                position: relative;
                width: 100%;
                height: 400%;
                background-image: url('../../../uploads/image_1.jpg');
                background-position: center;
                background-size: cover;
                background-repeat: no-repeat;
            }
            .image_2{ 
                position: relative;
                width: 100%;
                height: 400%;
                background-image: url('../../../uploads/image_1.jpg');
                background-position: center;
                background-size: cover;
                background-repeat: no-repeat;
            }
            .image_3{ 
                position: relative;
                width: 100%;
                height: 400%;
                background-image: url('../../../uploads/image_3.jpg');
                background-position: center;
                background-size: cover;
                background-repeat: no-repeat;
            }
            .image_4{ 
                position: relative;
                width: 100%;
                height: 400%;
                background-image: url('../../../uploads/image_4.jpg');
                background-position: center;
                background-size: cover  ;
                background-repeat: no-repeat;
            }
        /* platform */
        .plartform {
            padding:20px;
        }
        .custom-radio label{

            position:relative !important;
        }
        .custom-radio {
            display: flex;
            justify-content: space-around;
            /* flex-direction: column; */
            position:relative;
        }
        .custom-radio>div{
            /* background-color: red; */
            display:flex;
            position:relative !important;
            width: 100%;
        }
        .custom-radio>div label{
            /* display:flex; */
            position:absolute;
            width:100%; 
            padding-top: 20px;
            bottom:-100px;
        }
        .custom-radio>div input{
            /* width:100%; */
            font-size:10px;
            height:20px;
            background-color:red;
        }
        .custom-radio input:checked ~ .checkmark {
            background-color: #ff0000;
        }
        .custom-radio .checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 25px;
            width: 25px;
            background-color: #eee;
            border-radius: 50%;
        }
        .box_sections{
            cursor: pointer;
            position:relative;
            display: flex;
            width:100%;
            /* margin: 30px; */
            padding:30px;
            justify-content: space-between;
        }
        .box_sections:hover{
            background-color:#e86000;
            color:#fff;
        }
        .box_sections:hover h5{
            /* background-color:#e86000; */
            color:#fff;
        }
        .box_sections h5{
            padding:30px;
            left:100px;
            top:-10px;
            position:absolute;
        }
        .plartform label{
            position:absolute;
            top:-10px;
            left:8px;
            letter-spacing:1px;
            font-size:13px;
            color:#e68000;
        }
        .form_btn_primary{
            /* height: 100%; */
            box-shadow: 1px 1px 10px rgba(155, 155, 155, 0.226);
            background-color: #e68000 !important;
            border-radius: 5px;
            font-weight: 700;
            color: #f7f7f7 !important;
            padding: 10px 25px;
            width: 100px; 
        }
        .form_btn_close{
            box-shadow: 1px 1px 10px rgba(155, 155, 155, 0.226);
            /* height: 100%; */
            background-color: #f7f7f7;
            border-radius: 5px;
            color: #303030;
            padding: 10px 20px;
            width: 80px; 

        }
        .bottom_platform{
            display: flex;
            justify-items:center;
            justify-content: space-between;
            justify-items: flex-start;
            text-justify: auto;
            /* justify-content: enspace-betwe; */
            /* height: 8%; */
            padding:0px 100px; 
            /* width: 100% !; */
            /* background-color: #e68000; */
        }
        .left_board form{
            width:100%;
        }
        .next:hover,
        .finish:hover{
            color: #fff;
        }
        .prev:hover{
            color: #303030;
        }
        .platforms{
            
            width: 100% !important;
            height: 90%;
            /* position: relative; */
            /* background-color: #f90000; */
        }
        .platforms>div{
            width: 100%;
            /* height: 90%; */
            /* position: relative; */
            /* background-color: #ffffff; */
        }
    }
   
</style>
@endsection
@section('content')
 @php
    $list_type_of_business  = [
        "shop"       => "Shops And POS",
        "craft"      => "Crafts and service professions",
        "medical"    => "Medical care",
        "tourism"    => "Logistics services",
        "care"       => "Body care and fitness",
        "tourism"    => "Tourism, transportation and hospitality",
        "education"  => "Education",
        "cars"       => "Cars",
        "services"   => "Business services",
        "projects"   => "Projects, contracting and real estate investment"
    ];
    
    $list_section_of_business['shop']  = [
        1 => "Shops And POS sections one",
        2 => "Shops And POS sections Two"
    ];
    $list_section_of_business['craft']  = [
        1 => "Crafts and service professions sections one",
        2 => "Crafts and service professions sections Two",
        3 => "Crafts and service professions sections Three"
    ];
    $list_section_of_business['medical']  = [
        1 => "Medical care sections one",
        2 => "Medical care sections Two",
        3 => "Medical care sections Three",
        4 => "Medical care sections Four"
    ];
    $list_section_of_business['tourism']  = [
        1 => "Logistics services",
        1 => "Logistics services",
        1 => "Logistics services"
    ];
    $list_section_of_business['care']  = [
        2 => "Body care and fitness",
        2 => "Body care and fitness",
        2 => "Body care and fitness",
    ];
    $list_section_of_business['tourism']  = [
        3 => "Tourism, transportation and hospitality",
        3 => "Tourism, transportation and hospitality",
    ];
    $list_section_of_business['education']  = [
        1 => "Education"
    ];
    $list_section_of_business['cars']  = [
        1 => "Cars"
    ];
    $list_section_of_business['services']  = [
        1 => "Business services"
    ];
    $list_section_of_business['projects']  = [
        1 => "Projects, contracting and real estate investment"
    ];
 @endphp
    {{-- @if(request()->session()->get('startLogin'))
        <div class="container">
            <div class="row">
                <div class="loading col-12 text-center">
                    izo
                    <small>waiting...</small>
                </div>
            </div>
        </div>
       
    @else --}}
        <body class="contents font_style">
           
            {{-- top --}}
            <div class="container mainTopbox">
                <div class="row">
                    <div class="col-md-2 col-6" >
                        <div class="footerLogo p-10">
                            <img src="../../../uploads/headerLogo.png" width="50px" height="15px" alt="logo">
                            <h6 class="logo_text_header">{{"THE FUTURE IS HERE"}}</h6>
                        </div>
                    </div>
                    <div class="col-md-3 col-6" >
                        <div class="slideshow-container">
                            <!-- Full-width images with number and caption text -->
                            <div class="mySlides image_1" data-active="1" data-id="1"></div>
                            <div class="mySlides hide image_2"  data-active="0"  data-id="2"></div>
                            <div class="mySlides hide image_3"  data-active="0" data-id="3"></div>
                            <div class="mySlides hide image_4"  data-active="0" data-id="4"></div>
                            
                        </div>
                        <br>
                        
                    </div>
                    
                    <div class="col-md-4 text-right">
                        @php   
                            $domain_name = "http://".session()->get('user_main.domain').".localhost:8000/login-account";
                            $domain_name = $domain_name??"";
                            @endphp 
                        <br>
                        <ul class="header_nav">
                            <li>{{" "}}</li>
                            <li class="hide">{{"Website"}}</li>
                            <li class="hide">{{"IZOCLOUD"}}</li>
                            <li>
                                
                            </li>
                        </ul> 
                        
                    </div>
                    
                    <div class="col-md-3 col-6" >
                        <div class="pull-right">
                            <br>
                            <select class="input-form" style="border-radius:10px;font-size:14px;padding 10px; color:#ec6808" id="select_lang">
                                <option  value="">
                                    &nbsp;&nbsp; Language
                                    <i class="fa fas fa-globe" ></i>
                                </option>
                                <option data-href="{{action("HomeController@changeLanguageApp",["lang"=>"en"])}}" value="en">
                                      <a href="#"> &nbsp;&nbsp;{{__('izo.english')}}  </a>
                                </option>
                                <option data-href="{{action("HomeController@changeLanguageApp",["lang"=>"ar"])}}" value="ar" >
                                    <a href="#">&nbsp;&nbsp;{{__('izo.arabic')}}  </a>  
                                </option> 
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            {{-- center --}}
            <div class="container mainbox">
                <input type="hidden" id='list_section' value="{{json_encode($list_section_of_business)}}">
                <div class="row childbox">
                    <div class="leftChild"> 
                            <div onclick="viewPlatform(1);"  data-id="1" class="btn_platform active_div">{{"Personal Information"}}</div>
                            <div onclick="viewPlatform(2);"  data-id="2" class="btn_platform">{{"Business Information"}}</div>
                            {{-- <div onclick="viewPlatform(3);"  data-id="3" data-type="1" class="btn_platform type_of_job hide">{{"Type Of Job"}}</div> --}}
                            {{--<div onclick="viewPlatform(5);"  data-id="5" class="btn_platform">{{"PLANS"}}</div>
                            <div onclick="viewPlatform(6);"  data-id="6" class="btn_platform">{{"LOGOUT"}}</div> --}}
                            <div ><a style="color: #2c2c2c" href="/account/logout">{{"LOGOUT"}}</a></div>  
                    </div>
                    <div class="rightChild">  
                            <div class="board">
                                <div class="left_board">
                                    {!! Form::open( ["url" => "/create-company", "method" => "POST" , "file" => true ]) !!}
                                    <div class="platforms">
                                        <div class="plartform " data-id="1" >
                                            <div class="col-md-6">
                                                {!! Form::label('first_name', __('First Name *') ) !!}
                                                {!! Form::text('first_name',null,['class' => 'izo-form-input', 'id'=>'first_name','required', 'placeholder' => __('First Name') ]) !!}
                                            </div>
                                            <div class="col-md-6">
                                                {!! Form::label('second_name', __('Second Name *') ) !!}
                                                {!! Form::text('second_name',null,['class' => 'izo-form-input', 'id'=>'second_name','required', 'placeholder' => __('Second Name') ]) !!}
                                            </div>
                                            <div class="clearfix"></div>
                                            <div class="col-md-6">
                                                {!! Form::label('job', __('Job') ) !!}
                                                {!! Form::select('job',$jobs,null,['class' => 'izo-form-input', 'id'=>'job','required', 'placeholder' => __('-- Choose job') ]) !!}
                                            </div>
                                            <div class="col-md-6">
                                                {!! Form::label('company_size', __('Size Of Company *') ) !!}
                                                {!! Form::select('company_size',$company_size,null,['class' => 'izo-form-input','required', 'id'=>'company_size', 'placeholder' => __('-- Choose Size') ]) !!}
                                            </div>
                                            <div class="clearfix"></div>
                                            <div class="col-md-6">
                                                {!! Form::label('country', __('Country *') ) !!}
                                                {!! Form::select('country',$country,null,['class' => 'izo-form-input', 'id'=>'country','required', 'placeholder' => __('-- Choose Country') ]) !!}
                                            </div>
                                            <div class="col-md-6">
                                                {!! Form::label('time_zone', __('Time Zone *') ) !!}
                                                {!! Form::select('time_zone',$timeZone,null,['class' => 'izo-form-input', 'id'=>'time_zone','required', 'placeholder' => __('-- Choose Time Zone') ]) !!}
                                            </div>
                                            <div class="clearfix"></div>
                                            <div class="col-md-6">
                                                {!! Form::label('currency', __('Currency *') ) !!}
                                                {!! Form::select('currency',$currency,null,['class' => 'izo-form-input', 'id'=>'currency','required', 'placeholder' => __('-- Choose Currency') ]) !!}
                                            </div>
                                            <div class="col-md-6">
                                                {!! Form::label('language', __('Language *') ) !!}
                                                {!! Form::select('language',$language,null,['class' => 'izo-form-input', 'id'=>'language','required', 'placeholder' => __('-- Choose Language') ]) !!}
                                            </div>
                                            <div class="clearfix"></div>
                                            <div class="col-md-6">
                                                {!! Form::label('address', __('Address') ) !!}
                                                {!! Form::text('address',null,['class' => 'izo-form-input', 'id'=>'address', 'placeholder' => __('Address') ]) !!}
                                            </div>
                                            <div class="col-md-6">
                                                {!! Form::label('city', __('City') ) !!}
                                                {!! Form::text('city',null,['class' => 'izo-form-input', 'id'=>'city','placeholder' => __('City') ]) !!}
                                            </div>
                                            <div class="clearfix"></div>
                                            <div class="col-md-6">
                                                {!! Form::label('zip_code', __('Zip Code') ) !!}
                                                {!! Form::text('zip_code',null,['class' => 'izo-form-input', 'id'=>'zip_code', 'placeholder' => __('Zip Code') ]) !!}
                                            </div>
                                            <div class="col-md-6">
                                                {!! Form::label('tax_number', __('Tax Number') ) !!}
                                                {!! Form::text('tax_number',null,['class' => 'izo-form-input', 'id'=>'tax_number', 'placeholder' => __('Tax Number') ]) !!}
                                            </div>
                                        </div> 
                                        <div class="plartform hide" data-id="2"  >
                                            <div class="col-md-6">
                                                {!! Form::label('type_business', __('Type Of Business *') ) !!}
                                                {!! Form::select('type_business',$list_type_of_business,null,['class' => 'izo-form-input','required', 'id'=>'type_business', 'placeholder' => __('-- Choose Type Of Business') ]) !!}
                                            </div>
                                            <div class="col-md-6">
                                                {!! Form::label('section', __('Type Of Business *') ) !!}
                                                {!! Form::select('section',[],null,['class' => 'izo-form-input','required', 'id'=>'section','placeholder' => __('-- Choose Type Of Business')  ]) !!}
                                            </div>
                                            {{-- <div class="custom-radio"> 
                                                <div class="left-radio">
                                                    {!! Form::radio('type_business',1,null,['class' => 'izo-form-input type_business', 'id'=>'type_business' ]) !!} 
                                                    {!! Form::label('type_business', __('Shops And POS') ) !!}
                                                </div>
                                                <div class="right-radio">
                                                    {!! Form::radio('type_business',2,null,['class' => 'izo-form-input type_business', 'id'=>'type_business' ]) !!} 
                                                    {!! Form::label('type_business', __('Crafts and service professions') ) !!}
                                                </div>
                                            </div>
                                            <div class="custom-radio">  
                                                <div class="left-radio">
                                                    {!! Form::radio('type_business',3,null,['class' => 'izo-form-input type_business', 'id'=>'type_business' ]) !!} 
                                                    {!! Form::label('type_business', __('Medical care') ) !!}
                                                </div>
                                                <div class="left-radio">
                                                    {!! Form::radio('type_business',4,null,['class' => 'izo-form-input type_business', 'id'=>'type_business' ]) !!}
                                                    {!! Form::label('type_business', __('Logistics services') ) !!}
                                                </div>
                                            </div>
                                            <div class="custom-radio"> 
                                                <div class="left-radio">
                                                    {!! Form::radio('type_business',5,null,['class' => 'izo-form-input type_business', 'id'=>'type_business' ]) !!} 
                                                    {!! Form::label('type_business', __('Body care and fitness') ) !!}
                                                </div>
                                                <div class="right-radio">
                                                    {!! Form::radio('type_business',6,null,['class' => 'izo-form-input type_business', 'id'=>'type_business' ]) !!} 
                                                    {!! Form::label('type_business', __('Tourism, transportation and hospitality') ) !!}
                                                </div>
                                            </div>
                                            <div class="custom-radio"> 
                                                <div class="left-radio">
                                                    {!! Form::radio('type_business',7,null,['class' => 'izo-form-input type_business', 'id'=>'type_business' ]) !!}
                                                    {!! Form::label('type_business', __('education') ) !!}
                                                </div>
                                                <div class="right-radio">
                                                    {!! Form::radio('type_business',8,null,['class' => 'izo-form-input type_business', 'id'=>'type_business' ]) !!} 
                                                    {!! Form::label('type_business', __('Cars') ) !!}
                                                </div>
                                            </div>
                                            <div class="custom-radio">
                                                <div class="left-radio">
                                                    {!! Form::radio('type_business',9,null,['class' => 'izo-form-input type_business', 'id'=>'type_business' ]) !!} 
                                                    {!! Form::label('type_business', __('Business services') ) !!}
                                                </div>
                                                <div class="right-radio">
                                                    {!! Form::radio('type_business',10,null,['class' => 'izo-form-input type_business', 'id'=>'type_business' ]) !!} 
                                                    {!! Form::label('type_business', __('Projects, contracting and real estate investment') ) !!}
                                                </div> 
                                            </div> --}}
                                        </div>
                                        {{-- <div class="plartform hide" data-id="3"  >
                                            <div class="sections" data-type="1">
                                                <div class="box_sections" data-type="1" data-section="1">
                                                    <h5>{{"Shops And POS One"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div>
                                                <div class="box_sections" data-type="1" data-section="2">
                                                    <h5>{{"Shops And POS Two"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div>
                                                <div class="box_sections" data-type="1" data-section="3">
                                                    <h5>{{"Shops And POS Three"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div>
                                                <div class="box_sections" data-type="1" data-section="4">
                                                    <h5>{{"Shops And POS Four"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div>
                                                <div class="box_sections" data-type="1" data-section="5">
                                                    <h5>{{"Shops And POS Five"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div> 
                                            </div>
                                            <div class="sections" data-type="2">
                                                <div class="box_sections" data-type="2" data-section="1">
                                                    <h5>{{"Crafts and service professions One"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div>
                                                <div class="box_sections" data-type="2" data-section="2">
                                                    <h5>{{"Crafts and service professions Two"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div>
                                                <div class="box_sections" data-type="2" data-section="3">
                                                    <h5>{{"Crafts and service professions Three"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div>
                                                <div class="box_sections" data-type="2" data-section="4">
                                                    <h5>{{"Crafts and service professions Four"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div>
                                                <div class="box_sections" data-type="2" data-section="5">
                                                    <h5>{{"Crafts and service professions Five"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div> 
                                            </div>
                                            <div class="sections" data-type="3">
                                                <div class="box_sections" data-type="3" data-section="1">
                                                    <h5>{{"Medical care One"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div>
                                                <div class="box_sections" data-type="3" data-section="2">
                                                    <h5>{{"Medical care Two"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div>
                                                <div class="box_sections" data-type="3" data-section="3">
                                                    <h5>{{"Medical care Three"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div>
                                                <div class="box_sections" data-type="3" data-section="4">
                                                    <h5>{{"Medical care Four"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div>
                                                <div class="box_sections" data-type="3" data-section="5">
                                                    <h5>{{"Medical care Five"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div> 
                                            </div>
                                            <div class="sections" data-type="4">
                                                <div class="box_sections" data-type="4" data-section="1">
                                                    <h5>{{"Logistics One"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div>
                                                <div class="box_sections" data-type="4" data-section="2">
                                                    <h5>{{"Logistics Two"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div>
                                                <div class="box_sections" data-type="4" data-section="3">
                                                    <h5>{{"Logistics  Three"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div>
                                                <div class="box_sections" data-type="4" data-section="4">
                                                    <h5>{{"Logistics  Four"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div>
                                                <div class="box_sections" data-type="4" data-section="5">
                                                    <h5>{{"Logistics  Five"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div> 
                                            </div>
                                            <div class="sections" data-type="5">
                                                <div class="box_sections" data-type="5" data-section="1">
                                                    <h5>{{"fitness One"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div>
                                                <div class="box_sections" data-type="5" data-section="2">
                                                    <h5>{{"fitness Two"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div>
                                                <div class="box_sections" data-type="5" data-section="3">
                                                    <h5>{{"Three"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div>
                                                <div class="box_sections" data-type="5" data-section="4">
                                                    <h5>{{"fitness Four"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div>
                                                <div class="box_sections" data-type="5" data-section="5">
                                                    <h5>{{"fitness Five"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div> 
                                            </div>
                                            <div class="sections" data-type="6">
                                                <div class="box_sections" data-type="6" data-section="1">
                                                    <h5>{{"Tourism One"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div>
                                                <div class="box_sections" data-type="6" data-section="2">
                                                    <h5>{{"Tourism Two"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div>
                                                <div class="box_sections" data-type="6" data-section="3">
                                                    <h5>{{"Tourism Three"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div>
                                                <div class="box_sections" data-type="6" data-section="4">
                                                    <h5>{{"Tourism Four"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div>
                                                <div class="box_sections" data-type="6" data-section="5">
                                                    <h5>{{"Tourism Five"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div> 
                                            </div>
                                            <div class="sections" data-type="7">
                                                <div class="box_sections" data-type="7" data-section="1">
                                                    <h5>{{"education One"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div>
                                                <div class="box_sections" data-type="7" data-section="2">
                                                    <h5>{{"education Two"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div>
                                                <div class="box_sections" data-type="7" data-section="3">
                                                    <h5>{{"education Three"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div>
                                                <div class="box_sections" data-type="7" data-section="4">
                                                    <h5>{{"education Four"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div>
                                                <div class="box_sections" data-type="7" data-section="5">
                                                    <h5>{{"education Five"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div> 
                                            </div>
                                            <div class="sections" data-type="8">
                                                <div class="box_sections" data-type="8" data-section="1">
                                                    <h5>{{"Cars One"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div>
                                                <div class="box_sections" data-type="8" data-section="2">
                                                    <h5>{{"Cars Two"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div>
                                                <div class="box_sections" data-type="8" data-section="3">
                                                    <h5>{{"Cars Three"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div>
                                                <div class="box_sections" data-type="8" data-section="4">
                                                    <h5>{{"Cars Four"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div>
                                                <div class="box_sections" data-type="8" data-section="5">
                                                    <h5>{{"Cars Five"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div> 
                                            </div>
                                            <div class="sections" data-type="9">
                                                <div class="box_sections" data-type="9" data-section="1">
                                                    <h5>{{"Business services One"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div>
                                                <div class="box_sections" data-type="9" data-section="2">
                                                    <h5>{{"Business services Two"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div>
                                                <div class="box_sections" data-type="9" data-section="3">
                                                    <h5>{{"Business services Three"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div>
                                                <div class="box_sections" data-type="9" data-section="4">
                                                    <h5>{{"Business services Four"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div>
                                                <div class="box_sections" data-type="9" data-section="5">
                                                    <h5>{{"Business services Five"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div> 
                                            </div>
                                            <div class="sections" data-type="10">
                                                <div class="box_sections" data-type="10" data-section="1">
                                                    <h5>{{"Projects One"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div>
                                                <div class="box_sections" data-type="10" data-section="2">
                                                    <h5>{{" ProjectsTwo"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div>
                                                <div class="box_sections" data-type="10" data-section="3">
                                                    <h5>{{"Projects Three"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div>
                                                <div class="box_sections" data-type="10" data-section="4">
                                                    <h5>{{"Projects Four"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div>
                                                <div class="box_sections" data-type="10" data-section="5">
                                                    <h5>{{"Projects Five"}}</h5>
                                                    <i class="fa fas fa-star"></i>
                                                </div> 
                                            </div>
                                        </div> --}}
                                    </div>
                                    <div class="bottom_platform">
                                        <a href="#" class="form_btn_close prev" onclick="prev();">PREV</a>
                                        <a href="#" class="form_btn_primary next" onclick="next();">NEXT</a>
                                        <a href="#" class="form_btn_primary finish hide" >
                                            {!! Form::submit("FINISH",['class'=>'submit_panel']) !!}
                                        </a>
                                    </div> 
                                    <input type="hidden" name="side" class="side" id="side" value="1"/>
                                    <input type="hidden" name="current_active" class="current_active" id="current_active" value="1"/>
                                    <input type="hidden" name="box_section_value" class="box_section_value" id="box_section_value" value="-1"/>
                                    {!! Form::close() !!}
                                </div>
                                <div class="right_board">
                                    <div class="row font_style">
                                        <div class="col-12"></div>
                                        <div class="col-12"><h1>&nbsp;</h1></div>
                                        <div class="col-12 text-center">
                                            <img src="../../../uploads/footerLogo.png"  height="100px"  style="width:50%"> 
                                            <h1 class="p-10 font_style"><b>@lang('WELCOME TO IZOCLOUD')</b></h1>
                                            <h5 class="font_style"><b>@lang(' &nbsp;&nbsp;&nbsp; Now You can Manage Your Company<br> ')</br></h5>
                                        </div>
                                        <div class="col-12 text-center p-10"> 
                                                {!! "<a target='_blank' id='BOX_DOMAIN_url' href='".$domain_name."'><div class='BOX_DOMAIN'>".$domain_name."</div></a>"  !!}
                                        </div>  
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
            {{-- bottom --}}
            <div class="container mainBottombox">
                <div class="row p-10">
                    <div class="col-md-3">
                        <div class="footerLogo2 p-10">
                            <img src="../../../uploads/footerLogo.png" width="150px" height="45px" alt="logo">
                        </div>
                        <h3 class="logo_text">{{"THE FUTURE IS HERE"}}</h3>
                    </div>
                    <div class="col-md-3">
                        <h3 class="title_color">{{"All Sections"}}</h3>
                        <ul>
                            <li>{{"Dashboard"}}</li>
                            <li>{{"Website"}}</li>
                            <li>{{"Payments"}}</li>
                            <li>{{"Users"}}</li>
                            <li>{{"Subscribe"}}</li>
                        </ul>
                        </div>
                        <div class="col-md-3">
                            <h3  class="title_color">{{"Social Media"}}</h3>
                            <ul>
                            <li>{{"Whatsapp link"}}</li>
                            <li>{{"Facebook link"}}</li>
                            <li>{{"Youtube link"}}</li>
                            <li>{{"TikTok link"}}</li>
                            <li>{{"Instagram link"}}</li>
                            </ul>
                        </div>
                        <div class="col-md-3">
                            <h3  class="title_color">{{"Services"}}</h3>
                            <ul>
                            <li>{{"Financal Accounting"}}</li>
                            <li>{{"Programming Solutions"}}</li>
                            <li>{{"POS Systems"}}</li>
                            <li>{{"ERP Systems"}}</li>
                            </ul>
                        
                    </div>
                </div>
                <div class="col-md-12 p-10">
                    <h6 class="text-center">Powered By AGT All Right Reserved &copy; izocloud 2024</h6>  
                </div>
            </div>
        
            <script type="text/javascript">
                   
                    function next(){
                        var id = $('#current_active').attr('value');
                        
                        var i  = 0 ;
                        var first_name   = $('#first_name').val();
                        var second_name  = $('#second_name').val();
                        var job          = $('#job').val();
                        var company_size = $('#company_size').val();
                        var country      = $('#country').val();
                        var time_zone    = $('#time_zone').val();
                        var currency     = $('#currency').val();
                        var language     = $('#language').val();
                        var zip_code     = $('#zip_code').val();
                        var tax_number   = $('#tax_number').val(); 

                        if(first_name == ""  || second_name == ""  || job == ""  || company_size == ""  || country == ""  || time_zone == ""  || currency == ""  || language == ""  || 
                           first_name == null  || second_name == null  || job == null  || company_size == null  || country == null  || time_zone == null  || currency == null  || language == null    ){
                            i = 1;
                        }
                        if( i == 1){
                            if($('#first_name').val() == "" || $('#first_name').val()  == null){$('#first_name').addClass('active_input_focus');}else{$('#first_name').removeClass('active_input_focus')}
                            if($('#second_name').val() == "" || $('#second_name').val()  == null){$('#second_name').addClass('active_input_focus');}else{$('#second_name').removeClass('active_input_focus')}
                            if($('#job').val() == "" || $('#job').val()  == null){$('#job').addClass('active_input_focus');}else{$('#job').removeClass('active_input_focus')}
                            if($('#company_size').val() == "" || $('#company_size').val()  == null){$('#company_size').addClass('active_input_focus');}else{$('#company_size').removeClass('active_input_focus')}
                            if($('#country').val() == "" || $('#country').val()  == null){$('#country').addClass('active_input_focus');}else{$('#country').removeClass('active_input_focus')}
                            if($('#time_zone').val() == "" || $('#time_zone').val()  == null){$('#time_zone').addClass('active_input_focus');}else{$('#time_zone').removeClass('active_input_focus')}
                            if($('#currency').val() == "" || $('#currency').val()  == null){$('#currency').addClass('active_input_focus');}else{$('#currency').removeClass('active_input_focus')}
                            if($('#language').val() == "" || $('#language').val()  == null){$('#language').addClass('active_input_focus');}else{$('#language').removeClass('active_input_focus')}
                            if($('#section').val() == "" || $('#section').val()  == null){$('#section').addClass('active_input_focus');}else{$('#section').removeClass('active_input_focus')}
                        }else{
                            if($('#first_name').val() == "" || $('#first_name').val()  == null){$('#first_name').addClass('active_input_focus');}else{$('#first_name').removeClass('active_input_focus')}
                            if($('#second_name').val() == "" || $('#second_name').val()  == null){$('#second_name').addClass('active_input_focus');}else{$('#second_name').removeClass('active_input_focus')}
                            if($('#job').val() == "" || $('#job').val()  == null){$('#job').addClass('active_input_focus');}else{$('#job').removeClass('active_input_focus')}
                            if($('#company_size').val() == "" || $('#company_size').val()  == null){$('#company_size').addClass('active_input_focus');}else{$('#company_size').removeClass('active_input_focus')}
                            if($('#country').val() == "" || $('#country').val()  == null){$('#country').addClass('active_input_focus');}else{$('#country').removeClass('active_input_focus')}
                            if($('#time_zone').val() == "" || $('#time_zone').val()  == null){$('#time_zone').addClass('active_input_focus');}else{$('#time_zone').removeClass('active_input_focus')}
                            if($('#currency').val() == "" || $('#currency').val()  == null){$('#currency').addClass('active_input_focus');}else{$('#currency').removeClass('active_input_focus')}
                            if($('#language').val() == "" || $('#language').val()  == null){$('#language').addClass('active_input_focus');}else{$('#language').removeClass('active_input_focus')}
                            if($('#section').val() == "" || $('#section').val()  == null){$('#section').addClass('active_input_focus');}else{$('#section').removeClass('active_input_focus')}
                            if(parseFloat(id)+1>2){
                                id = 1;
                            }else{
                                id = parseFloat(id)+1
                            }
                            $('#current_active').attr('value',id);
                            viewPlatform(id);
                        }
                    }
                    function prev(){
                        id = $('#current_active').attr('value');
                        if(parseFloat(id)-1<=0){
                            id = 1;
                        }else{
                            id = parseFloat(id)-1
                        }
                        $('#current_active').attr('value',id);
                        viewPlatform(id);
                    }
                    function  viewPlatform(id){
                        var i  = 0 ;
                        var first_name   = $('#first_name').val();
                        var second_name  = $('#second_name').val();
                        var job          = $('#job').val();
                        var company_size = $('#company_size').val();
                        var country      = $('#country').val();
                        var time_zone    = $('#time_zone').val();
                        var currency     = $('#currency').val();
                        var language     = $('#language').val();
                        var zip_code     = $('#zip_code').val();
                        var tax_number   = $('#tax_number').val(); 

                        if(first_name == ""  || second_name == ""  || job == ""  || company_size == ""  || country == ""  || time_zone == ""  || currency == ""  || language == ""  || 
                           first_name == null  || second_name == null  || job == null  || company_size == null  || country == null  || time_zone == null  || currency == null  || language == null     ){
                            i = 1;
                        }
                        if( i == 1){ 
                            if($('#first_name').val() == "" || $('#first_name').val()  == null){$('#first_name').addClass('active_input_focus');}else{$('#first_name').removeClass('active_input_focus')}
                            if($('#second_name').val() == "" || $('#second_name').val()  == null){$('#second_name').addClass('active_input_focus');}else{$('#second_name').removeClass('active_input_focus')}
                            if($('#job').val() == "" || $('#job').val()  == null){$('#job').addClass('active_input_focus');}else{$('#job').removeClass('active_input_focus')}
                            if($('#company_size').val() == "" || $('#company_size').val()  == null){$('#company_size').addClass('active_input_focus');}else{$('#company_size').removeClass('active_input_focus')}
                            if($('#country').val() == "" || $('#country').val()  == null){$('#country').addClass('active_input_focus');}else{$('#country').removeClass('active_input_focus')}
                            if($('#time_zone').val() == "" || $('#time_zone').val()  == null){$('#time_zone').addClass('active_input_focus');}else{$('#time_zone').removeClass('active_input_focus')}
                            if($('#currency').val() == "" || $('#currency').val()  == null){$('#currency').addClass('active_input_focus');}else{$('#currency').removeClass('active_input_focus')}
                            if($('#language').val() == "" || $('#language').val()  == null){$('#language').addClass('active_input_focus');}else{$('#language').removeClass('active_input_focus')}
                            if($('#section').val() == "" || $('#section').val()  == null){$('#section').addClass('active_input_focus');}else{$('#section').removeClass('active_input_focus')}
                          }else{
                            if($('#first_name').val() == "" || $('#first_name').val()  == null){$('#first_name').addClass('active_input_focus');}else{$('#first_name').removeClass('active_input_focus')}
                            if($('#second_name').val() == "" || $('#second_name').val()  == null){$('#second_name').addClass('active_input_focus');}else{$('#second_name').removeClass('active_input_focus')}
                            if($('#job').val() == "" || $('#job').val()  == null){$('#job').addClass('active_input_focus');}else{$('#job').removeClass('active_input_focus')}
                            if($('#company_size').val() == "" || $('#company_size').val()  == null){$('#company_size').addClass('active_input_focus');}else{$('#company_size').removeClass('active_input_focus')}
                            if($('#country').val() == "" || $('#country').val()  == null){$('#country').addClass('active_input_focus');}else{$('#country').removeClass('active_input_focus')}
                            if($('#time_zone').val() == "" || $('#time_zone').val()  == null){$('#time_zone').addClass('active_input_focus');}else{$('#time_zone').removeClass('active_input_focus')}
                            if($('#currency').val() == "" || $('#currency').val()  == null){$('#currency').addClass('active_input_focus');}else{$('#currency').removeClass('active_input_focus')}
                            if($('#language').val() == "" || $('#language').val()  == null){$('#language').addClass('active_input_focus');}else{$('#language').removeClass('active_input_focus')}
                            if($('#section').val() == "" || $('#section').val()  == null){$('#section').addClass('active_input_focus');}else{$('#section').removeClass('active_input_focus')}
                            var id_finish = ($(".type_of_job").hasClass("hide"))?1:2;
                            if(id == id_finish){
                                $('.finish').removeClass('hide');
                                $('.next').addClass('hide');
                            }else{
                                $('.next').removeClass('hide');
                                $('.finish').addClass('hide');
                            }
                            $('.current_active').attr('value',id);
                            $('.btn_platform').each(function(){
                                if($(this).attr("data-id") == id){
                                    $(this).addClass('active_div');
                                }else{    
                                    $(this).removeClass('active_div');
                                }
                            });
                            $('.plartform').each(function(){
                                if(id == 1){
                                    type = $(".type_of_job").attr("data-type");
                                    $(".sections").each(function(){
                                        if($(this).attr("data-type") == type){
                                            $(this).removeClass('hide');
                                        }else{    
                                            $(this).addClass('hide');
                                        }
                                    });
                                }
                                if($(this).attr("data-id") == id){
                                    $(this).removeClass('hide');
                                }else{    
                                    $(this).addClass('hide');
                                }
                            });
                        }
                    }
                setTimeout(() => {
                    // .............................................. platform
                    $(".type_business").on("change",function(){
                        if($(this).is(":checked")){ 
                            $(".type_of_job").removeClass("hide");
                            $(".type_of_job").attr("data-type",$(this).val());
                            $('.next').removeClass('hide');
                            $('.finish').addClass('hide');
                            $(".box_sections").each(function(){
                                $(this).removeClass('active_div');
                                $("#box_section_value").attr("value",-1);
                            })
                        }
                    })
                    $("#select_lang").on("change",function(){ 
                        var ln  = $(this).val(); 
                        $.ajax({
                            url: '/home/change-lang-app',
                            dataType: 'json',
                            data:{
                                lang:ln
                            },
                            success: function(result) {
                                 location.reload();
                            },
                        });
                    })
                    $("#type_business").on("change",function(){ 
                        var type  = $(this).val(); 
                        list      = JSON.parse($("#list_section").val());
                        html      = '<option value=""> -- Choose Type Of Business</option>';
                        for( i in list){
                            if(type == i){
                                for( ie in list[i]) {
                                    html += '<option value="'+ie+'">'+list[i][ie]+'</option>'; 
                                }
                            }  
                        }
                        if(html == ''){
                            html      = '<option value=""> -- Choose Type Of Business</option>';
                        }     
                        $("#section").html(html); 
                         
                        
                    })
                    $(document).on('click','.language_box',function(){
                        $(".list_of_lang").toggleClass('hide');
                    });
                    $(".box_sections").each(function(){
                        $(this).on("click",function(){
                            id  = $(this).attr("data-type");
                            sec = $(this).attr("data-section");
                            $(".box_sections").each(function(){
                                if(id == $(this).attr("data-type") && $(this).attr("data-section") != sec ){
                                    console.log("id" + $(this).attr("data-type"));
                                    console.log("sec"+ $(this).attr("data-section"));
                                    $(this).removeClass('active_div');
                                }else if(id != $(this).attr("data-type")){
                                    $(this).removeClass('active_div');
                                }else{
                                    $(this).addClass('active_div');
                                }
                            })
                            var type    = $(this).attr("data-type");
                            var section = $(this).attr("data-section");
                            var type_section = type+section;
                            $("#box_section_value").attr("value",type_section);
                        })
                    })
                    // .............................................. media
                    applyFunctionBasedOnWidth();
                    function applyFunctionBasedOnWidth() {
                        var width = $(window).width();
                        if (width <= 1024 ) {  // Replace with your specific width range 
                            $(".leftChild").hover(
                                function() { 
                                    // $(this).parent().css({"background-color":"red"})
                                    $(this).css({"width":"100%","left":"0%"})
                                }, 
                                function() { 
                                    $(this).css({"width":"100%","left":"-100%"})
                                }
                            );
                        } else  {
                            console.log("Width is outside the specified range.");
                        }
                    }
                    $(window).resize(function() {
                        applyFunctionBasedOnWidth();
                    });
                }, 200);
                // ..................
                setInterval(() => { 
                    id = $('#side').val();
                    id = ((parseFloat(id)+1)>2)?1:(parseFloat(id) +1); 
                    
                    slide(id);
                    $('#side').attr("value",id); 
                    id1 = $('#side').val();
                }, 3000);
                function slide(id){
                    $(".mySlides").each(function(){
                        data_id    = $(this).attr('data-id'); 
                            if(data_id == id){
                                $(this).removeClass("hide");
                            }else{
                                $(this).addClass("hide");
                            }
                        });  
                        if(id == 1){
                            id = 2;
                        }else  if(id == 2){
                            id = 3;
                        }else if(id == 3){
                            id = 1;
                        }
                    }
            </script>
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
    {{-- @endif --}}
@endsection