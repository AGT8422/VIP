<!DOCTYPE html>
<html lang="{{ session()->get('user.language', config('app.locale')) }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Module Manufacturing</title>

       {{-- Laravel Mix - CSS File --}}
       {{-- <link rel="stylesheet" href="{{ mix('css/manufacturing.css') }}"> --}}

    </head>
    <body>
        @yield('content')

        {{-- Laravel Mix - JS File --}}
        {{-- <script src="{{ mix('js/manufacturing.js') }}"></script> --}}
    </body>
</html>
