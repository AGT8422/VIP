@extends('layouts.app')
@section('title', __('woocommerce::lang.cart'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('woocommerce::lang.cart')</h1>
</section>

<!-- Main content -->
<section class="content">
     <div class="row">
        <div class="col-xs-12" style="padding:10px;  max-height:auto; ">
           
            {{-- *********3************ --}}
            @component('components.widget',['class'=>' sections box-primary',"title"=>__('All Cart')]) 
            <div class="tab">
                <div class="row">
                    {{-- <div class="col-lg-12 ">
                        <a class="btn btn-primary pull-right" href="{{action("\Modules\Woocommerce\Http\Controllers\WoocommerceController@createFloat" , ['style'=>'about'])}}">@lang('messages.add') <i class="fa fas-fa fa-plus"></i></a>
                    </div> --}}
                </div>
                <div class="content" style="width:100%">
                    <table class="table table-striped table-bordered dataTable" style="width:100%" id="table_cart">
                        <thead>
                            <tr>
                                <th class="xl-1"  style="background-color:#00000034">Action</th>
                                <th class="xl-1"  style="background-color:#00000034">Date</th>
                                <th class="xl-1"  style="background-color:#00000034">Invoice No</th>
                                <th class="xl-1"  style="background-color:#00000034">Final Total</th>
                                <th class="xl-1"  style="background-color:#00000034">Status</th>
                                <th class="xl-1"  style="background-color:#00000034">Payment Status</th>
                                <th class="xl-1"  style="background-color:#00000034">Delivery Status</th>
                                <th class="xl-1"  style="background-color:#00000034">Contact</th>
                                <th class="xl-1"  style="background-color:#00000034">Mobile</th>
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
<!-- /.content -->
<div class="modal fade product_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade payment_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>

@stop
@section('javascript')
 
<script type="text/javascript">
        
        $(document).on('click', '.view_payment_modal', function(e) {
            e.preventDefault();
            var container = $('.payment_modal');

            $.ajax({
                url: $(this).attr('href'),
                dataType: 'html',
                success: function(result) {
                    $(container)
                        .html(result)
                        .modal('show');
                    __currency_convert_recursively(container);
                },
            });
        });
        $(document).ready(function(){
            table_cart = $('#table_cart').DataTable({
                processing:     true,
                serverSide:     true,
                // scrollY:        "75vh",
                // scrollX:        true,
                // scrollCollapse: true, 
                "ajax": {
                    "url" : "/woocommerce/cart/all",
                    "data": function ( d ) { 
                        d = __datatable_ajax_callback(d);
                    }
                },        
                aaSorting: [[1, 'desc']],
                columns: [
                    { data: 'action'            , name: 'action' ,orderable: false, "searchable": false            },
                    { data: 'transaction_date'  , name: 'transaction_date'  },
                    { data: 'invoice_no'        , name: 'invoice_no'        },
                    { data: 'final_total'       , name: 'final_total'       },
                    { data: 'status'            , name: 'status'  },
                    { data: 'payment_status'    , name: 'payment_status'  },
                    { data: 'delivery_status'   , name: 'delivery_status'  },
                    { data: 'contact_id'        , name: 'contact_id'        },
                    { data: 'mobile'            , name: 'mobile'        },
                 ],
                fnDrawCallback: function(oSettings) {
                    __currency_convert_recursively($('#table_cart'));
                },
            });
        });
          
      
        

        

</script>
@endsection