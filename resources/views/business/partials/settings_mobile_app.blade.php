<div class="pos-tab-content">
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                {!! Form::label('app_pattern_id',__('home.pattern') . ':*') !!}
                <br>
                {!! Form::select('app_pattern_id', $patterns , $app_pattern_id , ['class'       => 'form-control select2' , 'style' => 'width: 100%;', 'id'=> 'itemMfg' ,
                                                           'placeholder' => __('messages.please_select')]); !!}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                {!! Form::label('app_store_id',__('warehouse.nameW') . ':*') !!}
                <br>
                {!! Form::select('app_store_id', $Stores ,$app_store_id, ['class'       => 'form-control select2', 'style' => 'width: 100%;', 'id'=> 'profitMfg' ,
                                                           'placeholder' => __('messages.please_select')]); !!}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                {!! Form::label('app_account',__('lang_v1.account') . ':*') !!}
                <br>
                {!! Form::select('app_account', $accounts ,$app_account, ['class'       => 'form-control select2', 'style' => 'width: 100%;', 'id'=> 'profitMfg' ,
                                                           'placeholder' => __('messages.please_select')]); !!}
            </div>
        </div>
         
   </div>
</div>