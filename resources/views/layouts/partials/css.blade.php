<link rel="stylesheet" href="{{ asset('css/vendor.css?v=' . $asset_v) }}">

@if( in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) )
	<link rel="stylesheet" href="{{ asset('css/rtl.css?v=' . $asset_v) }}">
@endif

@yield('css')
@php
	$float_btn     = (in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl'))) ? 'left' : 'right';
	$border_right  = (in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl'))) ? '0px' : '3px';
	$border_left   = (in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl'))) ? '3px' : '0px';
@endphp
<!-- app css -->
<link rel="stylesheet" href="{{ asset('css/app.css?v='.$asset_v) }}">

@if(isset($pos_layout) && $pos_layout)
	<style type="text/css">
		.content{
			padding-bottom: 0px !important;
		}
	</style>
@endif
<style type="text/css">
	/*
	* Pattern lock css
	* Pattern direction
	* http://ignitersworld.com/lab/patternLock.html
	*/
	.loading{
                position: fixed;
                left: 0px;
                right: 0px;
                width: 100%;
                height: 100%;
                z-index: 2000;
                background-color: rgb(0, 0, 0);
            }
            .loading .loading-content{
                position: relative; 
                margin: 50vh auto; 
                transform: translateX(-50%);
                transform: translateY(-50%);
                width: 200px;
                height: 100px;
                z-index: 2000;
                color: #fefefe;
                font-size: 20px;
                font-weight: 700;
                background-color: rgba(175, 28, 28, 0);
            }
            .loading .loading-content h1{ 
                color: #fefefe;  
                font-weight: bold;
            }
	.patt-wrap {
	  z-index: 10;
	}
	.patt-circ.hovered {
	  background-color: #cde2f2;
	  border: none;
	}
	.patt-circ.hovered .patt-dots {
	  display: none;
	}
	.patt-circ.dir {
	  background-image: url("{{asset('/img/pattern-directionicon-arrow.png')}}");
	  background-position: center;
	  background-repeat: no-repeat;
	}
	.patt-circ.e {
	  -webkit-transform: rotate(0);
	  transform: rotate(0);
	}
	.patt-circ.s-e {
	  -webkit-transform: rotate(45deg);
	  transform: rotate(45deg);
	}
	.patt-circ.s {
	  -webkit-transform: rotate(90deg);
	  transform: rotate(90deg);
	}
	.patt-circ.s-w {
	  -webkit-transform: rotate(135deg);
	  transform: rotate(135deg);
	}
	.patt-circ.w {
	  -webkit-transform: rotate(180deg);
	  transform: rotate(180deg);
	}
	.patt-circ.n-w {
	  -webkit-transform: rotate(225deg);
	   transform: rotate(225deg);
	}
	.patt-circ.n {
	  -webkit-transform: rotate(270deg);
	  transform: rotate(270deg);
	}
	.patt-circ.n-e {
	  -webkit-transform: rotate(315deg);
	  transform: rotate(315deg);
	}
	#inside-content{
		/* position: fixed; */
		padding-bottom:100px !important;
		overflow-y: scroll;
		height: 100vh !important;
		width: 84% !important;
	}
	a{
		color:#ec6808 !important;
	}
	.btn-search{

		background-color: #ec6808 !important;
		border:1px solid #ec6808 !important;
	}
	.btn-primary{
		color:white !important;
		background-color: #ec6808 !important;
		border:1px solid #ec6808 !important;
	}
	.btn-success{
		color:white !important;
		 
	}
	.box.box-primary{
		box-shadow: 0px 0px 10px #3a3a3a33 !important;
		border-top-color:transparent !important;
		border-top:0px solid transparent !important;
		border-left:{{$border_left}} solid transparent !important;
		border-right:{{$border_right}} solid transparent !important;
	}
	.content-header h5 b{
		cursor: pointer !important;
		color:#ec6808 !important;
	}
	.table tfoot tr th,
	.table tfoot tr td{
		font-family:Georgia, 'Times New Roman', Times, serif !important;
	}
	.table thead tr th{
		font-family:Georgia, 'Times New Roman', Times, serif !important;
		background-color: #353535fd !important;
		border:1px solid #00000033 !important;
		border-top:1px solid #3a3a3a33 !important;
		border-bottom:1px solid #3a3a3a33 !important;
		color: #ffffff !important;  
	}
	.nav-tabs-custom{
		
		box-shadow: 0px 0px 10px #3a3a3a33 !important;
	}
	.icheckbox_square-blue.checked{
		border: 1px solid #ee680e !important;
		color:   #ee680e !important;
	}
	.text-info {
		color:   #ee680e !important;
	}
	.D_rang{
		font-size: 13px !important;
	}
	.box-tools{
		float: {{$float_btn}} !important;
	}
</style>
@if(!empty($__system_settings['additional_css']))
    {!! $__system_settings['additional_css'] !!}
@endif