<!DOCTYPE html>

<html lang="{{ session()->get('user.language', config('app.locale')) }}">
 
<head>
    <!-- PWA  -->
    <meta name="theme-color" content="#e68000"/>
    <link rel="apple-touch-icon" href="{{ asset('logo.png') }}">
    <link rel="manifest" href="{{ asset('/manifest.json') }}"> 
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/x-icon" href="{{ asset('/public/uploads/POS.ico') }}">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="author" content="IZOCLOUD">
    <meta property="og:type" content="website"/>
    <meta property="og:title" content="IZOCLOUD - برنامج المحاسبة لإدارة الأنشطة التجارية"/>
    <meta property="og:site_name" content="IZOCLOUD "/>
    <meta property="og:image" content="http://test.izocloud.com/uploads/IZO-D1.jpg"/>
    <meta property="og:description" content=" مرحبا   نحن نعم علي بناء و تطوير المواقع وبرامج سطح المكتب"/>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/css/flag-icon.min.css">
    <meta name="keywords" content="صيدليات ,
		مخازن , مخزون , مستودعات , حسابات , مشتريات , مبيعات , عملاء , موردين , عملاء وموردين , محلات ، إدارة محلات ،
		برنامج مخازن , برنامج حسابات , برنامج مستودعات , برنامج مشتريات , برنامج عملاء , برنامج موردين , برنامج عملاء وموردين , برنامج محلات , برنامج مخزون , إدارة مستودعات ،برنامج محلات ،برنامج مخازن مجانى ,
		برنامج المخازن , برنامج الحسابات , برنامج المستودعات , برنامج المشتريات , برنامج العملاء , برنامج الموردين , برنامج العملاء والموردين ، برنامج المحلات , برنامج المخزون , برنامج إدارة المستودعات ، برنامج المحلات ،برنامج للمخازن مجانى ,
		برنامج للمخازن , برنامج للحسابات , برنامج للمستودعات , برنامج للمشتريات , برنامج للعملاء , برنامج للموردين , برنامج للعملاء والموردين ، برنامج للمحلات , برنامج للمخزون , برنامج لإدارة المستودعات ، برنامج للمحلات ،
		برنامج عربى ,
		justagain , pharmacy , drugs , ERP , store , customers , clients , suppliers , sales , stores , store , point of sale , pos , pos system , supermarket system ,
		" />
    <title>@yield('title',"login-first-time") - {{ config('app.name', 'POS') }}</title>
    <script src="https://www.google.com/recaptcha/enterprise.js" async defer></script>
    @include('layouts.partials.css')
    @yield('app_css')
    
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
      .status_box{
        top:30px;
        z-index: 5000;
        padding: 20px;
        right:40px;
        border-radius: 10px;
        position: absolute;
        background-color: green;
        color: white;
        font-weight: 700;
        font-size: 20px;
      }
      .status_box_wrong{
        top:30px;
        z-index: 5000;
        padding: 20px;
        right:40px;
        border-radius: 10px;
        position: absolute;
        background-color: #ff3333;
        color: white;
        font-weight: 700;
        font-size: 20px;
      }
    </style>
</head>
 
@if(request()->session()->get('status'))
  <div @if(request()->session()->get('status.success') == 1) class="status_box" @else  class="status_box_wrong" @endif>
      {{ request()->session()->get('status.msg')}}
  </div>
@endif
@yield('content')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
@yield('javascript')
<script type="text/javascript">
  $(document).ready(function(){
    @if(request()->session()->get('status'))
    setTimeout(() => {
          $('.status_box').remove();
          $('.status_box_wrong').remove();
        }, 5000);
        @endif
      });
        setInterval(function() {
            $('meta[name="csrf-token"]').attr('content', '{{ csrf_token() }}');
        }, 300); 
    </script>

</html>
  