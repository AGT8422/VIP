@extends('izo_user.layouts.app')

@section('title','panel')
@section('app_css')
 <style>

        .loading2{
            position: fixed;
            left: 0px ;
            right: 0px ;
            width: 100%;
            height: 100%;
            z-index: 1000;
            background-color: rgb(0, 0, 0);
        }
        .loading2 .loading-content{
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
        .loading2 .loading-content h1{ 
            color: #fefefe;  
            font-weight: bold;
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
    .logout{ 
        top: 10px;
        position: absolute;
        padding: 10px; 
        background-color: red;
        color: rgb(255, 255, 255);
        text-decoration: none;
    }
    .logout:hover{ 
        position: absolute;
        top: 10px;
        padding: 10px; 
        background-color: red;
        color: rgb(255, 255, 255);
        text-decoration: none;
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
            border-radius:10px;
            padding: 10px;
            background-color: #ffffff75;
            width: 120%;
            text-align: center;
        }
        .board div.right_board{
            border-left:1px  solid #2c2c2c;
            box-shadow: -10px 0px 10px #2c2c2c;
            background-color: white;
            width: 50%;
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
            top:-8px;
            letter-spacing:1px;
            font-size:12px;
            color:#e68000;
        }
        .form_btn_primary{
            /* height: 100%; */
            box-shadow: 1px 1px 10px rgba(155, 155, 155, 0.226);
            background-color: #e68000;
            border-radius: 5px;
            font-weight: 700;
            color: #f7f7f7;
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
            border-top-left-radius:10px;
            border-top-right-radius:10px;
            padding: 10px;
            background-color: #ffffff75;
            width: 110%;
        }
        .board div.right_board{
            border-left:1px  solid #2c2c2c;
            box-shadow: -10px 0px 10px #2c2c2c;
            background-color: white;
            width: 50%;
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
            top:-8px;
            letter-spacing:1px;
            font-size:12px;
            color:#e68000;
        }
        .form_btn_primary{
            /* height: 100%; */
            box-shadow: 1px 1px 10px rgba(155, 155, 155, 0.226);
            background-color: #e68000;
            border-radius: 5px;
            font-weight: 700;
            color: #f7f7f7;
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
                    border-radius:10px;
                    padding: 10px;
                    background-color: #ffffff75;
                    width: 110%;
                }
                .board div.right_board{
                    border-left:1px  solid #2c2c2c;
                    box-shadow: -10px 0px 10px #2c2c2c;
                    background-color: white;
                    width:50%;
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
            top:-8px;
            letter-spacing:1px;
            font-size:12px;
            color:#e68000;
        }
        .form_btn_primary{
            /* height: 100%; */
            box-shadow: 1px 1px 10px rgba(155, 155, 155, 0.226);
            background-color: #e68000;
            border-radius: 5px;
            font-weight: 700;
            color: #f7f7f7;
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
                    border-radius:10px;
                    padding: 10px;
                    background-color: #ffffff75;
                    width: 110%;
                }
                .board div.right_board{
                    border-left:1px  solid #2c2c2c;
                    box-shadow: -10px 0px 10px #2c2c2c;
                    background-color: white;
                    width:50%;
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
            top:-8px;
            letter-spacing:1px;
            font-size:12px;
            color:#e68000;
        }
        .form_btn_primary{
            /* height: 100%; */
            box-shadow: 1px 1px 10px rgba(155, 155, 155, 0.226);
            background-color: #e68000;
            border-radius: 5px;
            font-weight: 700;
            color: #f7f7f7;
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
                border-radius:10px;
                padding: 10px;
                background-color: #ffffff75;
                width: 110%;
            }
            .board div.right_board{
                border-left:1px  solid #2c2c2c;
                box-shadow: -10px 0px 10px #2c2c2c;
                background-color: white;
                width:50%;
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
            top:-8px;
            letter-spacing:1px;
            font-size:12px;
            color:#e68000;
        }
        .form_btn_primary{
            /* height: 100%; */
            box-shadow: 1px 1px 10px rgba(155, 155, 155, 0.226);
            background-color: #e68000;
            border-radius: 5px;
            font-weight: 700;
            color: #f7f7f7;
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
                border-radius:10px;
                padding: 10px;
                background-color: #ffffff75;
                width: 110%;
            }
            .board div.right_board{
                border-left:1px  solid #2c2c2c;
                box-shadow: -10px 0px 10px #2c2c2c;
                background-color: white;
                width:50%;
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
            top:-8px;
            letter-spacing:1px;
            font-size:12px;
            color:#e68000;
        }
        .form_btn_primary{
            /* height: 100%; */
            box-shadow: 1px 1px 10px rgba(155, 155, 155, 0.226);
            background-color: #e68000;
            border-radius: 5px;
            font-weight: 700;
            color: #f7f7f7;
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
             
            if(request()->session()->get('redirect_admin')){
                $domain_url  = request()->session()->get('redirect_admin.domain_url'); 
                $database    = request()->session()->get('redirect_admin.database'); 
                $domain      = request()->session()->get('redirect_admin.domain');
                $domain_name = "https://".session()->get('redirect_admin.domain').".izocloud.com";
                $domain_name = $domain_name??"";
                $text        = "email=".request()->session()->get("login_info.email")."_##password=".request()->session()->get("login_info.password")."_##logoutOther=".request()->session()->get("login_info.logoutOther")."_##administrator=1_##database=".$database."_##adminDatabaseUser=".$database."_##domain=".$domain."_##domain_url=".$domain_url."_##redirect=admin";
                
            }else{
                $domain_name = "https://".session()->get('user_main.domain').".izocloud.com";
                $domain_name = $domain_name??"";
                $text        = "email=".request()->session()->get("login_info.email")."_##password=".request()->session()->get("login_info.password")."_##logoutOther=".request()->session()->get("login_info.logoutOther")."_##redirect=normal";
            }
            
            $text        =  Illuminate\Support\Facades\Crypt::encryptString($text);
            $url         = $domain_name."/login-account-redirect"."/".$text;
    @endphp 
      <form hidden action="{{$url}}" id="go-home" method="GET">
        <button id="go_home"  type="submit">Go Home</button>
    </form>
    
    <body class="contents font_style">
        @if(isset($login_user))
            <div class="loading2">
                <div class="loading-content">
                    <h1 class="text-center">
                        <img class="logo-style"   style="width:200px !important" height=75 src="{{asset('logo-white.png')}}" alt="logo">
                        <br>
                        <small>{!!__('izo.waiting')!!}</small>
                    </h1>
                </div>
            </div>
        @endif
        {{-- center --}}
        <div class="container mainbox">
            <div class="row childbox">
                <div class="rightChild">  
                    <div class="board">
                        <div class="right_board">
                            <div class="row font_style">
                                <div class="col-12"></div>
                                <div class="col-12"><h1>&nbsp;</h1></div>
                                <div class="col-12 text-center">
                                    <input type="hidden" name="email" id="email" value="{{request()->session()->get("login_info.email")}}">
                                    <input type="hidden" name="password" id="password" value="{{request()->session()->get("login_info.password")}}">
                                    <img src="../../../uploads/footerLogo.png"  height="100px"  style="width:50%"> 
                                    <h1 class="p-10 font_style"><b>@lang('WELCOME TO IZOCLOUD')</b></h1>
                                    <h5 class="font_style"><b>@lang(' &nbsp;&nbsp;&nbsp; Now You can Manage Your Company<br> ')</br></h5>
                                        <div class="col-12 text-center p-10"> 
                                            {!! "<a  id='BOX_DOMAIN_url' href='".$domain_name."?email=".request()->session()->get("login_info.email")."&password=".request()->session()->get("login_info.password")."'><div class='BOX_DOMAIN'>".$domain_name."</div></a>"  !!}
                                        </div>
                                </div>
                                  <a class="logout" href="/account/logout">LOGOUT</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
       
    
        <script type="text/javascript">
                
            function next(){
                id = $('#current_active').attr('value');
                if(parseFloat(id)+1>4){
                    id = 1;
                }else{
                    id = parseFloat(id)+1
                }
                $('#current_active').attr('value',id);
                viewPlatform(id);
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
                var id_finish = ($(".type_of_job").hasClass("hide"))?3:4;
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
                    if(id == 4){
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
            setTimeout(() => {
                @if(isset($login_user))
                    $("form#go-home").submit(); 
                @endif
            },1000);
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
            }, 100);
            // ..................
            setInterval(() => { 
                id = $('#side').val();
                id = ((parseFloat(id)+1)>4)?1:(parseFloat(id) +1); 
                
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
    </body>  
@endsection