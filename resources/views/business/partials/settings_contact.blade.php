<!--Purchase related settings -->
<div class="pos-tab-content">
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('default_credit_limit',__('lang_v1.default_credit_limit') . ':') !!}
                {!! Form::text('common_settings[default_credit_limit]', $common_settings['default_credit_limit'] ?? '', ['class' => 'form-control input_number',
                'placeholder' => __('lang_v1.default_credit_limit'), 'id' => 'default_credit_limit']); !!}
            </div>
        </div>
    </div>
        <hr style="width:100% !important;height:5px !important">
        <div class="col-md-6 col-sm-6 ">
            <div class="form-group">
                {!! Form::label('account_liability', __('lang_v1.account_liability') . ':*') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fas fa-edit"></i>
                    </span>
                    {!! Form::select('account_liability',  $account_type_, $business->liability, ['class' => 'form-control select2','style'=>'width:100%' , 'placeholder' => __( 'messages.please_select' ),'id'=>'account_liability']); !!}
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-6   ">
            <div class="form-group">
                {!! Form::label('account_assets', __('lang_v1.account_assets') . ':*') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fas fa-edit"></i>
                    </span>
                    {!! Form::select('account_assets',  $account_type_, $business->assets, ['class' => 'form-control select2','style'=>'width:100%','id'=>'account_assets', 'placeholder' => __( 'messages.please_select' )]); !!}
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-6 ">
            <div class="form-group">
                {!! Form::label('bank', __('lang_v1.bank') . ':*') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fas fa-edit"></i>
                    </span>
                    {!! Form::select('bank',  $account_type_, $business->bank, ['class' => 'form-control select2','style'=>'width:100%', 'placeholder' => __( 'messages.please_select' ),'id'=>'bank']); !!}
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-6 ">
            <div class="form-group">
                {!! Form::label('cash', __('lang_v1.cash') . ':*') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fas fa-edit"></i>
                    </span>
                    {!! Form::select('cash',  $account_type_, $business->cash, ['class' => 'form-control select2','style'=>'width:100%', 'placeholder' => __( 'messages.please_select' ),'id'=>'cash']); !!}
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-6 ">
            <div class="form-group">
                {!! Form::label('additional_expense', __('lang_v1.additional_expense') . ':*') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fas fa-edit"></i>
                    </span>
                    {!! Form::select('additional_expense',  $additional_expenses, $business->additional_expense, ['class' => 'form-control select2','style'=>'width:100%', 'placeholder' => __( 'messages.please_select' ),'id'=>'additional_expense']); !!}
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-6 col-sm-6 ">
            <div class="form-group">
                {!! Form::label('customer_type_id', __('lang_v1.customer_account') . ':*') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fas fa-edit"></i>
                    </span>
                    {!! Form::select('customer_type_id',  $account_type_, $business->customer_type_id, ['class' => 'form-control select2','style'=>'width:100%', 'placeholder' => __( 'messages.please_select' ),'id'=>'cash']); !!}
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-6 ">
            <div class="form-group">
                {!! Form::label('supplier_type_id', __('lang_v1.supplier_account') . ':*') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fas fa-edit"></i>
                    </span>
                    {!! Form::select('supplier_type_id',  $account_type_, $business->supplier_type_id, ['class' => 'form-control select2','style'=>'width:100%', 'placeholder' => __( 'messages.please_select' ),'id'=>'cash']); !!}
                </div>
            </div>
        </div>
    
</div>