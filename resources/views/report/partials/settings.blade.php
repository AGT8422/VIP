@extends('layouts.app')
@section('title',$title)
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ $title }} </h1>
</section>
<section class="content">

    {!! Form::open(['url' => '/reports/settings/save', 'method' => 'post', 'id' => 'reports_setting', 'files' => true ]) !!}
    <div class="col-xs-12">
        @component('components.widget' ,["class"=>"box-primary"])
            <h1>@lang("lang_v1.setting_account_report")</h1>    
            {{-- Sales --}}
            <table class="table">
                <thead>
                    <tr>
                        <th>@lang("sale.sale") @lang("account.account")</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="row">
                                <div class="col-md-6" style="">
                                    <div class="form-group">
                                        {!! Form::label('sale_account', __('sale.parent_account_sale') . ':') !!}  
                                        <br/>
                                        {!! Form::select('sale_account', $accounts, (!empty($data))?$data->sale:null, ['class' => 'form-control     select2', 'placeholder' => __('messages.please_select'),'id'=>'sale_account']); !!}
                                    </div>
                                </div>
                                <div class="col-md-6" style="">
                                    <div class="form-group">
                                        {!! Form::label('return_sale_account', __('sale.parent_account_sale_return') . ':') !!}  
                                        <br/>
                                        {!! Form::select('return_sale_account', $accounts, (!empty($data))?$data->sale_return:null, ['class' => 'form-control      select2', 'placeholder' => __('messages.please_select'),'id'=>'return_sale_account']); !!}
                                    </div>
                                </div>
                                <div class="col-md-6" style="border:0px solid black">
                                    <div class="form-group">
                                        {!! Form::label('sale_additional_cost', __('sale.parent_additional_sale_cost').':*') !!}
                                        <br/>
                                        {!! Form::select('sale_additional_cost',$accounts, (!empty($data))?$data->sale_addtional_cost:null, ['class' => 'form-control select2 ','id'=>'sale_additional_cost' ,'placeholder' => __('messages.please_select') ]); !!}
                                    </div>
                                </div>
                                <div class="col-md-6" style="border:0px solid black">
                                    <div class="form-group">
                                        {!! Form::label('sale_discount', __('sale.parent_discount_sale').':*') !!}
                                        <br/>
                                        {!! Form::select('sale_discount',$accounts, (!empty($data))?$data->sale_discount:null, ['class' => 'form-control select2 ','id'=>'sale_discount' ,'placeholder' => __('messages.please_select') ]); !!}
                                    </div>
                                </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
               
            </table>
            {{-- Purchases --}}
            <table class="table">
                <thead>
                    <tr>
                        <th>@lang("purchase.purchase") @lang("account.account")</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="row">
                                <div class="col-md-6" style="">
                                    <div class="form-group">
                                        {!! Form::label('purchase_account', __('purchase.parent_account_purchase') . ':') !!}  
                                        <br/>
                                        {!! Form::select('purchase_account', $accounts, (!empty($data))?$data->purchase:null, ['class' => 'form-control     select2', 'placeholder' => __('messages.please_select'),'id'=>'purchase_account']); !!}
                                    </div>
                                </div>
                                <div class="col-md-6" style="">
                                    <div class="form-group">
                                        {!! Form::label('return_purchase_account', __('purchase.parent_account_purchase_return') . ':') !!}  
                                        <br/>
                                        {!! Form::select('return_purchase_account', $accounts, (!empty($data))?$data->purchase_return:null, ['class' => 'form-control      select2', 'placeholder' => __('messages.please_select'),'id'=>'return_purchase_account']); !!}
                                    </div>
                                </div>
                                <div class="col-md-6" style="border:0px solid black">
                                    <div class="form-group">
                                        {!! Form::label('purchase_additional_cost', __('purchase.parent_additional_purchase_cost').':*') !!}
                                        <br/>
                                        {!! Form::select('purchase_additional_cost',$accounts, (!empty($data))?$data->purchase_addtional_cost:null, ['class' => 'form-control select2 ','id'=>'purchase_additional_cost' ,'placeholder' => __('messages.please_select') ]); !!}
                                    </div>
                                </div>
                                <div class="col-md-6" style="border:0px solid black">
                                    <div class="form-group">
                                        {!! Form::label('purchase_discount', __('purchase.parent_discount_purchase').':*') !!}
                                        <br/>
                                        {!! Form::select('purchase_discount',$accounts, (!empty($data))?$data->purchase_discount:null, ['class' => 'form-control select2 ','id'=>'purchase_discount' ,'placeholder' => __('messages.please_select') ]); !!}
                                    </div>
                                </div>
                                <div class="col-md-6" style="border:0px solid black">
                                    <div class="form-group">
                                        {!! Form::label('purchase_closing_stock', __('purchase.purchase_closing_stock').':*') !!}
                                        <br/>
                                        {!! Form::select('purchase_closing_stock',$accounts, (!empty($data))?$data->purchase_closing_stock:null, ['class' => 'form-control select2 ','id'=>'purchase_closing_stock' ,'placeholder' => __('messages.please_select')]); !!}
                                    </div>
                                </div>
                                <div class="col-md-6" style="border:0px solid black">
                                    <div class="form-group">
                                        {!! Form::label('purchase_opening_stock', __('purchase.purchase_opening_stock').':*') !!}
                                        <br/>
                                        {!! Form::select('purchase_opening_stock',$accounts, (!empty($data))?$data->purchase_opening_stock:null, ['class' => 'form-control select2 ','id'=>'purchase_opening_stock' ,'placeholder' => __('messages.please_select') ]); !!}
                                    </div>
                                </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
                
            </table>
            {{-- Expenses  / Revenues --}}
            <table class="table">
                <thead>
                    <tr>
                        <th>@lang("lang_v1.expenses") {{" / "}}  @lang("lang_v1.Revenues") @lang("account.account")</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="row">
                                <div class="col-md-6" style="">
                                    <div class="form-group">
                                        {!! Form::label('expense_account', __('lang_v1.parent_account_expense') . ':') !!}  
                                        <br/>
                                        {!! Form::select('expense_account', $accounts, (!empty($data))?$data->expense:null, ['class' => 'form-control     select2', 'placeholder' => __('messages.please_select'),'id'=>'expense_account']); !!}
                                    </div>
                                </div>
                                <div class="col-md-6" style="">
                                    <div class="form-group">
                                        {!! Form::label('revenue_account', __('lang_v1.parent_account_revenue') . ':') !!}  
                                        <br/>
                                        {!! Form::select('revenue_account', $accounts, (!empty($data))?$data->revenue:null, ['class' => 'form-control     select2', 'placeholder' => __('messages.please_select'),'id'=>'revenue_account']); !!}
                                    </div>
                                </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td></td>
                    </tr>
                </tfoot>
             
            </table>
           
            {{-- Submit Button --}}
            <div class="content">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="sub-mit pull-right">
                             &nbsp;
                        </div>
                        <div class="sub-mit pull-right">
                            <button type="submit" name="save_report" class="btn btn-primary"  >@lang('messages.save')</button>
                        </div>
                    </div>
                </div>
            </div>
        @endcomponent
    </div>
   
    {!! Form::close() !!}
</section>
@stop