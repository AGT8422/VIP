<div class="modal-dialog" role="document" style="width:50%">
  <div class="modal-content">

    {!! Form::open(['url' => action('UnitController@store'), 'method' => 'post', 'id' =>  'unit_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'unit.add_unit' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
          <div class="form-group col-sm-12">
            <div onClick="addUnit();"><i class="fa fas fa-plus"></i></div>
          </div>
          <div class="form-group col-sm-12 " id="base_unit_div">
            <table class="table" id="unit_tables">
              {{-- <tr>
                <th style="vertical-align: middle;"><br>1 <span id="unit_name">@lang('product.unit')</span></th>
                <th style="vertical-align: middle;"><br>=</th>
                <td style="vertical-align: middle;">
                    {!! Form::label('actual_name', __( 'unit.name' ) . ':*') !!}
                    {!! Form::text('actual_name[]', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'unit.name' )]); !!}
                    <br>
                    {!! Form::text('base_unit_multiplier[]', null, ['class' => 'form-control input_number', 'placeholder' => __( 'lang_v1.times_base_unit' )]); !!}</td>
                    <td style="vertical-align: middle;">
                      {!! Form::label('short_name', __( 'unit.short_name' ) . ':*') !!}
                      {!! Form::text('short_name[]', null, ['class' => 'form-control', 'placeholder' => __( 'unit.short_name' ), 'required']); !!}
                      <br>
                      {!! Form::select('base_unit_id[]', $units, null, ['placeholder' => __( 'lang_v1.select_base_unit' ), 'class' => 'form-control']); !!}
                    </td>
                    <td style="vertical-align: middle;">
                      {!! Form::label('allow_decimal', __( 'unit.allow_decimal' ) . ':*') !!}
                      {!! Form::select('allow_decimal[]', ['1' => __('messages.yes'), '0' => __('messages.no')], null, ['placeholder' => __( 'messages.please_select' ), 'required', 'class' => 'form-control']); !!}
                      <br>
                    {!! Form::text('price_unit[]', null, ['class' => 'form-control input_number', 'placeholder' => __( 'lang_v1.price' )]); !!}</td>
                  </td>
                  {{-- <td><i class='fa fas fa-trash' onClick='deleteR();'></i></td> --}}
                  {{-- <td></td>
              </tr>   --}}
              <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
              </tr>
            </table>
          </div>
       
      </div>

    </div>

    <div class="modal-footer">
      <button type="button" class="btn btn-primary quik_add_unit"   >@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal" >@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script type="text/javascript">

</script>
