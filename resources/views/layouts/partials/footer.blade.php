<!-- Main Footer -->
  <footer class="main-footer no-print" style="@if(session()->get('user.language', config('app.locale'))=='ar') margin-right:16%; @else margin-left:16%;  @endif !important">
    <!-- To the right -->
    <!-- <div class="pull-right hidden-xs">
      Anything you want
    </div> -->
    <!-- Default to the left -->
    <small>
    	{{ config('app.name', 'AZHA Soft POS') }} - V{{config('author.app_version')}} | Copyright &copy; {{ date('Y') }} All rights reserved.
    </small>


    <div class="btn-group pull-right">
      	<button type="button" class="btn btn-success btn-xs toggle-font-size" data-size="s"><i class="fa fa-font"></i> <i class="fa fa-minus"></i></button>
      	<button type="button" class="btn btn-success btn-xs toggle-font-size" data-size="m"> <i class="fa fa-font"></i> </button>
      	<button type="button" class="btn btn-success btn-xs toggle-font-size" data-size="l"><i class="fa fa-font"></i> <i class="fa fa-plus"></i></button>
      	<button type="button" class="btn btn-success btn-xs toggle-font-size" data-size="xl"><i class="fa fa-font"></i> <i class="fa fa-plus"></i><i class="fa fa-plus"></i></button>
    </div>
</footer>