    @extends('layouts.app')
    @section('title', __('purchase.purchases'))

    @section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header no-print">
        <h1><b>@lang('purchase.Open')</b>
            <small></small>
        </h1>               
        <h5><i><b>{{ "   Products  >  " }} </b>{{ "Add Opening Stock "   }} <b> {{"   "}} </b></i></h5>  
        <br> 
     
    </section>

    <!-- Main content -->
    <section class="content no-print">
        <div class="row" style="margin:0px 15%">
            @component('components.filters', ["class"=>"box-primary" ,'title' => __('report.filters')])
                <form action="{{ URL::to('products/Opening_product') }}" method="GET">
                    <div class="col-md-5">
                        <div class="form-group">
                            {!! Form::label('purchase_list_filter_location_id',  __('purchase.business_location') . ':') !!}
                            {!! Form::select('location_id', $business_locations, app('request')->input('location_id'), ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            {!! Form::label('purchase_list_filter_supplier_id',  __('home.Store') . ':') !!}
                            {!! Form::select('store_id', $childs, app('request')->input('store_id'), ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                        </div>
                    </div>
                    <div class="col-md-2 text-right">
                        <label for="purchase_list_filter_location_id" class="label-control" style="width: 100%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                        <button type="submit" class="btn btn-md btn-primary">  Filter</button>
                    </div>
                    
                </form>
            @endcomponent
        </div>
        <div class="row" style="margin:0px 15%">
            @component("components.widget",["class"=>"box-primary","title"=>__('List Of Opening Stock')])
                <div class="col-md-12">
                    <a href="{{ URL::to('/products/add-Opening-Product') }}"  class="pull-right btn btn-md btn-primary">@lang("messages.add")</a>
                    <h1>&nbsp;</h1>
                    <table class="table table-bordered ">
                        <tbody>
                        <tr style="background:#f1f1f1;">
                            <th>Ref No</th>
                            <!--<th>Product</th>-->
                            <!--<th>Amount</th>-->
                            <!--<th>warehouse name</th>-->
                            <th>Business Loaction</th>
                            <th>Date</th>  
                            <th></th>  
                        </tr>
                            @php
                            $array = [];
                            @endphp
                        @foreach ($allData as $item)
                        
                        
                            @if(!in_array($item->transaction->ref_no ,$array))
                                @php
                                    array_push($array,$item->transaction->ref_no);
                                @endphp
                                <tr style="border:1px solid #f1f1f1;">
                            <td>{{ $item->transaction?$item->transaction->ref_no:'-------' }}</td>
                            <!--<td>{{  $item->product?$item->product->name:'-------' }}</td>-->
                            <!--<td>{{ $item->quantity }}</td>-->
                            <!--<td> {{  $item->store?$item->store->name:''}}</td>-->
                            <td> {{ $item->location?$item->location->name:'  ' }} </td>
                            <td>{{ date('Y-m-d',strtotime($item->date)) }}</td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">@lang("messages.actions")
                                        <span class="caret"></span>
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-left" role="menu">
                                        @can("warehouse.views")
                                        <li >
                                            <a  href="/products/Opening_product/view/{{$item->id}}"     class="view-product">
                                                <i class="fa fa-eye"></i>
                                                @lang("messages.view")
                                            </a>
                                        </li>
                                        @endcan
                                        @can("warehouse.Edit")
                                        <li >
                                            <a  href="/products/Opening_product/edit/{{$item->id}}" id="edit_product" >
                                                <i class="glyphicon glyphicon-edit"></i>
                                                @lang("messages.edit")
                                            </a>
                                        </li>
                                        @endcan
                                        @if(auth()->user()->hasRole('Admin#' . session('business.id')))
                                        <li >
                                            <a href="/products/Opening_product/destroy/{{$item->id}}" id="destroy" class="delete_open">
                                                <i class="fa fa-trash"></i>
                                                @lang("messages.delete")
                                            </a>
                                        </li>
                                        @endif
                                    </ul>
                                </div>


                            </td>
                            </tr>
                            
                            @endif    
                        @endforeach
                        
                        </tbody>
                        <tfoot>
                        <tr class="bg-gray  font-17 footer-total" style="border:1px solid #f1f1f1;  ">
                            <td class="text-center " colspan="7"><strong>
                            </strong></td>
                            
                        </tr>
                        </tfoot>
                    </table>
                </div>
            @endcomponent
        </div>

        <div class="modal fade product_modal" tabindex="-1" role="dialog"
            aria-labelledby="gridSystemModalLabel">
        </div>
    

    </section>

    <section id="receipt_section" class="print_section"></section>

    <!-- /.content -->
    @stop
    @section('javascript')
    
    
        <script type="text/javascript">
            $(document).on('click', 'a.view-product', function(e) {
                e.preventDefault();
                $.ajax({
                    url: $(this).attr('href'),
                    dataType: 'html',
                    success: function(result) {
                        $('.product_modal')
                            .html(result)
                            .modal('show');
                        __currency_convert_recursively($('.product_modal'));
                    },
                });
            });

        $(document).on('click', 'a.delete_open', function(e) {
            e.preventDefault();
            swal({
                title: LANG.sure,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                    var href = $(this).attr('href');
                    $.ajax({
                        method: 'GET',
                        url: href,
                        dataType: 'json',
                        success: function(result) {
                            if (result.success == true) {
                                toastr.success(result.msg);
                            
                            } else {
                                toastr.error(result.msg);
                            }
                            

                        },
                    });
                    location.reload();
                }
            });
        });
        </script>
        
    @endsection