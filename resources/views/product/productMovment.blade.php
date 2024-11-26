<div class="modal-dialog modal-xl" role="document" id="model-os">
	<div class="modal-content">
		<div class="modal-header">
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1> 
        {{ $data->name }}
    </h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
    


    @can('product.view')



        <div class="row">
            <div class="col-md-12">


            <!-- Custom Tabs -->
                <div class="nav-tabs-custom">
               
                    <div class="tab-content">
                        <div class="tab-pane active" id="product_list_tab">
                                 
                                <table class="table table-bordered table-striped ajax_view hide-footer" id="product_move_table">
                                    <thead>
                                        <tr>
                                            <th>@lang('warehouse.nameW')</th>
                                            <th>@lang('movement.move')</th>
                                            <th>@lang('movement.movePlus')</th>
                                            <th>@lang('movement.moveMinus')</th>
                                            <th>@lang('movement.total')</th>
                                            <th>@lang('lang_v1.price')</th>
                                            <th>@lang('lang_v1.date')</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        @foreach ($allData as $data)
                                        <tr>
                                            <td> {{ $data->store?$data->store->name:'----' }}</td>
                                            <td>{{ $data->movement }}</td>
                                            <td>{{ $data->plus_qty }}</td>
                                            <td>{{ $data->minus_qty  }}</td>
                                            <td>{{ $data->current_qty  }}</td>
                                            <td>{{ $data->current_price  }}</td>
                                            <td>{{ date('Y-m-d',strtotime($data->created_at))  }}</td>
                                            {{-- <td></td>
                                            <td></td> --}}
                                            </tr>
                                        @endforeach
                                       <tr>
                                        <td colspan="7" class="                                                                      v                   vbvvvvvvvvvc0 ">
                                            {{ $allData->links() }}
                                        </td>
                                       </tr>
                                    </tfoot>
                                </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcan


 

<div class="modal fade product_modal" tabindex="-1" role="dialog"
    aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade" id="view_product_modal" tabindex="-1" role="dialog"
    aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade" id="opening_stock_modal" tabindex="-1" role="dialog"
    aria-labelledby="gridSystemModalLabel">
</div>

@include('product.partials.edit_product_location_modal')

</section>
<!-- /.content -->

 

        </div>
        </div>
        </div>
<script type="text/javascript" >
    $(document).ready(function(){
        //  $("#product_move_table").html() ;
     });
 </script>
