@extends('layouts.app')
@section('title', __('woocommerce::lang.shop_settings'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('woocommerce::lang.shop_settings')</h1>
</section>

<!-- Main content -->
<section class="content">
     <div class="row">
        <div class="col-xs-12" style="padding:10px;  max-height:auto; ">
           
            {{-- *********3************ --}}
            @component('components.widget',['class'=>' sections box-primary',"title"=>__('All Shop By Category ')]) 
            <div class="tab">
                <div class="row">
                    <div class="col-lg-12 ">
                        <a class="btn btn-primary pull-right" href="{{action("\Modules\Woocommerce\Http\Controllers\WoocommerceController@createShop" , ['style'=>'about'])}}">@lang('messages.add') <i class="fa fas-fa fa-plus"></i></a>
                    </div>
                </div>
                <div class="content" style="width:100%">
                    <table class="table table-striped table-bordered dataTable" style="width:100%" id="table_floating_bar">
                        <thead>
                            <tr>
                                <th class="xl-1"  style="background-color:#00000034">Action</th>
                                <th class="xl-1"  style="background-color:#00000034">Title</th>
                                <th class="xl-1 img_section"  style="background-color:#00000034">Icon</th>
                                <th class="xl-1"  style="background-color:#00000034">Category</th>
                                <th class="xl-1"  style="background-color:#00000034">Date</th>
                                <th class="xl-1"  style="background-color:#00000034">Type</th>
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
        
        
        table_floating_bar = $('#table_floating_bar').DataTable({
            processing:true,
            serverSide:true,
            scrollY:  "75vh",
            scrollX:  true,
            scrollCollapse: true, 
            "ajax": {
                "url": "/woocommerce/shop/all",
                "data": function ( d ) {
                    d.check        = "Top";
                    d              = __datatable_ajax_callback(d);
                }
            },
            columns: [
                { data: 'action', name: 'action'},
                { data: 'name'  , name: 'name'      },
                { data: 'icon'  , name: 'icon'  ,class: 'img_section' },
                { data: 'category'  , name: 'category'      },
                { data: 'created_at'  , name: 'created_at'       },
                { data: 'type'  , name: 'type'       },
            ],
            fnDrawCallback: function(oSettings) {
                __currency_convert_recursively($('#table_floating_bar'));
            },
        });
          
       $(document).on('click', 'a.delete-shop', function(e){
            e.preventDefault();
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    var href = $(this).attr('href');
                    $.ajax({
                        method: "GET",
                        url: href,
                        dataType: "json",
                        success: function(result){
                            if(result.success == true){
                                toastr.success(result.msg);
                                table_floating_bar.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
            });
            });
        
        

        

</script>
@endsection