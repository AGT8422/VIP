<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\ChartOfAccounts\Http\Controllers\ChartOfAccountsController@saveacount'), 'method' => 'post' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'chartofaccounts::lang.add_account' )</h4>
        </div>

<div class="modal-body">
  <div class="row">

    <div class="col-lg-6 mt-15">
        {!! Form::label('type', __( 'chartofaccounts::lang.account_type' ) ) !!}
        <select name="type" id="type " class="form-control">
            <option value="0">@lang('chartofaccounts::lang.account_chiled')</option>
            <option value="1">@lang('chartofaccounts::lang.account_main')</option>
        </select>
     </div>

    <div class="col-lg-6 mt-15">
        {!! Form::label('parent_id', __( 'chartofaccounts::lang.main_account' ) ) !!}
       {{-- {!! Form::select('parent_id', $accounts, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'parent_id', 'placeholder' => __('chartofaccounts::lang.main_account')]); !!}--}}
        <select name="parent_id" id="parent_id"  class="form-control select2">
            @foreach($accounts as $account)
                <option value="{{$account->id}}" data-href="99">{{$account->name}}</option>
            @endforeach
        </select>


    </div>


    <div class="col-lg-6 mt-15">
        <div class="form-group">
            {!! Form::label('name', __( 'chartofaccounts::lang.account_name' ) . ':*') !!}
            {!! Form::text('name', null, ['class' => 'form-control', 'required' ]); !!}
        </div>
    </div>
    <div class="col-lg-6 mt-15">
        <div class="form-group">
            {!! Form::label('account_code', __( 'chartofaccounts::lang.account_code' ) . ':') !!}
            <input type="text" name="account_code" id="account_code" class="form-control" style="max-width:200px" >

            </div>
     </div>


    <div class="col-lg-6 mt-15">
        {!! Form::label('journal_cat', __( 'chartofaccounts::lang.journal_cat' ) ) !!}
        <select name="journal_cat" id="journal_cat " class="form-control">
            <option value="0">@lang('chartofaccounts::lang.journal_debt')</option>
            <option value="1">@lang('chartofaccounts::lang.journal_cridet')</option>
        </select>
    </div>

</div>
     </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->