<div class="modal-dialog" role="document" style="width:50%">
  <div class="modal-content">

    {!! Form::open(['url' => action('UnitController@updateUnit',[$product->id]), 'method' => 'post', 'id' =>  'unit_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'unit.edit_unit' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
          <div class="form-group col-sm-12">
            <div onClick="addUnit();"><i class="fa fas fa-plus"></i></div>
          </div>
          <div class="form-group col-sm-12 " id="base_unit_div">
            <table class="table" id="unit_tables">
              @foreach($unitall as $i)
              {{-- @php dd($unitall); @endphp --}}
                @if($i->product_id == $product->id)
                  <tr>
                  <td style="vertical-align: middle;">
                      {!! Form::hidden('line_ids[]', $i->id, ['class' => 'form-control', 'required', 'placeholder' => __( 'unit.name' )]); !!}
                      {!! Form::label('actual_name', __( 'unit.name' ) . ':*') !!}
                      {!! Form::text('actual_name[]', $i->actual_name, ['class' => 'form-control', 'required', 'placeholder' => __( 'unit.name' )]); !!}
                      <br>
                      {!! Form::text('base_unit_multiplier[]', $i->base_unit_multiplier, ['class' => 'form-control input_number', 'placeholder' => __( 'lang_v1.times_base_unit' )]); !!}</td>
                      <td style="vertical-align: middle;">
                        {!! Form::label('short_name', __( 'unit.short_name' ) . ':*') !!}
                        {!! Form::text('short_name[]', $i->short_name, ['class' => 'form-control', 'placeholder' => __( 'unit.short_name' ), 'required']); !!}
                        <br>
                        {!! Form::select('base_unit_id[]', $units, $i->base_unit_id, ['placeholder' => __( 'lang_v1.select_base_unit' ), 'class' => 'form-control']); !!}
                      </td>
                      <td style="vertical-align: middle;">
                        {!! Form::label('allow_decimal', __( 'unit.allow_decimal' ) . ':*') !!}
                        {!! Form::select('allow_decimal[]', ['1' => __('messages.yes'), '0' => __('messages.no')], $i->allow_decimal, ['placeholder' => __( 'messages.please_select' ), 'required', 'class' => 'form-control']); !!}
                        <br>
                      {!! Form::text('price_unit[]', $i->price_unit, ['class' => 'form-control input_number', 'placeholder' => __( 'lang_v1.price' )]); !!}</td>
                    </td>
                      <td><i class='fa fas fa-trash' onClick='deleteR(this);'></i></td>  
                    <td></td>
                </tr>   
                @endif
              @endforeach
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
      <button type="button" class="btn btn-default" data-dismiss="modal" >@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script type="text/javascript">

</script>
