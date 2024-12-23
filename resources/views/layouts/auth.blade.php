<!DOCTYPE html>
<html lang="{{ session()->get('user.language', config('app.locale')) }}">
<html>
<head>
    <!-- PWA  -->
    <meta name="theme-color" content="#e68000"/>
    <link rel="apple-touch-icon" href="{{ asset('logo.png') }}">
    <link rel="manifest" href="{{ asset('/manifest.json') }}"> 
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name', 'POS') }}</title> 

    @include('layouts.partials.css')

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
            body {
                background-color: #243949;
            }
            h1 {
                color: #fff;
            }
    </style>
</head>

<body class="hold-transition">
    @if (session('status'))
        <input type="hidden" id="status_span" data-status="{{ session('status.success') }}" data-msg="{{ session('status.msg') }}">
    @endif

    @if(!isset($no_header))
        @include('layouts.partials.header-auth')
    @endif

    @yield('content')
    
    @include('layouts.partials.javascripts')
    <!-- Scripts -->
    <script src="{{ asset('js/login.js?v=' . $asset_v) }}"></script>
    @yield('javascript')

    <script type="text/javascript">
        $(document).ready(function(){
            $('.select2_register').select2();

            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' // optional
            });
        });
    </script>
    <script src="{{ asset('/sw.js') }}"></script>
    <script>
       if ("serviceWorker" in navigator) {
          // Register a service worker hosted at the root of the
          // site using the default scope.
          navigator.serviceWorker.register("/sw.js").then(
          (registration) => {
            //  console.log("Service worker registration succeeded:", registration);
          },
          (error) => {
            //  console.error(`Service worker registration failed: ${error}`);
          },
        );
      } else {
        //  console.error("Service workers are not supported.");
      }
    </script>
</body>

</html>