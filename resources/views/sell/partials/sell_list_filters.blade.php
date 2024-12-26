<?php
    $users = [];
    $business_id = request()->session()->get("user.business_id");
    $us     =  \App\Models\User::where('business_id', $business_id)
                        ->where('is_cmmsn_agnt', 1)->get();
    foreach($us as $it){
        $users[$it->id] = $it->first_name;
    }
?>
@if(empty($only) || in_array('sell_list_filter_location_id', $only))
<div class="col-md-3 hide">
    <div class="form-group">
        {!! Form::label('sell_list_filter_location_id',  __('purchase.business_location') . ':') !!}

        {!! Form::select('sell_list_filter_location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all') ]); !!}
    </div>
</div>
@endif
@if(empty($only) || in_array('sell_list_filter_customer_id', $only))
<div class="col-md-3">
    <div class="form-group">
        {!! Form::label('sell_list_filter_customer_id',  __('contact.customer') . ':') !!}
        {!! Form::select('sell_list_filter_customer_id', $customers, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
    </div>
</div>
@endif
@if(empty($only) || in_array('sell_list_filter_payment_status', $only))
<div class="col-md-3">
    <div class="form-group">
        {!! Form::label('sell_list_filter_payment_status',  __('purchase.payment_status') . ':') !!}
        {!! Form::select('sell_list_filter_payment_status', ['paid' => __('lang_v1.paid'), 'due' => __('lang_v1.due'), 'partial' => __('lang_v1.partial'), 'overdue' => __('lang_v1.overdue'),'installmented' => __('lang_v1.installmented')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
    </div>
</div>
@endif
@if(empty($only) || in_array('sell_list_filter_date_range', $only))
<div class="col-md-3">
    <div class="form-group">
        {!! Form::label('sell_list_filter_date_range', __('report.date_range') . ':') !!}
        {!! Form::text('sell_list_filter_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
    </div>
</div>
@endif
@if((empty($only) || in_array('created_by', $only)) && !empty($sales_representative))
<div class="col-md-3">
    <div class="form-group">
        {!! Form::label('created_by',  __('report.user') . ':') !!}
        {!! Form::select('created_by', $sales_representative, null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
    </div>
</div>
@endif
@if(empty($only) || in_array('sales_cmsn_agnt', $only))
@if(!empty($is_cmsn_agent_enabled))
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('sales_cmsn_agnt',  __('lang_v1.sales_commission_agent') . ':') !!}
            {!! Form::select('sales_cmsn_agnt', $commission_agents, null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
        </div>
    </div>
@endif
@endif
<div class="col-md-3">
	{!! Form::label('types_of_service_price_group', __('home.Agent') . ':') !!}
	{{  Form::select('agent_id',$users,null,['class'=>'form-control select2 ','id'=>'agent_id','placeholder'=>trans('home.Choose Agent')]) }}
</div>
<div class="col-md-3">
	{!! Form::label('pattern_id', __('business.patterns') . ':') !!}
	{{  Form::select('pattern_id',$patterns,null,['class'=>'form-control select2 ','id'=>'pattern_id','placeholder'=>trans('business.patterns')]) }}
</div>
@if(empty($only) || in_array('service_staffs', $only))
@if(!empty($service_staffs))
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('service_staffs', __('restaurant.service_staff') . ':') !!}
            {!! Form::select('service_staffs', $service_staffs, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>
@endif
 <div class="col-md-3">
    {{-- <div class="form-group">
        {!! Form::label('cost_center_id', __('home.Cost Center') . ':') !!}
        {!! Form::select('cost_center_id', $cost_centers, null, ['class' => 'form-control select2' ,'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
    </div> --}}
</div>
{{-- <div class="col-md-3">
    <div class="form-group">
        {!! Form::label('project_no', __('sale.project_no') . ':') !!}
        {!! Form::select('project_no', $project_numbers, null, ['class' => 'form-control select2' ,'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
    </div>
</div> --}}
@endif
@if(!empty($shipping_statuses))
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('shipping_status', __('lang_v1.shipping_status') . ':') !!}
            {!! Form::select('shipping_status', $shipping_statuses, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>
@endif
@if(empty($only) || in_array('only_subscriptions', $only))
<div class="col-md-3 hide">
    <div class="form-group">
        <div class="checkbox">
            <label>
                <br>
              {!! Form::checkbox('only_subscriptions', 1, false, 
              [ 'class' => 'input-icheck', 'id' => 'only_subscriptions']); !!} {{ __('lang_v1.subscriptions') }}
            </label>
        </div>
    </div>
</div>
@endif
