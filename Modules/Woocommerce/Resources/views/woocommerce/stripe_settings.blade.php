@extends('layouts.app')
@section('title', __('woocommerce::lang.stripe_settings'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('woocommerce::lang.stripe_settings')</h1>
</section>

<!-- Main content -->
<section class="content">
     <div class="row">
        <div class="col-xs-12" style="padding:10px;  max-height:auto; ">
           
            {{-- *********3************ --}}
            @component('components.widget',['class'=>' sections box-primary',"title"=>__('All Stripe Url')]) 
            <div class="tab">
                <div class="row">
                    <div class="col-lg-12 ">
                        <a class="btn btn-primary pull-right" href="{{action("\Modules\Woocommerce\Http\Controllers\WoocommerceController@editStripeApi" ,  ['id' => 1])}}">@lang('messages.edit')  </a>
                    </div>
                </div>
                <div class="content" style="width:100%">
                    <table class="table table-striped table-bordered dataTable" style="width:100%" id="table_stripe">
                        <thead>
                            <tr>
                                <th class="xl-1"  style="background-color:#00000034">Action</th>
                                <th class="xl-1"  style="background-color:#00000034">Website</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        
                    </table>
                </div>
            </div>
            @endcomponent
         
            
         
           
        </div>
    </div>
   
 
</section>
@stop
@section('javascript')
<script type="text/javascript">
        
        
        table_floating_bar = $('#table_stripe').DataTable({
            processing:true,
            serverSide:true,
            scrollY:  "75vh",
            scrollX:  true,
            scrollCollapse: true, 
            "ajax": {
                "url": "/woocommerce/stripe/all",
                "data": function ( d ) {
                    d.check        = "Top";
                    d              = __datatable_ajax_callback(d);
                }
            },
            columns: [
                { data: 'action', name: 'action'},
                { data: 'url_website'  , name: 'url_website'      },
                
            ],
            fnDrawCallback: function(oSettings) {
                __currency_convert_recursively($('#table_stripe'));
            },
        });
          
      
        

        

</script>
@endsection