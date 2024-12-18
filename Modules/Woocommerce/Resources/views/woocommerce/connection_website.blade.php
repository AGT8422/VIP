@extends('layouts.app')
@section('title', __('woocommerce::lang.connection_website'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('woocommerce::lang.connection_website')</h1>
</section>

<!-- Main content -->
<section class="content">
     <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  " style="padding:10px;margin:0px 10%;  max-height:auto;width:80% ">
            @component("components.filters",['class'=>'box-primary','title'=>__('Filter ') ])
                <div class="col-md-4" >
                    <div class="form-group">
                        {!! Form::label('company_name', __('Company Name') . ':') !!}
                        {!! Form::select('company_name', $companies , null, ['class' => 'fa-product_name form-control select2', 'style' => 'width:100%', 'id' => 'product_name', 'placeholder' => __('lang_v1.all')]); !!}
                    </div>
                </div>
                <div class="col-md-4" >
                    <div class="form-group">
                        {!! Form::label('e_commerce_url', __('Ecommerce Url') . ':') !!}
                        {!! Form::select('e_commerce_url', $e_url , null, ['class' => 'fa-product_name form-control select2', 'style' => 'width:100%', 'id' => 'product_name', 'placeholder' => __('lang_v1.all')]); !!}
                    </div>
                </div>
                <div class="col-md-4" >
                    <div class="form-group">
                        {!! Form::label('erp_url', __('ERP Url') . ':') !!}
                        {!! Form::select('erp_url', $erp_url , null, ['class' => 'fa-product_name form-control select2', 'style' => 'width:100%', 'id' => 'product_name', 'placeholder' => __('lang_v1.all')]); !!}
                    </div>
                </div>
                <div class="col-md-4" >
                    <div class="form-group">
                        {!! Form::label('username', __('Username') . ':') !!}
                        {!! Form::select('username', $username , null, ['class' => 'fa-product_name form-control select2', 'style' => 'width:100%', 'id' => 'product_name', 'placeholder' => __('lang_v1.all')]); !!}
                    </div>
                </div>
            @endcomponent
        </div>  
        <div class="col-xs-12" style="padding:10px;margin:0px 10%;  max-height:auto;width:80% ">
             
            {{-- *********3************ --}}
            @component('components.widget',['class'=>' sections box-primary',"title"=>__('LIST OF WEBSITES')]) 
                <div class="row" style="border:0px solid #ee680e;border-radius:10px;">
                    <table class="table table-bordered table-striped dataTable" id="table_websites">
                        <thead>
                            <th>@lang('Date')</th>
                            <th>@lang('Ecommerce Url')</th>
                            <th>@lang('Company Name')</th>
                            <th>@lang('Erp Url')</th>
                            <th>@lang('Username')</th>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot>
                            <td colspan="5"></td>
                        </tfoot>    
                    </table>   
                        
                </div>
            @endcomponent
            
           
        </div>
    </div>
   
   
 
</section>
@stop
@section('javascript')
  <script type="text/javascript">
        table_websites = $('#table_websites').DataTable({
            processing:true,
            serverSide:true,
            // scrollY:  "75vh",
            // scrollX:  true,
            // scrollCollapse: true, 
            "ajax": {
                "url": "/woocommerce/websites/list/all",
                "data": function ( d ) {
                    d.company_name     = "Top";
                    d.e_commerce_url   = "Top";
                    d.erp_url          = "Top";
                    d                  = __datatable_ajax_callback(d);
                }
            },
            columns: [
                { data: 'date'            , name: 'date'      },
                { data: 'e_commerce_url'  , name: 'e_commerce_url'     },
                { data: 'company_name'    , name: 'company_name'     },
                { data: 'erp_url'         , name: 'erp_url'      },
                { data: 'username'        , name: 'username'  },
                
            ],
            fnDrawCallback: function(oSettings) {
                __currency_convert_recursively($('#table_websites'));
            },
        });
        
 


    </script>  
@endsection