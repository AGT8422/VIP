<div class="pos-tab-content">
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
            
                {!! Form::label('name',__('lang_v1.account') . ':*') !!}
                <br>
                {!! Form::select('itemMfg', $accounts , $itemMfg , ['class'       => 'form-control select2' , 'style' => 'width: 100%;', 'id'=> 'itemMfg' ,
                                                           'placeholder' => __('lang_v1.Put_account')]); !!}

            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                
                {!! Form::label('profitMfg',__('lang_v1.Profit_account') . ':*') !!}
                <br>
                {!! Form::select('profitMfg', $accounts ,$profitMfg, ['class'       => 'form-control select2', 'style' => 'width: 100%;', 'id'=> 'profitMfg' ,
                                                           'placeholder' => __('lang_v1.Put_Profit_account')]); !!}

            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                
                {!! Form::label('store_mfg',__('lang_v1.Put_Store') . ':*') !!}
                <br>
                {!! Form::select('store_mfg', $Stores ,$store_mfg, ['class'       => 'form-control select2', 'style' => 'width: 100%;', 'id'=> 'profitMfg' ,
                                                           'placeholder' => __('lang_v1.Choose_M_Store')]); !!}

            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                    <!--<label>-->
                    <!--{!! Form::checkbox('wastageMfg',    1  , $wastageMfg  , [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.Wastage Percent' ) }}-->
                    <!--</label>-->
                </div>
            </div>
        </div>
   </div>
</div>