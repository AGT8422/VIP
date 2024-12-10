@inject('request', 'Illuminate\Http\Request')
@php
    $right =  in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) ? '0%' : 'initial'  ;
    $left  =  in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) ? 'initial' :  '0%' ;
    $right_top  =  in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) ? '10px' :  'initial' ;
    $left_top  =  in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) ? 'initial' :  '10px' ;
@endphp
<style>
      .sec{
        /* position: fixed; */
        /* overflow: scroll; */
        padding-top: 100px;
      }
      /* width */
      ::-webkit-scrollbar {
        width: 5px;
      }

      /* Track */
      ::-webkit-scrollbar-track {
        background: #f1f1f1;
      }

      /* Handle */
      ::-webkit-scrollbar-thumb {
        background: #88888800;
        border-radius: 20px; 
      }

      /* Handle on hover */
      ::-webkit-scrollbar-thumb:hover {
        background: #ec6608;
      }

       /* .treeview:hover{
      border:10px solid black !important;
    } */
    .sidebar-menu>li>a{
      padding:3% 10% ;
      font-size: 18px;
    }
    .sidebar-menu>li>a>.fa{
      width : 25px;
      margin-right: 20px;
      font-size: 20px;
    }

    .skin-black-light .sidebar-menu>li.active>a,
    .skin-black-light .sidebar-menu .treeview-menu>li.active>a{
      color: #ec6608 !important
    }
    .skin-black-light .sidebar-menu>li:hover>a{
      /* border-radius:100px !important;  */
      outline:1px solid #3a3a3a33 !important; 
      /* padding: 15px !important;  */
      /* border-radius: 100px !important; */
      /* background-color: purple !important; */
    }
    .skin-black-light .sidebar-menu>li:hover>span{
      /* background-color: purple !important; */
    }
    .sidebar-menu>li a::before{
      display: none !important;
    }
    .sidebar-menu>li a{
      margin:5px auto !important;

    }
    .sidebar-menu>li:hover a{
      /* background-color: blue !important; */
      /* margin:5px auto !important; */
    }
    .sidebar-menu>li:hover{
      background-color: red;
      /* border-radius: 100px !important; */
      /* border:1px solid #3a3a3a33 !important; */
      margin: 10px 5px  !important; 
    }
    .skin-black-light .sidebar-menu .treeview-menu{
      border-radius: 10px !important;
      margin: 10px !important;
    }
    .skin-black-light .sidebar-menu>.active:hover {

      background-color: #00000000 !important;
    }
    .skin-black-light .sidebar-menu>.active {
      background-color: #00000000 !important;
      border:0px solid black !important;
      border-radius:20px !important;
      /* padding: 10px 1px  !important; */
    }
    .skin-black-light .sidebar-menu>.active a{
      /* border-top-right-radius:20px !important; 
      border-top-left-radius:20px !important;  */
      border-left:0px solid transparent !important;
      /* background-color: red !important; */
     }
      .sidebar-menu>li a{
        color:black !important; 
      }
      .sidebar-menu>li{
        /* background-color: red; */
        /* border-radius: 100px; */
        /* border:1px solid #3a3a3a33; */
        margin: 10px 5px  ; 
      }
      .sidebar-menu .treeview{
        /* padding-bottom:10px !important;  */
      }
      .sidebar-menu .treeview:hover{
        border:0px solid black !important;
        background-color: #3a3a3a00 !important;
        /* padding-bottom:10px !important;  */
      }
      .sidebar-menu .treeview .active{
        /* background-color:blue; */
      }
      .title_side_bar h1 small{
        font-size: 15px;
        position:absolute;
        margin-top:-5px;
      }
      .title_side_bar small{
        color: white !important;
      }
      .logo-lg{
        padding-left:10px ;
        border-left:3px solid #ec6608;
        font-size:14px ;
        position: relative;
        top:-20px;
      }
      .title_side_bar a{
        color:white !important; 
      }
      .title_side_bar img{
        margin: 15px auto  ;
        width: 30%;
      }
      .title_side_bar{
        right:{{$right_top}};
        position: fixed;
        top: 10px;
        left:{{$left_top}};
        border-radius:10px ;
        width: 14.6%;
        z-index: 100;
        box-shadow: -10px 0px 20px #3a3a3a33;
        height: 70px;
        color: #fff;
        background-color: #000; 
        padding: 5px 20px 0px 0px ;
        text-align: center;
      }
      .treeview-menu{
        /* margin: -5px 20px 10px 20px !important; */
        /* border-radius: 0px !important; */
        /* border-bottom-left-radius: 20px !important;
        border-bottom-right-radius: 20px !important; */
      }
          .fa-angle-left{
      color: #ec6608 !important;
      display: none;
    }
    .fa-angle-right{
      color: #ec6608 !important;
      display: none;
    }

    .skin-black-light .sidebar-men:hover>li>a{
      display: none !important;
    }    
      .main-sidebar-new{
        overflow-y: scroll;
        padding: 100px 10px 20px 0px;
          position: fixed;
          z-index: 1001;
          left: {{$left}};
          right: {{$right}};
          top: 00px;
          width:16%;
          box-shadow: 0px 0px 10px #3a3a3a33;
          background-color: #fff;
          height: 100%;
          border:1px solid #3a3a3a33;
      }
  @media (max-width: 600px) {
     /* .treeview:hover{
      border:10px solid black !important;
    } */
    .sidebar-menu>li>a{
      padding:3% 10% ;
      font-size: 18px;
    }
    .sidebar-menu>li>a>.fa{
      width : 25px;
      margin-right: 20px;
      font-size: 20px;
    }

    .skin-black-light .sidebar-menu>li.active>a,
    .skin-black-light .sidebar-menu .treeview-menu>li.active>a{
      color: #ec6608 !important
    }
    .skin-black-light .sidebar-menu>li:hover>a{
      /* border-radius:100px !important;  */
    outline:1px solid #3a3a3a33 !important; 
      /* padding: 15px !important;  */
      /* border-radius: 100px !important; */
      /* background-color: purple !important; */
    }
    .skin-black-light .sidebar-menu>li:hover>span{
      /* background-color: purple !important; */
    }
    .sidebar-menu>li a::before{
      display: none !important;
    }
    .sidebar-menu>li a{
      margin:5px auto !important;

    }
    .sidebar-menu>li:hover a{
      /* background-color: blue !important; */
      /* margin:5px auto !important; */
    }
    .sidebar-menu>li:hover{
      background-color: red;
      /* border-radius: 100px !important; */
      /* border:1px solid #3a3a3a33 !important; */
      margin: 10px 5px  !important; 
    }
    .skin-black-light .sidebar-menu .treeview-menu{
      border-radius: 10px !important;
      margin: 10px !important;
    }
    .skin-black-light .sidebar-menu>.active:hover {

      background-color: #00000000 !important;
    }
    .skin-black-light .sidebar-menu>.active {
      background-color: #00000000 !important;
      border:0px solid black !important;
      border-radius:20px !important;
      /* padding: 10px 1px  !important; */
    }
    .skin-black-light .sidebar-menu>.active a{
      /* border-top-right-radius:20px !important; 
      border-top-left-radius:20px !important;  */
      border-left:0px solid transparent !important;
      /* background-color: red !important; */
     }
    .sidebar-menu>li a{
      color:black !important; 
    }
    .sidebar-menu>li{
      /* background-color: red; */
      /* border-radius: 100px; */
      /* border:1px solid #3a3a3a33; */
      margin: 10px 5px  ; 
    }
    .sidebar-menu .treeview{
      /* padding-bottom:10px !important;  */
    }
    .sidebar-menu .treeview:hover{
      border:0px solid black !important;
      background-color: #3a3a3a00 !important;
      /* padding-bottom:10px !important;  */
    }
    .sidebar-menu .treeview .active{
      /* background-color:blue; */
    }
    .title_side_bar h1 small{
      font-size: 15px;
      position:absolute;
      margin-top:-5px;
    }
    .title_side_bar small{
      color: white !important;
    }
    .logo-lg{
      padding-left:10px ;
      border-left:3px solid #ec6608;
      font-size:14px ;
      position: relative;
      top:-20px;
    }
    .title_side_bar a{
      color:white !important; 
    }
    .title_side_bar img{
      margin: 15px auto  ;
      width: 30%;
    }
    .title_side_bar{
      right:{{$right_top}};
      position: fixed;
      top: 10px;
      left:{{$left_top}};
      border-radius:10px ;
      width: 14.6%;
      z-index: 100;
      box-shadow: -10px 0px 20px #3a3a3a33;
      height: 70px;
      color: #fff;
      background-color: #000; 
      padding: 5px 20px 0px 0px ;
      text-align: center;
    }  
    .treeview-menu{
      /* margin: -5px 20px 10px 20px !important; */
      /* border-radius: 0px !important; */
      /* border-bottom-left-radius: 20px !important;
      border-bottom-right-radius: 20px !important; */
    }
   .fa-angle-left{
      color: #ec6608 !important;
      display: none;
    }
    .fa-angle-right{
      color: #ec6608 !important;
      display: none;
    }
    .sidebar-menu>li>a{
      padding:3% 10% ;
      font-size: 18px;
    }
    .sidebar-menu>li>a>.fa{
      width : 25px;
      margin-right: 20px;
      font-size: 20px;
    }

    .skin-black-light .sidebar-menu>li.active>a,
    .skin-black-light .sidebar-menu .treeview-menu>li.active>a{
      color: #ec6608 !important
    }
    .skin-black-light .sidebar-menu>li:hover>a{
      /* display: none !important; */
    outline:1px solid #3a3a3a33 !important; 
      /* padding: 15px !important;  */
      /* border-radius: 100px !important; */
    }
    .main-sidebar-new{
      overflow-y: scroll;
        padding: 100px 10px 20px 0px;
          position :fixed;
          z-index: 1001;
          left: {{$left}};
          right: {{$right}};
          top: 00px;
          width:16%;
          box-shadow: 0px 0px 10px #3a3a3a33;
          background-color: #fff;
          height: 100%;
          border:1px solid #3a3a3a33;
      }
  }
  @media (min-width: 600px)  and  (max-width: 900px) {
     /* .treeview:hover{
      border:10px solid black !important;
    } */

    .sidebar-menu>li>a{
      padding:3% 10% ;
      font-size: 18px;
    }
    .sidebar-menu>li>a>.fa{
      width : 25px;
      margin-right: 20px;
      font-size: 20px;
    }

    .skin-black-light .sidebar-menu>li.active>a,
    .skin-black-light .sidebar-menu .treeview-menu>li.active>a{
      color: #ec6608 !important
    }
    .skin-black-light .sidebar-menu>li:hover>a{
      /* border-radius:100px !important;  */
    outline:1px solid #3a3a3a33 !important; 
      /* padding: 15px !important;  */
      /* border-radius: 100px !important; */
      /* background-color: purple !important; */
    }
    .skin-black-light .sidebar-menu>li:hover>span{
      /* background-color: purple !important; */
    }
    .sidebar-menu>li a::before{
      display: none !important;
    }
    .sidebar-menu>li a{
      margin:5px auto !important;

    }
    .sidebar-menu>li:hover a{
      /* background-color: blue !important; */
      /* margin:5px auto !important; */
    }
    .sidebar-menu>li:hover{
              background-color: red;
        /* border-radius: 100px !important; */
        /* border:1px solid #3a3a3a33 !important; */
        margin: 10px 5px  !important; 
    }
    .sidebar-menu>li a{
      color:black !important; 
    }
    .sidebar-menu>li{
      /* background-color: red; */
      /* border-radius: 100px; */
      /* border:1px solid #3a3a3a33; */
      margin: 10px 5px  ; 
    }
    .skin-black-light .sidebar-menu .treeview-menu{
      border-radius: 10px !important;
      margin: 10px !important;
    }
    .skin-black-light .sidebar-menu>.active:hover {

      background-color: #00000000 !important;
    }
    .skin-black-light .sidebar-menu>.active {
      background-color: #00000000 !important;
      border:0px solid black !important;
      border-radius:20px !important;
      /* padding: 10px 1px  !important; */
    }
    .skin-black-light .sidebar-menu>.active a{
      /* border-top-right-radius:20px !important; 
      border-top-left-radius:20px !important;  */
      border-left:0px solid transparent !important;
      /* background-color: red !important; */
     }
    .sidebar-menu .treeview{
      /* padding-bottom:10px !important;  */
    }
    .sidebar-menu .treeview:hover{
      border:0px solid black !important;
      background-color: #3a3a3a00 !important;
      /* padding-bottom:10px !important;  */
    }
    .sidebar-menu .treeview .active{
      /* background-color:blue; */
    }
    .title_side_bar h1 small{
      font-size: 15px;
      position:absolute;
      margin-top:-5px;
    }
    .title_side_bar small{
      color: white !important;
    }
    .logo-lg{
      padding-left:10px ;
      border-left:3px solid #ec6608;
      font-size:14px ;
      position: relative;
      top:-20px;
    }
    .title_side_bar a{
      color:white !important; 
    }
    .title_side_bar img{
      margin: 15px auto  ;
      width: 30%;
    }
    .title_side_bar{
      right:{{$right_top}};
      position: fixed;
      top: 10px;
      left:{{$left_top}};
      border-radius:10px ;
      width: 14.6%;
      z-index: 100;
      box-shadow: -10px 0px 20px #3a3a3a33;
      height: 70px;
      color: #fff;
      background-color: #000; 
      padding: 5px 20px 0px 0px ;
      text-align: center;
    }  
    .treeview-menu{
      /* margin: -5px 20px 10px 20px !important; */
      /* border-radius: 0px !important; */
      /* border-bottom-left-radius: 20px !important;
      border-bottom-right-radius: 20px !important; */
    }
        .fa-angle-left{
      color: #ec6608 !important;
      display: none;
    }
    .fa-angle-right{
      color: #ec6608 !important;
      display: none;
    }
    .sidebar-menu>li>a{
      padding:3% 10% ;
      font-size: 18px;
    }
    .sidebar-menu>li>a>.fa{
      width : 25px;
      margin-right: 20px;
      font-size: 20px;
    }

    .skin-black-light .sidebar-menu>li.active>a,
    .skin-black-light .sidebar-menu .treeview-menu>li.active>a{
      color: #ec6608 !important
    }
    .skin-black-light .sidebar-menu>li:hover>a{
      /* display: none !important; */
    outline:1px solid #3a3a3a33 !important; 
      /* padding: 15px !important;  */
      /* border-radius: 100px !important; */
    }
    .main-sidebar-new{
      overflow-y: scroll;
        padding: 100px 10px 20px 0px;
          position:fixed;
          z-index: 1001;
          left: {{$left}};
          right: {{$right}};
          top: 00px;
          width:16%;
          box-shadow: 0px 0px 10px #3a3a3a33;
          background-color: #fff;
          height: 100%;
          border:1px solid #3a3a3a33;
      }
  }
  @media (min-width: 1024px) and  (max-width:1400px) {
     /* .treeview:hover{
      border:10px solid black !important;
    } */
    .sidebar-menu>li>a{
      padding:3% 10% ;
      font-size: 18px;
    }
    .sidebar-menu>li>a>.fa{
      width : 25px;
      margin-right: 20px;
      font-size: 20px;
    }

    .skin-black-light .sidebar-menu>li.active>a,
    .skin-black-light .sidebar-menu .treeview-menu>li.active>a{
      color: #ec6608 !important
    }
    .skin-black-light .sidebar-menu>li:hover>a{
      /* border-radius:100px !important;  */
    outline:1px solid #3a3a3a33 !important; 
      /* padding: 15px !important;  */
      /* border-radius: 100px !important; */
      /* background-color: purple !important; */
    }
    .skin-black-light .sidebar-menu>li:hover>span{
      /* background-color: purple !important; */
    }
    .sidebar-menu>li a::before{
      display: none !important;
    }
    .sidebar-menu>li a{
      margin:5px auto !important;

    }
    .sidebar-menu>li:hover a{
      /* background-color: blue !important; */
      /* margin:5px auto !important; */
    }
    .sidebar-menu>li:hover{
      background-color: red;
      /* border-radius: 100px  !important; */
      /* border:1px solid #3a3a3a33 !important; */
      margin: 10px 5px  !important; 
    }
    .skin-black-light .sidebar-menu .treeview-menu{
      border-radius: 10px !important;
      margin: 10px !important;
    }
    .skin-black-light .sidebar-menu>.active:hover {

      background-color: #00000000 !important;
    }
    .skin-black-light .sidebar-menu>.active {
      background-color: #00000000 !important;
      border:0px solid black !important;
      border-radius:20px !important;
      /* padding: 10px 1px  !important; */
    }
    .skin-black-light .sidebar-menu>.active a{
      /* border-top-right-radius:20px !important; 
      border-top-left-radius:20px !important;  */
      border-left:0px solid transparent !important;
      /* background-color: red !important; */
     }
    .sidebar-menu>li a{
      color:black !important; 
    }
    .sidebar-menu>li{
      /* background-color: red; */
      /* border-radius: 100px; */
      /* border:1px solid #3a3a3a33; */
      margin: 10px 5px  ; 
    }
    .sidebar-menu .treeview{
      /* padding-bottom:10px !important;  */
    }
    .sidebar-menu .treeview:hover{
      border:0px solid black !important;
      background-color: #3a3a3a00 !important;
      /* padding-bottom:10px !important;  */
    }
    .sidebar-menu .treeview .active{
      /* background-color:blue; */
    }
    .title_side_bar h1 small{
      font-size: 15px;
      position:absolute;
      margin-top:-5px;
    }
    .title_side_bar small{
      color: white !important;
    }
    .logo-lg{
      padding-left:10px ;
      border-left:3px solid #ec6608;
      font-size:14px ;
      position: relative;
      top:-20px;
    }
    .title_side_bar a{
      color:white !important; 
    }
    .title_side_bar img{
      margin: 15px auto  ;
      width: 30%;
    }
    .title_side_bar{
      right:{{$right_top}};
      position: fixed;
      top: 10px;
      left:{{$left_top}};
      border-radius:10px ;
      width: 14.6%;
      z-index: 100;
      box-shadow: -10px 0px 20px #3a3a3a33;
      height: 70px;
      color: #fff;
      background-color: #000; 
      padding: 5px 20px 0px 0px ;
      text-align: center;
    }  
    .treeview-menu{
      /* margin: -5px 20px 10px 20px !important; */
      /* border-radius: 0px !important; */
      /* border-bottom-left-radius: 20px !important;
      border-bottom-right-radius: 20px !important; */
    }
        .fa-angle-left{
      color: #ec6608 !important;
      display: none;
    }
    .fa-angle-right{
      color: #ec6608 !important;
      display: none;
    }
    .sidebar-menu>li>a{
      padding:3% 10% ;
      font-size: 18px;
    }
    .sidebar-menu>li>a>.fa{
      width : 25px;
      margin-right: 20px;
      font-size: 20px;
    }

    .skin-black-light .sidebar-menu>li.active>a,
    .skin-black-light .sidebar-menu .treeview-menu>li.active>a{
      color: #ec6608 !important
    }
    .skin-black-light .sidebar-menu>li:hover>a{
      /* display: none !important; */
    outline:1px solid #3a3a3a33 !important; 
      /* padding: 15px !important;  */
      /* border-radius: 100px !important; */
    }
    .main-sidebar-new{
      overflow-y: scroll;
        padding: 100px 10px 20px 0px;
          position:fixed;
          z-index: 1001;
          left: {{$left}};
          right: {{$right}};
          top: 00px;
          width:16%;
          box-shadow: 0px 0px 10px #3a3a3a33;
          background-color: #fff;
          height: 100%;
          border:1px solid #3a3a3a33;
      }
  }
  @media (min-width: 900px)  and  (max-width: 1024px){
    .sidebar-menu>li:hover{
              background-color: red;
        /* border-radius: 100px  !important; */
        /* border:1px solid #3a3a3a33 !important; */
        margin: 10px 5px  !important; 
    }
    .sidebar-menu>li a{
      color:black !important; 
    }
    .sidebar-menu>li{
      /* background-color: red; */
      /* border-radius: 100px; */
      /* border:1px solid #3a3a3a33; */
      margin: 10px 5px  ; 
    }
    .sidebar-menu .treeview{
      /* padding-bottom:10px !important;  */
    }
    .sidebar-menu .treeview:hover{
      border:0px solid black !important;
      background-color: #3a3a3a00 !important;
      /* padding-bottom:10px !important;  */
    }
    .sidebar-menu .treeview .active{
      /* background-color:blue; */
    }
    .title_side_bar h1 small{
      font-size: 15px;
      position:absolute;
      margin-top:-5px;
    }
    .title_side_bar small{
      color: white !important;
    }
    .logo-lg{
      padding-left:10px ;
      border-left:3px solid #ec6608;
      font-size:14px ;
      position: relative;
      top:-20px;
    }
    .title_side_bar a{
      color:white !important; 
    }
    .title_side_bar img{
      margin: 15px auto  ;
      width: 30%;
    }
    .title_side_bar{
      right:{{$right_top}};
      position: fixed;
      top: 10px;
      left:{{$left_top}};
      border-radius:10px ;
      width: 14.6%;
      z-index: 100;
      box-shadow: -10px 0px 20px #3a3a3a33;
      height: 70px;
      color: #fff;
      background-color: #000; 
      padding: 5px 20px 0px 0px ;
      text-align: center;
    }  
    .treeview-menu{
      /* margin: -5px 20px 10px 20px !important; */
      /* border-radius: 0px !important; */
      /* border-bottom-left-radius: 20px !important;
      border-bottom-right-radius: 20px !important; */
    }
        .fa-angle-left{
      color: #ec6608 !important;
      display: none;
    }
    .fa-angle-right{
      color: #ec6608 !important;
      display: none;
    }
    .sidebar-menu>li>a{
      padding:3% 10% ;
      font-size: 18px;
    }
    .sidebar-menu>li>a>.fa{
      width : 25px;
      margin-right: 20px;
      font-size: 20px;
    }

    .skin-black-light .sidebar-menu>li.active>a,
    .skin-black-light .sidebar-menu .treeview-menu>li.active>a{
      color: #ec6608 !important
    }
    .skin-black-light .sidebar-menu>li:hover>a{
      /* display: none !important; */
    outline:1px solid #3a3a3a33 !important; 
      /* padding: 15px !important;  */
      /* border-radius: 100px !important; */
    }
    .main-sidebar-new{
      overflow-y: scroll;
        padding: 100px 10px 20px 0px;
          position :fixed;
          z-index: 1001;
          left: {{$left}};
          right: {{$right}};
          top: 00px;
          width:16%;
          box-shadow: 0px 0px 10px #3a3a3a33;
          background-color: #fff;
          height: 100%;
          border:1px solid #3a3a3a33;
      }
  }

</style>
 

<!-- <span class="logo-lg">{{"The Future Is Here"  }}</span> Left side column. contains the logo and sidebar -->
<aside class="main-sidebar-new"  >
  <div class="title_side_bar">
    <img class="logo-style" src="{{asset('logo-white.png')}}" alt="logo">
    
  </div>
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar-new">

    {!! Menu::render('admin-sidebar-menu', 'adminltecustom'); !!}

  </section>
  <!-- /.sidebar -->
</aside>
 
 