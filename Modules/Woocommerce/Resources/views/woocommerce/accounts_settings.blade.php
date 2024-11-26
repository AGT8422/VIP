@extends('layouts.app')
@section('title', __('woocommerce::lang.accounts_settings'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('woocommerce::lang.accounts_settings')</h1>
</section>

<!-- Main content -->
<section class="content">
    {!! Form::open(['action' => '\Modules\Woocommerce\Http\Controllers\WoocommerceController@updateAccountsSettings', 'method' => 'post','files' => true]) !!}
    <div class="row">
        <div class="col-xs-12" style="padding:10px;  max-height:auto; ">
           <!--  <pos-tab-container> -->
            @component('components.widget',['class'=>' sections box-primary']) 
                <div class="contents">
                    <div class="col-sm-12">
                        <div class="form-group">
                            {!! Form::label('pattern_id',  __('home.patterns') . ':') !!}  
                            {!! Form::select('pattern_id', $patterns,$allData->pattern_id  , ['class' => 'form-control select2',"placeholder"=>__('home.select pattern')]); !!}
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <hr>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('purchase', __('home.Purchase').':') !!} 
                            {!! Form::select('purchase', $accounts,$allData->purchase, [ 'class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('purchase_tax', __('home.Purchase Tax').':') !!} 
                            {!! Form::select('purchase_tax', $accounts,$allData->purchase_tax, [ 'class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('purchase_return', __('home.Purchase Return').':') !!} 
                            {!! Form::select('purchase_return', $accounts,$allData->purchase_return, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('purchase_discount', __('home.Purchase Discount').':') !!} 
                            {!! Form::select('purchase_discount', $accounts,$allData->purchase_discount, [ 'class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <hr>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('sale', __('home.Sale').':') !!} 
                            {!! Form::select('sale', $accounts,$allData->sale, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('sale_tax', __('home.Sale Tax').':') !!} 
                            {!! Form::select('sale_tax', $accounts,$allData->sale_tax, [ 'class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('sale_return', __('home.Sale Return').':') !!} 
                            {!! Form::select('sale_return', $accounts,$allData->sale_return, [ 'class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('sale_discount', __('home.Sale Discount').':') !!} 
                            {!! Form::select('sale_discount', $accounts,$allData->sale_discount, [ 'class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
                        </div>
                    </div>
                    
                    <div class="clearfix"></div>
                    <hr>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('client_account_id', __( 'lang_v1.cash' ) . ': ') !!}
                              {!! Form::select('client_account_id', $accounts,$allData->client_account_id, ['class' => 'form-control select2',  "id"=>"client_account_id", 'placeholder' => __( 'messages.please_select' ) ]); !!}
                          </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                              {!! Form::label('client_visa_account_id', __( 'lang_v1.visa_account' ) . ': ') !!}
                              {!! Form::select('client_visa_account_id', $accounts,$allData->client_visa_account_id, ['class' => 'form-control select2',  "id"=>"client_visa_account_id", 'placeholder' => __( 'messages.please_select' ) ]); !!}
                          </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('client_store_id', __( 'warehouse.nameW' ) . ': ') !!}
                              {!! Form::select('client_store_id', $stores,$allData->client_store_id, ['class' => 'form-control select2',  "id"=>"client_store_id", 'placeholder' => __( 'messages.please_select' ) ]); !!}
                          </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('tax_id', __( 'product.applicable_tax' ) . ': ') !!}
                              {!! Form::select('tax_id', $tax,$allData->tax_id, ['class' => 'form-control select2',  "id"=>"tax_id", 'placeholder' => __( 'messages.please_select' ) ]); !!}
                          </div>
                    </div>
                </div>
            @endcomponent
            <!--  </pos-tab-container> -->
        </div>
    </div>
   
    <div class="row">
        <div class="col-xs-12">
            <div class="form-group pull-right">
            {{Form::submit('update', ['class'=>"btn btn-danger"])}}
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</section>
@stop
@section('javascript')
<script type="text/javascript">
      
</script>
@endsection