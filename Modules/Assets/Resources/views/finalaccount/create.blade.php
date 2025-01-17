<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Assets\Http\Controllers\FinalAccountController@store'),'id'=>'addnew', 'method' => 'post' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">إضافة حساب ختامي</h4>
        </div>

        <div class="modal-body">

                        <div class="form-group">
                            {!! Form::label('profite','قيمة الأرباح الموزعة :') !!}
                            {!! Form::text('profite', null, ['class' => 'form-control decimal', 'required', 'placeholder' =>'القيمة بالجنية' ]); !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('sharenumber','إجمالي عدد الأسهم : ') !!}
                            {!! Form::text('sharenumber', $totalshare, ['class' => 'form-control', 'readonly', 'placeholder' =>'' ]); !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('shareval','قيمة السهم  :') !!}
                            {!! Form::text('shareval', null, ['class' => 'form-control', 'readonly', 'placeholder' =>'' ]); !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('startdate','عن المدة من  :') !!}
                            <div class="input-group">
                            <span class="input-group-addon">
                                   <i class="fa fa-calendar"></i>
                             </span>
                            {!! Form::text('startdate', null, ['class' => 'form-control date-picker', 'required', 'placeholder' =>'بداية المدة' ]); !!}
                        </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('enddate','إلي  :') !!}
                            <div class="input-group">
                            <span class="input-group-addon">
                                   <i class="fa fa-calendar"></i>
                             </span>
                            {!! Form::text('enddate', null, ['class' => 'form-control date-picker', 'required', 'placeholder' =>'نهاية المدة' ]); !!}
                        </div>
                        </div>


            <div class="form-group">
                {!! Form::label('notes','ملاحظات : ') !!}
                {!! Form::text('notes', null, ['class' => 'form-control' ]); !!}
            </div>





            </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
    $('.date-picker').datepicker({
        autoclose: true,
        format:'yyyy-m-d',
    });


    $("#profite").on('keyup',function () {
        var total=$(this).val();
        var number=$('#sharenumber').val();
        var sharval=(total/number).toFixed(2);
        $('#shareval').val(sharval);

        $('.share').each(function (index,item) {
            var id = $(this).attr('id');
            var remval=$(this).val()*sharval -$('#value_'+id).val();
            $('#rem_'+id).val(remval.toFixed(2));

        });


    });

</script>